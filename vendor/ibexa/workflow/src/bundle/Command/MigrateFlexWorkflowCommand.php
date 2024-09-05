<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Exception;
use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
use Ibexa\Core\Base\Exceptions\DatabaseException;
use Ibexa\Core\Persistence\Legacy\Content\Gateway\DoctrineDatabase as ContentGateway;
use Ibexa\Core\Persistence\Legacy\Notification\Gateway\DoctrineDatabase as NotificationGateway;
use Ibexa\Workflow\Persistence\Gateway\DoctrineGateway as WorkflowGateway;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrateFlexWorkflowCommand extends Command implements BackwardCompatibleCommand
{
    private const DEFAULT_ITERATION_COUNT = 1000;
    private const CONFIRMATION_ANSWER = 'yes';

    public const SUCCESS = 0;
    private const CANCELED = 1;
    private const ERRORS_FOUND = 2;

    private const WORKFLOW_NAME = 'quick_review';
    private const TRANSITION_NAME = 'to_review';
    private const STAGE_NAME = 'review';

    private const FLEXWORKFLOW_TABLE = 'ezflexworkflow';
    private const FLEXWORKFLOW_TABLE_MESSAGE = 'ezflexworkflow_message';
    private const FLEXWORKFLOW_NOTIFICATION_TYPE = 'FlexWorkflow:Review';

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /** @var \Ibexa\Workflow\Persistence\Gateway\DoctrineGateway */
    private $gateway;

    public function __construct(
        Connection $connection,
        WorkflowGateway $gateway
    ) {
        $this->connection = $connection;
        $this->gateway = $gateway;

        parent::__construct('ibexa:migrate:flex-workflow');
    }

    private function getDatabasePlatform(): AbstractPlatform
    {
        try {
            return $this->connection->getDatabasePlatform();
        } catch (DBALException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    protected function configure()
    {
        $this
            ->setAliases(['ezplatform:migrate:flex-workflow'])
            ->addOption(
                'iteration-count',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Number of  values fetched into memory and processed at once',
                self::DEFAULT_ITERATION_COUNT
            )->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Prevents confirmation dialog. Please use it carefully.'
            );
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('force') !== true) {
            $io->caution('Read carefully. This operation is irreversible. Make sure you are using correct database and have backup.');
            $answer = $io->ask('Are you sure you want to start migration? (type "' . self::CONFIRMATION_ANSWER . '" to confirm)');

            if ($answer !== self::CONFIRMATION_ANSWER) {
                $io->comment('Canceled.');

                return self::CANCELED;
            }
        }

        $iterationCount = (int)$input->getOption('iteration-count');

        /** @var \Symfony\Component\Console\Output\ConsoleSectionOutput $errors */
        $totalCount = $this->countFlexWorkflowContentItems();
        $notificationsRemoved = $this->removeFlexWorkflowNotifications();

        $io->comment($notificationsRemoved . ' obsolete notification(s) removed.');

        if (empty($totalCount)) {
            $io->comment('No workflows to migrate.');

            return self::SUCCESS;
        }

        $io->comment('Migrating Flex Workflow to Quick Review.');

        $progressBar = new ProgressBar($output, $totalCount, 0);
        $progressBar->setFormat('debug_nomax');
        $progressBar->start();

        $errorsFound = false;
        for ($offset = 0; $offset <= $totalCount; $offset += $iterationCount) {
            foreach ($this->getFlexWorkflowContentItems($offset, $iterationCount) as $flexWorkflowContent) {
                $contentId = (int)$flexWorkflowContent['content_id'];
                $version = (int)$flexWorkflowContent['version'];

                $this->connection->beginTransaction();

                try {
                    $this->migrateContentFlexWorkflow($contentId, $version);

                    $this->connection->commit();

                    $progressBar->advance();
                } catch (Exception $e) {
                    $io->error(
                        sprintf('Content ID: %d, Version: %d', $contentId, $version)
                    );

                    $errorsFound = true;

                    $this->connection->rollBack();
                }
            }
        }

        $progressBar->finish();

        if ($errorsFound) {
            $io->caution('Errors found.');

            return self::ERRORS_FOUND;
        }

        $io->success('Errors found.');

        return self::SUCCESS;
    }

    private function migrateContentFlexWorkflow(int $contentId, int $version): void
    {
        $workflowStored = false;
        $workflowId = 0;

        foreach ($this->getFlexWorkflowMessages($contentId, $version) as $flexWorkflowMessage) {
            if (!$workflowStored) {
                $workflowId = $this->gateway->insertWorkflow(
                    $contentId,
                    $version,
                    self::WORKFLOW_NAME,
                    (int)$flexWorkflowMessage['sender_id'],
                    (int)$flexWorkflowMessage['created']
                );

                $workflowStored = true;
            }

            if (empty($workflowId)) {
                throw new RuntimeException('Error during Workflow migration: $workflowId is empty for unknown reason.');
            }

            $message = stream_get_contents($flexWorkflowMessage['message']);
            $receiverId = $flexWorkflowMessage['receiver_id'];

            $this->gateway->insertTransitionMetadata(
                $workflowId,
                self::TRANSITION_NAME,
                (int)$flexWorkflowMessage['sender_id'],
                (int)$flexWorkflowMessage['created'],
                $message
            );
        }

        if ($workflowStored) {
            $this->gateway->insertMarking(
                $workflowId,
                self::STAGE_NAME,
                $message ?? '',
                $receiverId ?? null
            );
        }
    }

    private function countFlexWorkflowContentItems(): int
    {
        $query = $this->connection->createQueryBuilder();
        $expr = $query->expr();
        $query
            ->select($this->getDatabasePlatform()->getCountExpression('fw.id'))
            ->from(self::FLEXWORKFLOW_TABLE, 'fw')
            ->innerJoin(
                'fw',
                ContentGateway::CONTENT_ITEM_TABLE,
                'c',
                $expr->eq('fw.content_id', 'c.id'),
            )
            ->leftJoin(
                'fw',
                WorkflowGateway::TABLE_WORKFLOWS,
                'ew',
                $expr->andX(
                    $expr->eq('fw.content_id', 'ew.content_id'),
                    $expr->eq('fw.version', 'ew.version_no'),
                    $expr->eq('ew.workflow_name', ':workflow_name')
                )
            )
            ->groupBy(['fw.content_id', 'fw.version'])
            ->where($expr->isNull('ew.id'))
            ->setParameter(':workflow_name', self::WORKFLOW_NAME, ParameterType::STRING);

        $statement = $query->execute();

        return (int)$statement->fetchColumn();
    }

    private function getFlexWorkflowContentItems(
        int $offset = 0,
        int $limit = self::DEFAULT_ITERATION_COUNT
    ): iterable {
        $query = $this->connection->createQueryBuilder();
        $expr = $query->expr();
        $query
            ->select([
                'fw.content_id',
                'fw.version',
            ])
            ->from(self::FLEXWORKFLOW_TABLE, 'fw')
            ->innerJoin(
                'fw',
                ContentGateway::CONTENT_ITEM_TABLE,
                'c',
                $expr->eq('fw.content_id', 'c.id'),
            )
            ->leftJoin(
                'fw',
                WorkflowGateway::TABLE_WORKFLOWS,
                'ew',
                $expr->andX(
                    $expr->eq('fw.content_id', 'ew.content_id'),
                    $expr->eq('fw.version', 'ew.version_no'),
                    $expr->eq('ew.workflow_name', ':workflow_name')
                )
            )
            ->groupBy(['fw.content_id', 'fw.version'])
            ->where($expr->isNull('ew.id'))
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter(':workflow_name', self::WORKFLOW_NAME, ParameterType::STRING);

        $statement = $query->execute();

        while ($record = $statement->fetch(FetchMode::ASSOCIATIVE)) {
            yield $record;
        }
    }

    private function getFlexWorkflowMessages(int $contentId, int $version): iterable
    {
        $query = $this->connection->createQueryBuilder();
        $expr = $query->expr();
        $query
            ->select([
                'fwm.id',
                'fwm.content_id',
                'fwm.message',
                'fwm.sender_id',
                'fwm.receiver_id',
                'fwm.created',
            ])
            ->from(self::FLEXWORKFLOW_TABLE, 'fw')
            ->innerJoin(
                'fw',
                self::FLEXWORKFLOW_TABLE_MESSAGE,
                'fwm',
                $expr->andX(
                    $expr->eq('fw.message_id', 'fwm.id'),
                )
            )
            ->where($expr->eq('fw.content_id', ':content_id'))
            ->orderBy('fw.created')
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER)
            ->setParameter(':version', $version, ParameterType::INTEGER);

        $statement = $query->execute();

        while ($record = $statement->fetch(FetchMode::ASSOCIATIVE)) {
            yield $record;
        }
    }

    private function removeFlexWorkflowNotifications(): int
    {
        $query = $this->connection->createQueryBuilder();
        $expr = $query->expr();
        $query
            ->delete(NotificationGateway::TABLE_NOTIFICATION)
            ->where($expr->eq('type', ':type'))
            ->setParameter(':type', self::FLEXWORKFLOW_NOTIFICATION_TYPE);

        return $query->execute();
    }

    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:migrate:flex-workflow'];
    }
}

class_alias(MigrateFlexWorkflowCommand::class, 'EzSystems\EzPlatformWorkflowBundle\Command\MigrateFlexWorkflowCommand');
