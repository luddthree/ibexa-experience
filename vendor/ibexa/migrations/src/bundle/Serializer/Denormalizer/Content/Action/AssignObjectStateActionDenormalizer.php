<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Content\Action;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Content\ObjectState;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignObjectState;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class AssignObjectStateActionDenormalizer extends AbstractActionDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === AssignObjectState::TYPE;
    }

    /**
     * @param array<mixed> $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action\Content\AssignObjectState
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'identifier');
        Assert::keyExists($data, 'groupIdentifier');

        $objectState = $this->denormalizer->denormalize($data, ObjectState::class, $format, $context);

        return new AssignObjectState($objectState);
    }
}

class_alias(AssignObjectStateActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Content\Action\AssignObjectStateActionDenormalizer');
