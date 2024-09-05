<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Permissions\Security\Limitation\Mapper;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class FieldGroupLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface, TranslationContainerInterface
{
    public const MESSAGE_DOMAIN = 'ibexa_content_forms_policies';

    /** @var \Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $fieldsGroupsList;

    public function __construct(
        FieldsGroupsList $fieldsGroupsList
    ) {
        $this->fieldsGroupsList = $fieldsGroupsList;
    }

    protected function getSelectionChoices()
    {
        return $this->fieldsGroupsList->getGroups();
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        return $limitation->limitationValues;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(LimitationIdentifierToLabelConverter::convert('fieldgroup'), self::MESSAGE_DOMAIN))->setDesc('Field Group'),
        ];
    }
}

class_alias(FieldGroupLimitationMapper::class, 'Ibexa\Platform\Permissions\Security\Limitation\Mapper\FieldGroupLimitationMapper');
