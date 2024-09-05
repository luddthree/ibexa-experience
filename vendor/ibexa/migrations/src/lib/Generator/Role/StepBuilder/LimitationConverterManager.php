<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitation;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;

/**
 * Handles converting of Limitations from Migration to API and from API to Migration classes.
 *
 * Delegates the conversion to LimitationConverterInterface instances. If none supports a particular type of conversion,
 * a default one is used (especially no limitation value conversion is done).
 */
final class LimitationConverterManager
{
    /** @var iterable<\Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverter\LimitationConverterInterface> */
    private $limitationConverters;

    /** @var \Ibexa\Core\Repository\Permission\LimitationService */
    private $limitationService;

    /**
     * @param iterable<\Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverter\LimitationConverterInterface> $limitationConverters
     */
    public function __construct(LimitationService $limitationService, iterable $limitationConverters)
    {
        $this->limitationConverters = $limitationConverters;
        $this->limitationService = $limitationService;
    }

    public function convertApiToMigration(APILimitation $limitation): Limitation
    {
        foreach ($this->limitationConverters as $limitationConverter) {
            if ($limitationConverter->supportsConversionToMigration($limitation)) {
                return $limitationConverter->convertToMigration($limitation);
            }
        }

        return new Limitation($limitation->getIdentifier(), $limitation->limitationValues);
    }

    public function convertMigrationToApi(Limitation $limitation): APILimitation
    {
        $type = $this->limitationService->getLimitationType($limitation->identifier);

        foreach ($this->limitationConverters as $limitationConverter) {
            if ($limitationConverter->supportsConversionToApi($type, $limitation)) {
                return $limitationConverter->convertToApi($type, $limitation);
            }
        }

        return $type->buildValue($limitation->values);
    }
}

class_alias(LimitationConverterManager::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\LimitationConverterManager');
