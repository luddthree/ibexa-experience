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

final class AssignToUserActionNormalizer extends AbstractActionNormalizer implements CacheableSupportsMethodInterface
{
    protected function supportsAction(Action $action, string $format = null): bool
    {
        return $action instanceof Action\Role\AssignToUser;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array{
     *     "action": Action\Role\AssignToUser::TYPE,
     *     "id"?: int,
     *     "email"?: string,
     *     "login"?: string,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $data = [
            'action' => Action\Role\AssignToUser::TYPE,
        ];

        if ($object->getId() !== null) {
            $data['id'] = $object->getId();
        }

        if ($object->getEmail() !== null) {
            $data['email'] = $object->getEmail();
        }

        if ($object->getLogin() !== null) {
            $data['login'] = $object->getLogin();
        }

        return $data;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(AssignToUserActionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Role\Action\AssignToUserActionNormalizer');
