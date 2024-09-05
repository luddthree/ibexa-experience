<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ContentTypeGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\ContentTypeGroupGenerator as ReferenceContentTypeGroupGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Migration\Generator\Reference\ContentTypeGroupGenerator */
    private $referenceContentTypeGroupGenerator;

    public function __construct(ReferenceContentTypeGroupGenerator $referenceContentTypeGroupGenerator)
    {
        $this->referenceContentTypeGroupGenerator = $referenceContentTypeGroupGenerator;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    public function build(ValueObject $contentTypeGroup): StepInterface
    {
        $references = $this->referenceContentTypeGroupGenerator->generate($contentTypeGroup);

        return new ContentTypeGroupCreateStep(
            CreateMetadata::create($contentTypeGroup),
            $references
        );
    }
}

class_alias(Create::class, 'Ibexa\Platform\Migration\Generator\ContentTypeGroup\StepBuilder\Create');
