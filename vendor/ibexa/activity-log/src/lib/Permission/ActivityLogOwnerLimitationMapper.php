<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Permission;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ActivityLogOwnerLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface, TranslationContainerInterface
{
    /**
     * @return array<string, mixed>
     */
    protected function getChoiceFieldOptions(): array
    {
        return parent::getChoiceFieldOptions() + [
            'translation_domain' => 'ibexa_content_forms_policies',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getSelectionChoices(): array
    {
        return [
            1 => 'activity_log.limitation.self',
        ];
    }

    /**
     * @return array<string>
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        return [
            'activity_log.limitation.self',
        ];
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                LimitationIdentifierToLabelConverter::convert('owner'),
                'ibexa_content_forms_policies'
            )->setDesc('Owner'),
            Message::create(
                'activity_log.limitation.self',
                'ibexa_content_forms_policies',
            )->setDesc('Only own logs'),
        ];
    }
}
