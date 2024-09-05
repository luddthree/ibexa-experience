<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\User\StepBuilder;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\UserGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\Generator\User\PasswordGeneratorInterface;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserCreateStep;
use Ibexa\Migration\ValueObject\User\CreateMetadata;
use Webmozart\Assert\Assert;

class Create implements StepBuilderInterface
{
    private const USER_FIELD_TYPE_NAME = 'ezuser';

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Migration\Generator\User\PasswordGeneratorInterface */
    private $passwordGenerator;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    private UserGenerator $referenceUserGenerator;

    public function __construct(
        UserService $userService,
        PasswordGeneratorInterface $passwordGenerator,
        FieldTypeServiceInterface $fieldTypeService,
        UserGenerator $referenceUserGenerator
    ) {
        $this->userService = $userService;
        $this->passwordGenerator = $passwordGenerator;
        $this->fieldTypeService = $fieldTypeService;
        $this->referenceUserGenerator = $referenceUserGenerator;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User|\Ibexa\Contracts\Core\Repository\Values\ValueObject $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        Assert::isInstanceOf($valueObject, User::class);

        $references = $this->referenceUserGenerator->generate($valueObject);

        return new UserCreateStep(
            CreateMetadata::createFromApi($valueObject, $this->passwordGenerator->generate()),
            $this->getGroups($valueObject),
            $this->getFields($valueObject),
            $references
        );
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Content\Field[]
     */
    private function getFields(User $userContent): array
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

    /**
     * @return string[]
     */
    private function getGroups(User $user): array
    {
        $groups = [];
        foreach ($this->userService->loadUserGroupsOfUser($user) as $key => $group) {
            $groups[$key] = $group->contentInfo->remoteId;
        }

        return $groups;
    }
}

class_alias(Create::class, 'Ibexa\Platform\Migration\Generator\User\StepBuilder\Create');
