<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Exception;

use Ibexa\Migration\ValueObject\Step\Action;
use InvalidArgumentException;
use Throwable;

class InvalidActionException extends InvalidArgumentException
{
    /**
     * @param string[] $supportedActions
     */
    public function __construct(Action $action, array $supportedActions, int $code = 0, Throwable $previous = null)
    {
        $message = self::prepareErrorMessage($action, $supportedActions);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string[] $supportedActions
     */
    private static function prepareErrorMessage(Action $action, array $supportedActions): string
    {
        return sprintf(
            'Unknown action: %s. Supported: [%s]',
            $action->getSupportedType(),
            implode('|', $supportedActions)
        );
    }
}

class_alias(InvalidActionException::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\Exception\InvalidActionException');
