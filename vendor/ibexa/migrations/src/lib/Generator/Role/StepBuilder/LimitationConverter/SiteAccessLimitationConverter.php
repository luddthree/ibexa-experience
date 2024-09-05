<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverter;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitation;
use Ibexa\Core\Limitation\SiteAccessLimitationType;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use LogicException;
use function sprintf;
use Webmozart\Assert\Assert;

final class SiteAccessLimitationConverter implements LimitationConverterInterface
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    /** @var string[]|null array mapping hashes to Site Access names */
    private $hashmap;

    public function __construct(SiteAccessServiceInterface $siteAccessService)
    {
        $this->siteAccessService = $siteAccessService;
    }

    public function convertToMigration(APILimitation $limitation): Limitation
    {
        Assert::isInstanceOf($limitation, APILimitation\SiteAccessLimitation::class);

        $values = array_map([$this, 'getNameForHash'], $limitation->limitationValues);

        return new Limitation($limitation->getIdentifier(), $values);
    }

    public function convertToApi(Type $type, Limitation $limitation): APILimitation
    {
        Assert::isInstanceOf($type, SiteAccessLimitationType::class);

        $values = array_map(function (string $value): string {
            return $this->generateHash($value);
        }, $limitation->values);

        return $type->buildValue($values);
    }

    public function supportsConversionToMigration(APILimitation $limitation): bool
    {
        return $limitation instanceof APILimitation\SiteAccessLimitation;
    }

    public function supportsConversionToApi(Type $type, Limitation $limitation): bool
    {
        return $type instanceof SiteAccessLimitationType;
    }

    private function generateHash(string $value): string
    {
        return sprintf('%u', crc32($value));
    }

    private function getNameForHash(string $hash): string
    {
        if (!isset($this->hashmap)) {
            $this->hashmap = [];
            foreach ($this->siteAccessService->getAll() as $siteAccess) {
                $name = $siteAccess->name;
                $this->hashmap[$this->generateHash($name)] = $name;
            }
        }

        if (!isset($this->hashmap[$hash])) {
            throw new LogicException(sprintf(
                'SiteAccess limitation uses name that is not configured / defined. CRC32: %s (dec) or %s (hex)',
                $hash,
                dechex((int) $hash),
            ));
        }

        return $this->hashmap[$hash];
    }
}

class_alias(SiteAccessLimitationConverter::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\LimitationConverter\SiteAccessLimitationConverter');
