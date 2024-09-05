<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\Section;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;

/**
 * @internal
 */
interface SectionResolverInterface
{
    public function resolve(ReferenceDefinition $referenceDefinition, Section $section): Reference;
}
