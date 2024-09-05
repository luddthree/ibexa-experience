<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\FieldType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FieldGroupsListDecorator implements FieldsGroupsList, TranslationContainerInterface
{
    private const CORPORATE_FIELD_GROUPS = [
        'basic_information',
        'billing_address',
        'internal',
        'company',
        'member',
    ];

    private FieldsGroupsList $innerService;

    private TranslatorInterface $translator;

    public function __construct(
        FieldsGroupsList $innerService,
        TranslatorInterface $translator
    ) {
        $this->innerService = $innerService;
        $this->translator = $translator;
    }

    /**
     * @return array<string, string>
     */
    public function getGroups(): array
    {
        $translatedGroups = $this->innerService->getGroups();

        foreach (self::CORPORATE_FIELD_GROUPS as $groupIdentifier) {
            $translatedGroups[$groupIdentifier] ??= $this->translator->trans(
                /** @Ignore */
                $groupIdentifier,
                [],
                'ibexa_fields_groups'
            );
        }

        return $translatedGroups;
    }

    public function getDefaultGroup(): string
    {
        return $this->innerService->getDefaultGroup();
    }

    public function getFieldGroup(FieldDefinition $fieldDefinition): string
    {
        return $this->innerService->getFieldGroup($fieldDefinition);
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            Message::create('basic_information', 'ibexa_fields_groups')->setDesc('Basic information'),
            Message::create('billing_address', 'ibexa_fields_groups')->setDesc('Billing address'),
            Message::create('internal', 'ibexa_fields_groups')->setDesc('Internal'),
            Message::create('company', 'ibexa_fields_groups')->setDesc('Company'),
            Message::create('member', 'ibexa_fields_groups')->setDesc('Member'),
        ];
    }
}
