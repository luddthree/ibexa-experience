<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\ContentType\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class ContentTypeUpdateStepBuilder implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep
     */
    public function build(ValueObject $contentType): StepInterface
    {
        return new ContentTypeUpdateStep(
            UpdateMetadata::create($contentType),
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
            $this->createMatcher($contentType),
        );
    }

    private function createMatcher(ContentType $contentType): Matcher
    {
        return new Matcher(
            Matcher::CONTENT_TYPE_IDENTIFIER,
            $contentType->identifier
        );
    }
}

class_alias(ContentTypeUpdateStepBuilder::class, 'Ibexa\Platform\Migration\Generator\StepBuilder\ContentTypeUpdateStepBuilder');
