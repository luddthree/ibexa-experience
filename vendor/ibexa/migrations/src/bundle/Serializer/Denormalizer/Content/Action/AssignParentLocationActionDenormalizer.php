<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Content\Action;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignParentLocation;
use Webmozart\Assert\Assert;

final class AssignParentLocationActionDenormalizer extends AbstractActionDenormalizer
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === AssignParentLocation::TYPE;
    }

    /**
     * @param array<mixed> $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action\Content\AssignParentLocation
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'value');

        return new AssignParentLocation($data['value']);
    }
}

class_alias(AssignParentLocationActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Content\Action\AssignParentLocationActionDenormalizer');
