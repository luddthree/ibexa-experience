<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContentContainerType extends Constraint implements TranslationContainerInterface
{
    public $message = 'ezplatform.content.is_not_expected_content_container';

    public $types;

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ezplatform.content.is_not_expected_content_container', 'validators')
                ->setDesc('The selected Content item isn\'t a container.'),
        ];
    }
}

class_alias(ContentContainerType::class, 'EzSystems\EzPlatformPageFieldType\Validator\Constraints\ContentContainerType');
