<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Scheduler\Command;

use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Scheduler\Repository\DateBasedEntriesListInterface;
use Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Scheduler\Notification\SenderInterface as NotificationSenderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScheduledRunCommand extends Command implements BackwardCompatibleCommand
{
    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $publisherService;

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface */
    private $hideService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Scheduler\Notification\SenderInterface */
    private $notificationSender;

    public function __construct(
        DateBasedPublishServiceInterface $publisherService,
        DateBasedHideServiceInterface $hideService,
        ContentService $contentService,
        PermissionResolver $permissionResolver,
        Repository $repository,
        NotificationSenderInterface $sender
    ) {
        $this->publisherService = $publisherService;
        $this->hideService = $hideService;
        $this->contentService = $contentService;
        $this->permissionResolver = $permissionResolver;
        $this->repository = $repository;
        $this->notificationSender = $sender;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('ibexa:scheduled:run');
        $this->setAliases($this->getDeprecatedAliases());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->executeSchedulePublish($output);
        $this->executeScheduleHide($output);

        return 0;
    }

    private function executeSchedulePublish(OutputInterface $output): void
    {
        $output->writeln('Starting publishing scheduled content');

        $scheduledEntries = $this->getScheduledEntriesToProcess(
            $this->publisherService
        );

        $totalCount = $this->publisherService->countScheduledEntries();
        $toPublish = $this->publisherService->countScheduledEntriesToProcess();

        $output->writeln(sprintf('<info>Total scheduled for publication: %s</info>', $totalCount));
        $output->writeln(sprintf('<info>Ready for publication: %s</info>', $toPublish));

        if ($toPublish === 0) {
            $output->writeln('Nothing to do here...');

            return;
        }

        $output->writeln('Processing:');
        $contentService = $this->contentService;

        foreach ($scheduledEntries as $scheduledEntry) {
            $user = $scheduledEntry->user;
            $isUserNotNull = $user !== null;

            if ($isUserNotNull) {
                $this->permissionResolver->setCurrentUserReference($user);
            }

            $contentId = $scheduledEntry->versionInfo->contentInfo->id;
            $versionNumber = $scheduledEntry->versionInfo->versionNo;

            // load actual data
            if ($isUserNotNull) {
                $contentInfo = $contentService->loadContentInfo($contentId);
                $versionInfo = $contentService->loadVersionInfo($contentInfo, $versionNumber);
            } else {
                [$contentInfo, $versionInfo] = $this->permissionResolver->sudo(
                    static function () use ($contentService, $contentId, $versionNumber): array {
                        $contentInfo = $contentService->loadContentInfo($contentId);
                        $versionInfo = $contentService->loadVersionInfo($contentInfo, $versionNumber);

                        return [$contentInfo, $versionInfo];
                    },
                    $this->repository
                );
            }

            try {
                $this->writeProcessEntryLog($output, $contentInfo, $scheduledEntry);

                if ($isUserNotNull) {
                    $contentService->publishVersion($versionInfo, [$versionInfo->initialLanguageCode]);
                    $this->notificationSender->sendPublishNotifications($scheduledEntry, $contentInfo);
                } else {
                    $this->permissionResolver->sudo(
                        static function () use ($contentService, $versionInfo): void {
                            $contentService->publishVersion($versionInfo, [$versionInfo->initialLanguageCode]);
                        },
                        $this->repository
                    );
                }

                $output->writeln('<info>Done</info>');
            } catch (BadStateException $exception) {
                if ($scheduledEntry->versionInfo->status === VersionInfo::STATUS_PUBLISHED) {
                    $output->writeln('Already published.');
                }
            }

            $this->publisherService->unschedulePublish($scheduledEntry->versionInfo->id);
        }
    }

    private function executeScheduleHide(OutputInterface $output): void
    {
        $output->writeln('Starting hide scheduled content');

        $entriesToHide = $this->getScheduledEntriesToProcess(
            $this->hideService
        );

        $totalCount = $this->hideService->countScheduledEntries();
        $toHide = $this->hideService->countScheduledEntriesToProcess();

        $output->writeln(sprintf('<info>Total scheduled for hide: %s</info>', $totalCount));
        $output->writeln(sprintf('<info>Ready for hide: %s</info>', $toHide));

        if ($toHide === 0) {
            $output->writeln('Nothing to do here...');

            return;
        }

        $output->writeln('Processing:');
        $contentService = $this->contentService;

        foreach ($entriesToHide as $scheduledEntry) {
            $user = $scheduledEntry->user;
            $isUserNotNull = $user !== null;

            if ($isUserNotNull) {
                $this->permissionResolver->setCurrentUserReference($user);
            }

            $contentId = $scheduledEntry->content->id;

            // load actual data
            $contentInfo = $this->contentService->loadContentInfo($contentId);

            $this->writeProcessEntryLog($output, $contentInfo, $scheduledEntry);

            if ($isUserNotNull) {
                $contentService->hideContent($contentInfo);
                $this->notificationSender->sendHideNotifications($scheduledEntry, $contentInfo);
            } else {
                $this->permissionResolver->sudo(
                    static function () use ($contentService, $contentInfo): void {
                        $contentService->hideContent($contentInfo);
                    },
                    $this->repository
                );
            }

            $output->writeln('<info>Done</info>');

            $this->hideService->unscheduleHide($scheduledEntry->content->id);
        }
    }

    /**
     * @param \Ibexa\Contracts\Scheduler\Repository\DateBasedEntriesListInterface $dateBasedService
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    private function getScheduledEntriesToProcess(DateBasedEntriesListInterface $dateBasedService): iterable
    {
        return $this->permissionResolver->sudo(
            static function () use ($dateBasedService): iterable {
                return $dateBasedService->getScheduledEntriesToProcess();
            },
            $this->repository
        );
    }

    private function writeProcessEntryLog(
        OutputInterface $output,
        ContentInfo $contentInfo,
        ScheduledEntry $scheduledEntry
    ): void {
        $output->write(sprintf(
            ' - "%s" [%s] (id: %d, version: %d)... ',
            $contentInfo->name,
            $scheduledEntry->date->format('c'),
            $contentInfo->id,
            $scheduledEntry->versionInfo->versionNo ?? '-'
        ));
    }

    /**
     * @return string[]
     */
    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:scheduled:run', 'ezstudio:scheduled:publish'];
    }
}

class_alias(ScheduledRunCommand::class, 'EzSystems\DateBasedPublisherBundle\Command\ScheduledRunCommand');
