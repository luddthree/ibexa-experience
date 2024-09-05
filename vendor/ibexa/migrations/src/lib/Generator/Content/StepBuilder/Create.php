<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Content\StepBuilder;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Migration\Generator\Reference\ContentGenerator as ReferenceContentGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\Location;
use Ibexa\Migration\ValueObject\Content\ObjectState;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignObjectState;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Traversable;
use Webmozart\Assert\Assert;

class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Migration\Generator\Reference\ContentGenerator */
    private $referenceContentGenerator;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(
        ReferenceContentGenerator $referenceContentGenerator,
        FieldTypeServiceInterface $fieldTypeService,
        ObjectStateService $objectStateService
    ) {
        $this->referenceContentGenerator = $referenceContentGenerator;
        $this->fieldTypeService = $fieldTypeService;
        $this->objectStateService = $objectStateService;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        $references = $this->referenceContentGenerator->generate($valueObject);

        $fields = [];
        foreach ($valueObject->getFields() as $key => $field) {
            $hash = $this->fieldTypeService->getHashFromFieldValue($field->value, $field->fieldTypeIdentifier);

            $fields[$key] = Field::createFromValueObject($field, $hash);
        }

        $actions = [];

        foreach ($this->objectStateService->loadObjectStateGroups() as $objectStateGroup) {
            try {
                $objectStates = $this->objectStateService->loadObjectStates($objectStateGroup);

                $contentObjectState = $this->objectStateService->getContentState(
                    $valueObject->contentInfo,
                    $objectStateGroup
                );

                if ($objectStates instanceof Traversable) {
                    $objectStates = iterator_to_array($objectStates);
                }

                if (empty($objectStates)) {
                    continue;
                }

                if ($objectStates[0]->identifier === $contentObjectState->identifier) {
                    continue;
                }

                $actions[] = new AssignObjectState(
                    ObjectState::createFromValueObject(
                        $contentObjectState
                    )
                );
            } catch (NotFoundException $e) {
                continue;
            }
        }

        $location = $valueObject->contentInfo->getMainLocation();

        Assert::notNull($location);

        return new ContentCreateStep(
            CreateMetadata::createFromContent($valueObject),
            Location::createFromValueObject($location),
            $fields,
            $references,
            $actions
        );
    }
}

class_alias(Create::class, 'Ibexa\Platform\Migration\Generator\Content\StepBuilder\Create');
