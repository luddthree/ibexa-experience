<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;

/**
 * @internal
 */
interface ResolverInterface
{
    /**
     * @throws \Ibexa\Migration\StepExecutor\ReferenceDefinition\UnhandledReferenceDefinitionException
     */
    public function resolve(ReferenceDefinition $referenceDefinition, ValueObject $valueObject): Reference;
}

class_alias(ResolverInterface::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\ResolverInterface');
