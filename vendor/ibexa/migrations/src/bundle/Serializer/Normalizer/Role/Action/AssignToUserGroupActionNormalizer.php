<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Role\Action;

use Ibexa\Contracts\Migration\Serializer\Normalizer\AbstractActionNormalizer;
use Ibexa\Migration\ValueObject\Step\Action;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

final class AssignToUserGroupActionNormalizer extends AbstractActionNormalizer implements CacheableSupportsMethodInterface
{
    protected function supportsAction(Action $action, string $format = null): bool
    {
        return $action instanceof Action\Role\AssignToUserGroup;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array{
     *     "action": Action\Role\AssignToUserGroup::TYPE,
     *     "id"?: int,
     *     "remote_id"?: string,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $data = [
            'action' => Action\Role\AssignToUserGroup::TYPE,
        ];

        if ($object->getId() !== null) {
            $data['id'] = $object->getId();
        }

        if ($object->getRemoteId() !== null) {
            $data['remote_id'] = $object->getRemoteId();
        }

        return $data;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(AssignToUserGroupActionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Role\Action\AssignToUserGroupActionNormalizer');
