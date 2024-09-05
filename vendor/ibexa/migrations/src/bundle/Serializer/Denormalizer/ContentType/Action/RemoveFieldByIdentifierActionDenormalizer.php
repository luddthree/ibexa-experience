<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\ContentType\Action;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveFieldByIdentifier;
use Webmozart\Assert\Assert;

final class RemoveFieldByIdentifierActionDenormalizer extends AbstractActionDenormalizer
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === RemoveFieldByIdentifier::TYPE;
    }

    /**
     * @param array<mixed> $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveFieldByIdentifier
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'value');

        return new RemoveFieldByIdentifier($data['value']);
    }
}

class_alias(RemoveFieldByIdentifierActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ContentType\Action\RemoveFieldByIdentifierActionDenormalizer');
