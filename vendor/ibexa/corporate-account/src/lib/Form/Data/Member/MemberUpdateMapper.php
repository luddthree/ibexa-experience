<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Member;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberUpdateMapper
{
    /**
     * @param array<string, mixed> $params
     */
    public function mapToFormData(Member $member, ContentType $contentType, array $params = []): MemberUpdateData
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        $data = new MemberUpdateData([
            'user' => $member->getUser(),
            'enabled' => $member->getUser()->enabled,
            'contentType' => $contentType,
        ]);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field[] $fields */
        $fields = $member->getFieldsByLanguage($params['languageCode']);
        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $field = $fields[$fieldDef->identifier];
            if ($fieldDef->fieldTypeIdentifier !== 'ezuser') {
                $data->addFieldData(new FieldData([
                    'fieldDefinition' => $fieldDef,
                    'field' => $field,
                    'value' => $field->value,
                ]));
            }
        }

        $data->setMember($member);
        $data->setRole($member->getRole());

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setRequired(['languageCode']);
    }
}
