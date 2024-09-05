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
use Ibexa\Migration\ValueObject\Step\UserGroupUpdateStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use Ibexa\Migration\ValueObject\UserGroup\UpdateMetadata;
use Webmozart\Assert\Assert;

final class Update implements StepBuilderInterface
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

    public function build(ValueObject $valueObject): StepInterface
    {
        Assert::isInstanceOf($valueObject, UserGroup::class);

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $valueObject */
        $metadata = UpdateMetadata::createFromApi($valueObject);
        $fields = $this->getFields($valueObject);
        $references = $this->userGroupGenerator->generate($valueObject);

        return new UserGroupUpdateStep(
            $metadata,
            new Matcher(Matcher::ID, $valueObject->id),
            $fields,
            [],
            $references,
        );
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Content\Field[]
     */
    private function getFields(UserGroup $userGroup): array
    {
        return array_map(
            function (APIField $field): Field {
                $hash = $this->fieldTypeService->getHashFromFieldValue($field->value, $field->fieldTypeIdentifier);

                return Field::createFromValueObject($field, $hash);
            },
            $userGroup->fields
        );
    }
}

class_alias(Update::class, 'Ibexa\Platform\Migration\Generator\UserGroup\StepBuilder\Update');
