<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;

/**
 * @internal
 */
interface ContentResolverInterface
{
    public function resolve(ReferenceDefinition $referenceDefinition, Content $content): Reference;
}

class_alias(ContentResolverInterface::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\Content\ContentResolverInterface');
