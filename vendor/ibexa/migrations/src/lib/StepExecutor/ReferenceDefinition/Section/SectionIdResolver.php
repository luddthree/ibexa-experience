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
use Webmozart\Assert\Assert;

final class SectionIdResolver implements SectionResolverInterface
{
    public static function getHandledType(): string
    {
        return 'section_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, Section $section): Reference
    {
        $name = $referenceDefinition->getName();
        Assert::notNull(
            $section->id,
            'Section object does not contain an ID. Make sure to reload Section object if persisted.'
        );

        return Reference::create($name, $section->id);
    }
}
