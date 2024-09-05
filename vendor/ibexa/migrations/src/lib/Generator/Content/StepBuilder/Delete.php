<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Content\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\RemoteId;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Step\ContentDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class Delete implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     */
    public function build(ValueObject $content): StepInterface
    {
        return new ContentDeleteStep(
            new RemoteId($content->contentInfo->remoteId)
        );
    }
}

class_alias(Delete::class, 'Ibexa\Platform\Migration\Generator\Content\StepBuilder\Delete');
