<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class ContentUpdateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use ContentFieldPreparationTrait;
    use DenormalizerAwareTrait;

    protected function getHandledKaliopType(): string
    {
        return 'content';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'update';
    }

    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $convertedData = [
            'type' => 'content',
            'mode' => Mode::UPDATE,
            'match' => $this->denormalizer->denormalize($data, Criterion::class, $format, $context),
            'metadata' => $this->prepareMetadata($data),
            'fields' => $this->prepareFields($data),
        ];

        $references = $data['references'] ?? [];
        if (!empty($references)) {
            $convertedData['references'] = $this->denormalizer->denormalize(
                $references,
                ReferenceDefinition::class . '[]',
                $format,
                $context
            );
        }

        return $convertedData;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    private function prepareMetadata(array $data): array
    {
        return [
            'remoteId' => $data['new_remote_id'] ?? null,
            'alwaysAvailable' => $data['always_available'] ?? null,
            'mainLanguageCode' => null,
            'modificationDate' => $data['modification_date'] ?? null,
            'publishedData' => $data['publication_date'] ?? null,
            'ownerId' => $data['owner'] ?? null,
        ];
    }
}

class_alias(ContentUpdateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentUpdateDenormalizer');
