<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ContentTypeGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class Delete implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    public function build(ValueObject $contentTypeGroup): StepInterface
    {
        return new ContentTypeGroupDeleteStep(
            new Matcher(Matcher::CONTENT_TYPE_NAME_IDENTIFIER, $contentTypeGroup->identifier),
        );
    }
}

class_alias(Delete::class, 'Ibexa\Platform\Migration\Generator\ContentTypeGroup\StepBuilder\Delete');
