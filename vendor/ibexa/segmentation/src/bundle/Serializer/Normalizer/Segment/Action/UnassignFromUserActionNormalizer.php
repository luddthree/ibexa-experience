<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\AbstractUserActionNormalizer;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Segmentation\Value\Step\Action\Segment\UnassignFromUser;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

final class UnassignFromUserActionNormalizer extends AbstractUserActionNormalizer implements CacheableSupportsMethodInterface
{
    protected function supportsAction(Action $action, string $format = null): bool
    {
        return $action instanceof UnassignFromUser;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\Action\Segment\UnassignFromUser $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array{
     *     "action": \Ibexa\Segmentation\Value\Step\Action\Segment\UnassignFromUser::TYPE,
     *     "id"?: int,
     *     "email"?: string,
     *     "login"?: string,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return ['action' => UnassignFromUser::TYPE] + $this->getUserData($object);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
