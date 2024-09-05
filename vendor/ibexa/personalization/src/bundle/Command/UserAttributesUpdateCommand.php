<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Command;

use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
use Ibexa\Personalization\Event\UpdateUserAPIEvent;
use Ibexa\Personalization\Service\User\UserServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

final class UserAttributesUpdateCommand extends Command implements BackwardCompatibleCommand
{
    protected static $defaultName = 'ibexa:personalization:update-user';

    private const DEPRECATED_ALIASES = ['ibexa:recommendation:update-user'];

    private EventDispatcherInterface $eventDispatcher;

    private UserServiceInterface $userService;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserServiceInterface $userService
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->userService = $userService;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setAliases(self::DEPRECATED_ALIASES)
            ->setDescription('Update the set of the user attributes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = new UpdateUserAPIEvent();
        $this->eventDispatcher->dispatch($event);

        $request = $event->getUserAPIRequest();

        $io = new SymfonyStyle($input, $output);

        $io->info('Updating user attributes');

        if (null === $request) {
            $io->warning('Request object is empty');

            return self::INVALID;
        } elseif (empty($request->source)) {
            $io->warning('Property source is not defined');

            return self::INVALID;
        } elseif (empty($request->xmlBody)) {
            $io->warning('Property xmlBody is not defined');

            return self::INVALID;
        }

        $statusCode = $this->userService->updateUser($request);
        if ($statusCode !== Response::HTTP_OK) {
            $io->error('Failed to update user attributes!');

            return self::FAILURE;
        }

        $io->success('User attributes updated successfully!');

        return self::SUCCESS;
    }

    /**
     * @return string[]
     */
    public function getDeprecatedAliases(): array
    {
        return self::DEPRECATED_ALIASES;
    }
}

class_alias(UserAttributesUpdateCommand::class, 'EzSystems\EzRecommendationClientBundle\Command\UserAttributesUpdateCommand');
