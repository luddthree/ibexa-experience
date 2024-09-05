<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverter;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitation;
use Ibexa\Core\Limitation\SectionLimitationType;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Webmozart\Assert\Assert;

final class SectionLimitationConverter implements LimitationConverterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function convertToMigration(APILimitation $limitation): Limitation
    {
        Assert::isInstanceOf($limitation, APILimitation\SectionLimitation::class);

        $values = array_map(function ($value): string {
            Assert::numeric($value, "'$value' is not numeric");
            $section = $this->sectionService->loadSection((int)$value);

            return $section->identifier;
        }, $limitation->limitationValues);

        return new Limitation($limitation->getIdentifier(), $values);
    }

    public function convertToApi(Type $type, Limitation $limitation): APILimitation
    {
        Assert::isInstanceOf($type, SectionLimitationType::class);

        $values = array_map(function ($value): int {
            if (!is_int($value)) {
                $section = $this->sectionService->loadSectionByIdentifier((string)$value);
            } else {
                $section = $this->sectionService->loadSection((int)$value);
            }

            return $section->id;
        }, $limitation->values);

        return $type->buildValue($values);
    }

    public function supportsConversionToMigration(APILimitation $limitation): bool
    {
        return $limitation instanceof APILimitation\SectionLimitation;
    }

    public function supportsConversionToApi(Type $type, Limitation $limitation): bool
    {
        return $type instanceof SectionLimitationType;
    }
}

class_alias(SectionLimitationConverter::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\LimitationConverter\SectionLimitationConverter');
