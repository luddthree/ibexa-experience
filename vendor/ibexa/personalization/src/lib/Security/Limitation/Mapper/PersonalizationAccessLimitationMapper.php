<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Security\Limitation\Mapper;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class PersonalizationAccessLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface, TranslationContainerInterface
{
    private PersonalizationLimitationListLoaderInterface $accessListLoader;

    public function __construct(PersonalizationLimitationListLoaderInterface $accessListLoader)
    {
        $this->accessListLoader = $accessListLoader;
    }

    protected function getSelectionChoices(): array
    {
        return $this->accessListLoader->getList();
    }

    public function mapLimitationValue(Limitation $limitation): array
    {
        $values = [];

        foreach ($this->accessListLoader->getList() as $customerId => $name) {
            if (in_array((string)$customerId, $limitation->limitationValues, true)) {
                $values[] = $name;
            }
        }

        return $values;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                LimitationIdentifierToLabelConverter::convert('personalizationaccess'),
                'ibexa_content_forms_policies'
            )->setDesc('Personalization Access'),
        ];
    }
}

class_alias(PersonalizationAccessLimitationMapper::class, 'Ibexa\Platform\Personalization\Security\Limitation\Mapper\PersonalizationAccessLimitationMapper');
