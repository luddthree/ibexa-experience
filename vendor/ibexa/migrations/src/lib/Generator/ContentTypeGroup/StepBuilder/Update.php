<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ContentTypeGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher;
use Ibexa\Migration\ValueObject\ContentTypeGroup\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class Update implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    public function build(ValueObject $contentTypeGroup): StepInterface
    {
        return new ContentTypeGroupUpdateStep(
            UpdateMetadata::create($contentTypeGroup),
            $this->createMatcher($contentTypeGroup),
        );
    }

    private function createMatcher(ContentTypeGroup $contentTypeGroup): Matcher
    {
        return new Matcher(
            Matcher::CONTENT_TYPE_NAME_IDENTIFIER,
            $contentTypeGroup->identifier
        );
    }
}

class_alias(Update::class, 'Ibexa\Platform\Migration\Generator\ContentTypeGroup\StepBuilder\Update');
