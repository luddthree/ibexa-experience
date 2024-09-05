<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\UserGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Field as APIField;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\UserGroupGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupCreateStep;
use Ibexa\Migration\ValueObject\UserGroup\CreateMetadata;
use Webmozart\Assert\Assert;

final class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    /** @var \Ibexa\Migration\Generator\Reference\UserGroupGenerator */
    private $userGroupGenerator;

    public function __construct(
        FieldTypeServiceInterface $fieldTypeService,
        UserGroupGenerator $userGroupGenerator
    ) {
        $this->fieldTypeService = $fieldTypeService;
        $this->userGroupGenerator = $userGroupGenerator;
    }

    public function build(ValueObject $userGroup): StepInterface
    {
        Assert::isInstanceOf($userGroup, UserGroup::class);

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup */
        $metadata = CreateMetadata::createFromApi($userGroup);

        $fields = array_map(
            function (APIField $field): Field {
                $hash = $this->fieldTypeService->getHashFromFieldValue($field->value, $field->fieldTypeIdentifier);

                return Field::createFromValueObject($field, $hash);
            },
            $userGroup->fields
        );
        $roles = [];

        $references = $this->userGroupGenerator->generate($userGroup);

        return new UserGroupCreateStep(
            $metadata,
            $fields,
            $roles,
            $references,
        );
    }
}

class_alias(Create::class, 'Ibexa\Platform\Migration\Generator\UserGroup\StepBuilder\Create');
