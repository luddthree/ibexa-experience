<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\User\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserLogin;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserUpdateStep;
use Ibexa\Migration\ValueObject\User\UpdateMetadata;

class Update implements StepBuilderInterface
{
    private const USER_ACCOUNT_FIELD = 'user_account';
    private const USER_FIELD_TYPE_NAME = 'ezuser';

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    public function __construct(FieldTypeServiceInterface $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        /** @var \Ibexa\Core\FieldType\User\Value $userAccount */
        $userAccount = $valueObject->getFieldValue(self::USER_ACCOUNT_FIELD);

        return new UserUpdateStep(
            UpdateMetadata::createFromApi($userAccount),
            new UserLogin($userAccount->login),
            $this->getFields($valueObject)
        );
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Content\Field[]
     */
    private function getFields(Content $userContent): array
    {
        $fields = [];
        foreach ($userContent->getFields() as $field) {
            if ($field->fieldTypeIdentifier === self::USER_FIELD_TYPE_NAME) {
                continue;
            }

            $hash = $this->fieldTypeService->getHashFromFieldValue($field->value, $field->fieldTypeIdentifier);
            $fields[] = Field::createFromValueObject($field, $hash);
        }

        return $fields;
    }
}

class_alias(Update::class, 'Ibexa\Platform\Migration\Generator\User\StepBuilder\Update');
