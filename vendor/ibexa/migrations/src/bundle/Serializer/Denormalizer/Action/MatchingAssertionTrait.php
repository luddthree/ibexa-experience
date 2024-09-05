<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Action;

use Symfony\Component\Serializer\Exception\UnexpectedValueException;

trait MatchingAssertionTrait
{
    /**
     * @param string[] $matchingMethods
     * @param string[] $availableMatchingMethods
     */
    private function throwIfNoMatchingMethod(
        string $actionType,
        array $matchingMethods,
        array $availableMatchingMethods
    ): void {
        if (empty($matchingMethods)) {
            throw new UnexpectedValueException(sprintf(
                'Unable to denormalize "%s" action: missing matching property (one of "%s")',
                $actionType,
                implode('", "', $availableMatchingMethods),
            ));
        }
    }

    /**
     * @param string[] $matchingMethods
     */
    private function throwIfMoreThanOneMatchingMethod(
        string $actionType,
        array $matchingMethods
    ): void {
        if (count($matchingMethods) > 1) {
            throw new UnexpectedValueException(sprintf(
                'Unable to denormalize "%s" action: more than one matching property provided (received: "%s")',
                $actionType,
                implode('", "', $matchingMethods),
            ));
        }
    }
}

class_alias(MatchingAssertionTrait::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Action\MatchingAssertionTrait');
