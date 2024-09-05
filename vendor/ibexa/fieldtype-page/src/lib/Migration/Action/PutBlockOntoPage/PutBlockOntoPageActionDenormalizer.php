<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Migration\Action\PutBlockOntoPage;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\FieldTypePage\Migration\Data\ZonesBlocksList;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * @internal
 */
final class PutBlockOntoPageActionDenormalizer extends AbstractActionDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === PutBlockOntoPageAction::ACTION_NAME;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): PutBlockOntoPageAction
    {
        $fieldDefinitionIdentifier = $data['fieldDefinitionIdentifier'] ?? null;
        $zones = $data['zones'] ?? null;

        if ($fieldDefinitionIdentifier === null || $zones === null) {
            throw new InvalidArgumentException(
                '$data',
                'Neither "zones" nor "fieldDefinitionIdentifier" values can be null'
            );
        }

        return new PutBlockOntoPageAction(
            $fieldDefinitionIdentifier,
            $this->denormalizer->denormalize($zones, ZonesBlocksList::class, $format, $context),
        );
    }
}
