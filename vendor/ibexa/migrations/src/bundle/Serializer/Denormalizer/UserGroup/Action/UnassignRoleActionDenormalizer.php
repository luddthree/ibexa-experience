<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\UserGroup\Action;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\UserGroup\UnassignRole;
use Webmozart\Assert\Assert;

final class UnassignRoleActionDenormalizer extends AbstractActionDenormalizer
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === UnassignRole::TYPE;
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action\UserGroup\UnassignRole;
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'id');

        return new UnassignRole($data['id']);
    }
}

class_alias(UnassignRoleActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\UserGroup\UnassignRoleActionDenormalizer');
