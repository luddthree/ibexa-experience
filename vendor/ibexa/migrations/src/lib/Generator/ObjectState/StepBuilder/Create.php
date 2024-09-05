<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ObjectState\StepBuilder;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\ObjectState\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

final class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(
        ObjectStateService $objectStateService
    ) {
        $this->objectStateService = $objectStateService;
    }

    public function build(ValueObject $objectState): StepInterface
    {
        Assert::isInstanceOf($objectState, ObjectState::class);

        /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState */
        $metadata = CreateMetadata::createFromApi($objectState);

        return new ObjectStateCreateStep(
            $metadata
        );
    }
}

class_alias(Create::class, 'Ibexa\Platform\Migration\Generator\ObjectState\StepBuilder\Create');
