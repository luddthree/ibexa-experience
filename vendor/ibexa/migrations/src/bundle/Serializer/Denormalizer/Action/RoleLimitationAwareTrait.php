<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Action;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Migration\ValueObject\Step\Action\RoleLimitationAwareInterface;
use Webmozart\Assert\Assert;

trait RoleLimitationAwareTrait
{
    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /**
     * @param array<string, mixed> $data
     */
    private function handleRoleLimitation(array $data, RoleLimitationAwareInterface $action): void
    {
        if (isset($data['limitation'])) {
            $limitationData = $data['limitation'];
            Assert::isArray($limitationData);
            Assert::keyExists($limitationData, 'type');
            Assert::keyExists($limitationData, 'values');

            $limitationType = $this->roleService->getLimitationType($limitationData['type']);
            $limitation = $limitationType->buildValue($limitationData['values']);
            Assert::isInstanceOf($limitation, RoleLimitation::class);
            /** @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $limitation */
            $action->setLimitation($limitation);
        }
    }
}

class_alias(RoleLimitationAwareTrait::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Action\RoleLimitationAwareTrait');
