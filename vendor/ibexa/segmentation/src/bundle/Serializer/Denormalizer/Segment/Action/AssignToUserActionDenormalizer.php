<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Serializer\Denormalizer\Segment\Action;

use Ibexa\Bundle\Segmentation\Serializer\Denormalizer\AbstractUserActionDenormalizer;
use Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;

/**
 * @extends \Ibexa\Bundle\Segmentation\Serializer\Denormalizer\AbstractUserActionDenormalizer<
 *     \Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser
 * >
 */
final class AssignToUserActionDenormalizer extends AbstractUserActionDenormalizer implements DenormalizerAwareInterface
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === AssignToUser::TYPE;
    }

    /**
     * @param array<mixed> $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return $this->getActionStep($data, AssignToUser::class);
    }
}
