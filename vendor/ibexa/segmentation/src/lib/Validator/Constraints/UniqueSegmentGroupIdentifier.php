<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class UniqueSegmentGroupIdentifier extends Constraint implements TranslationContainerInterface
{
    private const MESSAGE = 'ibexa.segmentation.validator.segment_group_unique_identifier';

    public string $message = self::MESSAGE;

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::MESSAGE, 'validators')
                ->setDesc('Segment Group identifier "{{ identifier }}" is not unique'),
        ];
    }
}
