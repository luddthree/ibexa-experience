<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\Language;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;

/**
 * @internal
 */
interface LanguageResolverInterface
{
    public function resolve(ReferenceDefinition $referenceDefinition, Language $language): Reference;
}
