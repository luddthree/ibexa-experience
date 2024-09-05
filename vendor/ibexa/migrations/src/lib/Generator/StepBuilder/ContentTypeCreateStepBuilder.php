<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\ContentTypeGenerator as ReferenceContentTypeGenerator;
use Ibexa\Migration\ValueObject\ContentType\CreateMetadata;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class ContentTypeCreateStepBuilder implements StepBuilderInterface
{
    /** @var \Ibexa\Migration\Generator\Reference\ContentTypeGenerator */
    private $referenceContentTypeGenerator;

    public function __construct(ReferenceContentTypeGenerator $referenceContentTypeGenerator)
    {
        $this->referenceContentTypeGenerator = $referenceContentTypeGenerator;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep
     */
    public function build(ValueObject $contentType): StepInterface
    {
        $references = $this->referenceContentTypeGenerator->generate($contentType);

        return new ContentTypeCreateStep(
            CreateMetadata::create($contentType),
            FieldDefinitionCollection::create(
                $contentType->getFieldDefinitions()->map(
                    static function (ApiFieldDefinition $apiFieldDefinition): FieldDefinition {
                        return FieldDefinition::fromAPIFieldDefinition(
                            $apiFieldDefinition,
                            $apiFieldDefinition->defaultValue
                        );
                    }
                )
            ),
            $references
        );
    }
}

class_alias(ContentTypeCreateStepBuilder::class, 'Ibexa\Platform\Migration\Generator\StepBuilder\ContentTypeCreateStepBuilder');
