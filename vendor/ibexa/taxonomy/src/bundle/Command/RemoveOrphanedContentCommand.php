<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Command;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Persistence\Gateway\ContentGatewayInterface;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RemoveOrphanedContentCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = 'ibexa:taxonomy:remove-orphaned-content';

    protected static $defaultDescription = 'Find and remove orphaned Content items that may '
    . 'have been left after removing Taxonomy Entries';

    private TaxonomyConfiguration $taxonomyConfiguration;

    private Repository $repository;

    private ContentGatewayInterface $taxonomyContentGateway;

    private ContentService $contentService;

    private ContentTypeService $contentTypeService;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        Repository $repository,
        ContentGatewayInterface $taxonomyContentGateway,
        ContentService $contentService,
        ContentTypeService $contentTypeService
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->repository = $repository;
        $this->taxonomyContentGateway = $taxonomyContentGateway;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'taxonomy',
            InputArgument::REQUIRED,
            'Taxonomy to clean up orphaned Content for',
        );
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Set this parameter to execute this action',
        );
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Do not remove anything, only output affected Content items',
        );
        $this->addOption(
            'batch-size',
            'b',
            InputOption::VALUE_REQUIRED,
            'Number of Content Items to process at once. '
            . 'Lower the number if you are hitting memory limit or the database can\'t keep up',
            100,
        );

        $this->setHelp(
            <<<EOT
The <info>%command.name%</info> command finds and/or removes all Content items
associated with taxonomy that have no matching Taxonomy Entry. This Content items are 
in unusable state and should be removed.

This operation should be performed on every taxonomy separately using <info><taxonomy></info> argument:

    <info>php %command.full_name% <taxonomy></info>
    
By using <info>--dry-run</info> parameter you can list affected Content items.
    
The <info>--force</info> parameter has to be used to actually persist changes in the database.

<error>Be careful: Content data in a database will be lost when executing this command.</error>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $force = $input->getOption('force');
        $dryRun = $input->getOption('dry-run');
        $batchSize = (int) $input->getOption('batch-size');
        $taxonomy = $input->getArgument('taxonomy');
        $taxonomies = $this->taxonomyConfiguration->getTaxonomies();

        if (!$dryRun && !$force) {
            $io->error(
                'Parameter --force not used. If you want to check what '
                . 'Content items need to be removed, use --dry-run parameter.'
            );

            return self::FAILURE;
        }

        if ($dryRun) {
            $io->info('Using --dry-run option. The command will only list orphan content items.');
        }

        if (!in_array($taxonomy, $taxonomies, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Argument 'taxonomy' is invalid. Did you mean one of these: %s?",
                    implode(', ', $taxonomies),
                )
            );
        }

        $contentTypeIdentifier = $this->taxonomyConfiguration->getConfigForTaxonomy($taxonomy, 'content_type');
        $contentType = $this->repository->sudo(
            function () use ($contentTypeIdentifier): ContentType {
                return $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
            }
        );

        $orphanContentIds = $this->taxonomyContentGateway->findOrphanContentIds($contentType->id);
        $totalCount = count($orphanContentIds);

        if ($totalCount === 0) {
            $io->success('There are no orphan content items. Exiting.');

            return Command::SUCCESS;
        }

        if ($totalCount < $batchSize) {
            $batchSize = $totalCount;
        }

        $batchCount = ceil($totalCount / $batchSize);

        $io->writeln([
            "Total count: {$totalCount}",
            "Batch size: {$batchSize}",
            "Operation will be performed in: {$batchCount} batches",
        ]);

        $io->info(
            'You may see errors on loading Content items in the log. '
            . 'This is normal and does not affect this operation.'
        );

        $batchNumber = 1;

        $this->repository->beginTransaction();
        try {
            for ($offset = 0; $offset < $totalCount; $offset += $batchSize) {
                $io->writeln("Processing batch #{$batchNumber}");
                $this->processBatch($io, $orphanContentIds, $offset, $batchSize, $dryRun);

                ++$batchNumber;
            }

            if (!$dryRun) {
                $this->repository->commit();
            } else {
                $this->repository->rollBack();
            }
        } catch (\Exception $e) {
            $this->repository->rollBack();

            throw $e;
        }

        if ($dryRun) {
            $io->success(
                "Found {$totalCount} orphan Content items. "
                . 'To remove them rerun this command without --dry-run and with --force option.'
            );
        } else {
            $io->success("Successfully removed {$totalCount} Content items.");
        }

        return self::SUCCESS;
    }

    /**
     * @param array<int> $orphanContentIds
     */
    private function processBatch(
        SymfonyStyle $io,
        array $orphanContentIds,
        int $offset,
        int $batchSize,
        bool $dryRun
    ): void {
        $orphanContentIdsChunk = array_slice($orphanContentIds, $offset, $batchSize);
        $contentItems = $this->repository->sudo(
            /** @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo> */
            function () use ($orphanContentIdsChunk): iterable {
                return $this->contentService->loadContentInfoList($orphanContentIdsChunk);
            }
        );

        foreach ($contentItems as $contentInfo) {
            if ($dryRun) {
                $io->writeln(sprintf(
                    "Found orphan Content '%s' (Content ID: %d)",
                    $contentInfo->name,
                    $contentInfo->id,
                ));

                continue;
            }

            $io->writeln(sprintf(
                "Removing content '%s' (Content ID: %d)",
                $contentInfo->name,
                $contentInfo->id,
            ));

            $this->repository->sudo(
                function () use ($contentInfo): void {
                    $this->contentService->deleteContent($contentInfo);
                }
            );
        }
    }
}
