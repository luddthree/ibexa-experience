<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Content\Action;

use Ibexa\Contracts\Migration\Serializer\Normalizer\AbstractActionNormalizer;
use Ibexa\Migration\ValueObject\Step\Action;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

final class AssignObjectStateActionNormalizer extends AbstractActionNormalizer implements CacheableSupportsMethodInterface
{
    protected function supportsAction(Action $action, string $format = null): bool
    {
        return $action instanceof Action\Content\AssignObjectState;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action\Content\AssignObjectState $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array{
     *     "action": Action\Content\AssignObjectState::TYPE,
     *     "value": array{
     *         "groupIdentifier": string,
     *         "identifier": string,
     *     },
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'action' => Action\Content\AssignObjectState::TYPE,
            'value' => $object->getValue(),
        ];
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(AssignObjectStateActionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Content\Action\AssignObjectStateActionNormalizer');
