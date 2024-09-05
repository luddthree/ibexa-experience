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

final class UnassignContentTypeGroupActionNormalizer extends AbstractActionNormalizer implements CacheableSupportsMethodInterface
{
    protected function supportsAction(Action $action, string $format = null): bool
    {
        return $action instanceof Action\ContentType\UnassignContentTypeGroup;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\ContentType\UnassignContentTypeGroup $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array{
     *     "action": Action\ContentType\UnassignContentTypeGroup::TYPE,
     *     "value": string,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'action' => Action\ContentType\UnassignContentTypeGroup::TYPE,
            'value' => $object->getValue(),
        ];
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(UnassignContentTypeGroupActionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\ContentType\Action\UnassignContentTypeGroupActionNormalizer');
