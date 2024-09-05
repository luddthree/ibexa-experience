<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Command;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PurgeFormSubmissionsCommand extends Command
{
    private const DEFAULT_REPOSITORY_USER = 'admin';
    private const DEFAULT_ITERATION_COUNT = 50;
    private const BEFORE_RUNNING_HINTS = <<<EOT
        <error>Before you continue:</error>
        - Run this command without memory limit.
        - Run this command in production environment using <info>--env=prod</info>
    EOT;

    protected static $defaultName = 'ibexa:form-builder:purge-form-submissions';

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface */
    private $formSubmissionService;

    public function __construct(
        Repository $repository,
        ContentService $contentService,
        UserService $userService,
        PermissionResolver $permissionResolver,
        FormSubmissionServiceInterface $formSubmissionService
    ) {
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->formSubmissionService = $formSubmissionService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'form-id',
            InputArgument::REQUIRED,
            'Content ID of form'
        );

        $this->addOption(
            'language-code',
            'l',
            InputOption::VALUE_REQUIRED,
            'Language code e.g. eng-GB',
            null
        );

        $this->addOption(
            'user',
            'u',
            InputOption::VALUE_REQUIRED,
            'Repository user login',
            self::DEFAULT_REPOSITORY_USER
        );

        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Prevents confirmation dialog when used with --no-interaction. Please use it carefully.'
        );

        $this->addOption(
            'batch-size',
            'c',
            InputOption::VALUE_REQUIRED,
            'Number of form submissions to delete in a single iteration.',
            self::DEFAULT_ITERATION_COUNT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );

        $form = $this->getForm($input);
        $languageCode = $input->getOption('language-code');
        $batchSize = $this->getBatchSize($input);

        $formSubmissionsCount = $this->getFormSubmissionsCount($form, $languageCode);
        if (!$this->confirmPurge($input, $output, $formSubmissionsCount)) {
            return self::FAILURE;
        }

        $this->repository->beginTransaction();
        try {
            $this->purgeFormSubmissions($form, $languageCode, $batchSize);

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Form submissions has been purged successfully.');

        return self::SUCCESS;
    }

    private function confirmPurge(InputInterface $input, OutputInterface $output, int $formSubmissionsCount): bool
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('force')) {
            return true;
        }

        if (!$input->getOption('no-interaction')) {
            $question = sprintf(
                "<info>Found %d form submissions.</info>\n\n%s\n\n<info>Do you want to proceed?</info>",
                $formSubmissionsCount,
                self::BEFORE_RUNNING_HINTS
            );

            return $io->confirm($question, false);
        }

        return false;
    }

    private function getFormSubmissionsCount(ContentInfo $form, ?string $languageCode): int
    {
        return $this->formSubmissionService->loadByContent($form, $languageCode, 0, 0)->getTotalCount();
    }

    private function purgeFormSubmissions(ContentInfo $form, ?string $languageCode, int $limit): void
    {
        do {
            $submissions = $this->formSubmissionService->loadByContent($form, $languageCode, 0, $limit);
            foreach ($submissions as $submission) {
                $this->formSubmissionService->delete($submission);
            }
        } while ($submissions->getTotalCount() > 0);
    }

    private function getForm(InputInterface $input): ContentInfo
    {
        return $this->contentService->loadContentInfo(
            (int)$input->getArgument('form-id')
        );
    }

    private function getBatchSize(InputInterface $input): int
    {
        $limit = $input->getOption('batch-size');

        if (!ctype_digit($limit) || (int)$limit < 1) {
            throw new RuntimeException("'--batch-size' should be > 0, you passed '{$limit}'");
        }

        return (int)$limit;
    }
}

class_alias(PurgeFormSubmissionsCommand::class, 'EzSystems\EzPlatformFormBuilderBundle\Command\PurgeFormSubmissionsCommand');
