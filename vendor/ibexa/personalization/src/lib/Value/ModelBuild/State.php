<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\ModelBuild;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class State implements TranslationContainerInterface
{
    public const NEW = 'NEW';
    public const TRIGGERED = 'TRIGGERED';
    public const QUEUED = 'QUEUED';
    public const RUNNING = 'RUNNING';
    public const FINISHED = 'FINISHED';
    public const TERMINATED = 'TERMINATED';
    public const FAILED = 'FAILED';
    public const LOST = 'LOST';

    public const BUILD_STATE_ACTIVE = 'model_build.state.active';
    public const BUILD_STATE_FAILED = 'model_build.state.failed';
    public const BUILD_STATE_BUILD_IN_PROGRESS = 'model_build.state.build_in_progress';

    public const BUILD_STATES = [
        self::NEW => self::BUILD_STATE_BUILD_IN_PROGRESS,
        self::TRIGGERED => self::BUILD_STATE_BUILD_IN_PROGRESS,
        self::QUEUED => self::BUILD_STATE_BUILD_IN_PROGRESS,
        self::RUNNING => self::BUILD_STATE_BUILD_IN_PROGRESS,
        self::FINISHED => self::BUILD_STATE_ACTIVE,
        self::TERMINATED => self::BUILD_STATE_FAILED,
        self::FAILED => self::BUILD_STATE_FAILED,
        self::LOST => self::BUILD_STATE_FAILED,
    ];

    public const BUILD_IN_PROGRESS_STATES = [
        self::NEW,
        self::TRIGGERED,
        self::QUEUED,
        self::RUNNING,
    ];

    public const STATES_COLORS_CLASSES = [
        State::BUILD_STATE_ACTIVE => 'success',
        State::BUILD_STATE_BUILD_IN_PROGRESS => 'secondary',
        State::BUILD_STATE_FAILED => 'danger',
    ];

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::BUILD_STATE_ACTIVE, 'ibexa_personalization'))->setDesc('Active'),
            (new Message(self::BUILD_STATE_FAILED, 'ibexa_personalization'))->setDesc('Failed'),
            (new Message(self::BUILD_STATE_BUILD_IN_PROGRESS, 'ibexa_personalization'))->setDesc('Build in progress'),
        ];
    }
}
