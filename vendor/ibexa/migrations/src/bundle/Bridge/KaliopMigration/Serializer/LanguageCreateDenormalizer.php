<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class LanguageCreateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected function getHandledKaliopType(): string
    {
        return 'language';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'create';
    }

    protected function convertFromKaliopFormat(
        array $data,
        string $type,
        string $format = null,
        array $context = []
    ): array {
        $convertedData = [
            'type' => 'language',
            'mode' => Mode::CREATE,
            'metadata' => $this->prepareMetadata($data),
        ];

        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);
        if ($references) {
            $convertedData['references'] = $references;
        }

        return $convertedData;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{
     *      enabled: ?bool,
     *      name: string,
     *      languageCode: string,
     * }
     */
    private function prepareMetadata(array $data): array
    {
        Assert::keyExists($data, 'name');
        Assert::keyExists($data, 'lang');

        return [
            'languageCode' => $data['lang'],
            'name' => $data['name'],
            'enabled' => $data['enabled'] ?? true,
        ];
    }
}

class_alias(LanguageCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\LanguageCreateDenormalizer');
