<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\ContentType\Action;

use Ibexa\Contracts\Migration\Serializer\Normalizer\AbstractActionNormalizer;
use Ibexa\Migration\ValueObject\Step\Action;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

final class RemoveDraftsActionNormalizer extends AbstractActionNormalizer implements CacheableSupportsMethodInterface
{
    protected function supportsAction(Action $action, string $format = null): bool
    {
        return $action instanceof Action\ContentType\RemoveDrafts;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveDrafts $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array{
     *     "action": \Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveDrafts::TYPE,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'action' => Action\ContentType\RemoveDrafts::TYPE,
        ];
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(RemoveDraftsActionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\ContentType\Action\RemoveDraftsActionNormalizer');
