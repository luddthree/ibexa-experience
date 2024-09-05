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

final class AssignContentTypeGroupActionNormalizer extends AbstractActionNormalizer implements CacheableSupportsMethodInterface
{
    protected function supportsAction(Action $action, string $format = null): bool
    {
        return $action instanceof Action\ContentType\AssignContentTypeGroup;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\ContentType\AssignContentTypeGroup $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array{
     *     "action": Action\ContentType\AssignContentTypeGroup::TYPE,
     *     "value": string,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'action' => Action\ContentType\AssignContentTypeGroup::TYPE,
            'value' => $object->getValue(),
        ];
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(AssignContentTypeGroupActionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\ContentType\Action\AssignContentTypeGroupActionNormalizer');
