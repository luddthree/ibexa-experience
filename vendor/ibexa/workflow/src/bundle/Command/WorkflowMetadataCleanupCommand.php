<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Command;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class WorkflowMetadataCleanupCommand extends Command
{
    protected static $defaultName = 'ibexa:workflow:cleanup';

    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $handler;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    public function __construct(
        HandlerInterface $handler,
        TransactionHandler $transactionHandler
    ) {
        $this->handler = $handler;
        $this->transactionHandler = $transactionHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Cleanups workflow metadata tables from orphaned records.')
            ->setHelp(
                <<<'EOT'
The command <info>%command.name%</info> removes orphaned records from
workflow metadata tables.

<warning>Before you proceed with an actual update, create a backup and perform a dry run.</warning>

<warning>Do not run the <info>%command.name%</info> command more than ONCE.</warning>

Since this script can potentially run for a very long time, to avoid memory
exhaustion run it in production environment using <info>--env=prod</info> switch.
EOT
            );
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Workflow Metadata cleanup');

        $io->writeln('Checking the number of affected Content. Please wait.');
        $affectedContentCount = $this->handler->countContentWithOrphanedWorkflowMetadata();
        $io->writeln(sprintf('Found: %d', $affectedContentCount));

        if ($affectedContentCount === 0) {
            $io->success('Nothing to do');

            return Command::SUCCESS;
        }

        if ($io->confirm('Do you wish to continue?') === false) {
            return Command::SUCCESS;
        }

        $io->writeln('Preparing list of affected content. Please wait.');
        $affectedContentIds = $this->handler->loadContentIdsWithOrphanedWorkflowMetadata();
        $io->progressStart($affectedContentCount);

        $this->transactionHandler->beginTransaction();
        try {
            foreach ($affectedContentIds as $affectedContentId) {
                $this->handler->cleanupWorkflowMetadataForContent($affectedContentId);
                $io->progressAdvance();
            }
            $this->transactionHandler->commit();
        } catch (\Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }

        $io->progressFinish();
        $io->success('Done!');

        return Command::SUCCESS;
    }
}
