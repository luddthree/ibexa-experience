<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentIdResolver;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentLocationIdResolver;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Content\ContentLocationPathResolver;
use Webmozart\Assert\Assert;

final class ContentGenerator extends AbstractReferenceGenerator
{
    /**
     * @return \Ibexa\Migration\Generator\Reference\ReferenceMetadata[]
     */
    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref__content', ContentIdResolver::getHandledType()),
            new ReferenceMetadata('ref_location', ContentLocationIdResolver::getHandledType()),
            new ReferenceMetadata('ref_path', ContentLocationPathResolver::getHandledType()),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     */
    public function generate(ValueObject $content): array
    {
        Assert::isInstanceOf($content, Content::class);

        $contentTypeName = $content->getContentType()->identifier;
        $contentName = $content->getName();

        return $this->generateReferences($contentTypeName, $contentName);
    }
}

class_alias(ContentGenerator::class, 'Ibexa\Platform\Migration\Generator\Reference\ContentGenerator');
