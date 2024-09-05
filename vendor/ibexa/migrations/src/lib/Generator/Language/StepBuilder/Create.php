<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Language\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\LanguageGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Language\Metadata;
use Ibexa\Migration\ValueObject\Step\LanguageCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Migration\Generator\Reference\LanguageGenerator */
    private $referenceLanguageGenerator;

    public function __construct(LanguageGenerator $referenceLanguageGenerator)
    {
        $this->referenceLanguageGenerator = $referenceLanguageGenerator;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $location
     */
    public function build(ValueObject $location): StepInterface
    {
        $references = $this->referenceLanguageGenerator->generate($location);

        return new LanguageCreateStep(
            Metadata::create($location),
            $references
        );
    }
}

class_alias(Create::class, 'Ibexa\Platform\Migration\Generator\Language\StepBuilder\Create');
