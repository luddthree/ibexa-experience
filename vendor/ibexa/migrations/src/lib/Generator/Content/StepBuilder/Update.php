<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Content\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\RemoteId;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class Update implements StepBuilderInterface
{
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
        $fields = [];
        foreach ($valueObject->getFields() as $key => $field) {
            $hash = $this->fieldTypeService->getHashFromFieldValue($field->value, $field->fieldTypeIdentifier);

            $fields[$key] = Field::createFromValueObject($field, $hash);
        }

        return new ContentUpdateStep(
            UpdateMetadata::createFromContent($valueObject),
            $this->createCriterion($valueObject),
            $fields
        );
    }

    private function createCriterion(Content $content): FilteringCriterion
    {
        return new RemoteId($content->contentInfo->remoteId);
    }
}

class_alias(Update::class, 'Ibexa\Platform\Migration\Generator\Content\StepBuilder\Update');
