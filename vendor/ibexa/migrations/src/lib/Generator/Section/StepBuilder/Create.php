<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Section\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\SectionGenerator as ReferenceSectionGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Section\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\SectionCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

final class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Migration\Generator\Reference\SectionGenerator */
    private $referenceRoleGenerator;

    public function __construct(ReferenceSectionGenerator $referenceRoleGenerator)
    {
        $this->referenceRoleGenerator = $referenceRoleGenerator;
    }

    public function build(ValueObject $section): StepInterface
    {
        Assert::isInstanceOf($section, Section::class);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section $section */
        $metadata = CreateMetadata::createFromApi($section);

        $references = $this->referenceRoleGenerator->generate($section);

        return new SectionCreateStep(
            $metadata,
            $references
        );
    }
}

class_alias(Create::class, 'Ibexa\Platform\Migration\Generator\Section\StepBuilder\Create');
