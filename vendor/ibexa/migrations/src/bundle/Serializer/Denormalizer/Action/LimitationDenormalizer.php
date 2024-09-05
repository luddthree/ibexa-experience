<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Action;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager;
use Ibexa\Migration\ValueObject\Step\Role\Limitation as RoleLimitation;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class LimitationDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /** @var \Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager */
    private $limitationConverter;

    public function __construct(RoleService $roleService, LimitationConverterManager $limitationConverter)
    {
        $this->roleService = $roleService;
        $this->limitationConverter = $limitationConverter;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Limitation
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'type');
        Assert::keyExists($data, 'values');

        $limitationType = $this->roleService->getLimitationType($data['type']);
        $limitation = new RoleLimitation($data['type'], $data['values']);
        $apiLimitation = $this->limitationConverter->convertMigrationToApi($limitation);

        return $limitationType->buildValue($apiLimitation->limitationValues);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Limitation::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(LimitationDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Action\LimitationDenormalizer');
