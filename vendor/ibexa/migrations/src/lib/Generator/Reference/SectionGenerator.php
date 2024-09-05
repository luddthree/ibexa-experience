<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Section\SectionIdResolver;
use Webmozart\Assert\Assert;

final class SectionGenerator extends AbstractReferenceGenerator
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     */
    public function generate(ValueObject $section): array
    {
        Assert::isInstanceOf($section, Section::class);

        return $this->generateReferences((string)$section->id, 'section_id');
    }

    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref__section', SectionIdResolver::getHandledType()),
        ];
    }
}
