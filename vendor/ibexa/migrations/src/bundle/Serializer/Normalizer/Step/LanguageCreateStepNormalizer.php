<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Language\Metadata;
use Ibexa\Migration\ValueObject\Step\LanguageCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\LanguageCreateStep
 * >
 */
final class LanguageCreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'language';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return LanguageCreateStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\LanguageCreateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'references' => $this->normalizer->normalize($object->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array,
     *     references: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\LanguageCreateStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        return new LanguageCreateStep(
            Metadata::createFromArray($data['metadata']),
            array_map(static function (array $reference): ReferenceDefinition {
                return new ReferenceDefinition(
                    $reference['name'],
                    $reference['type']
                );
            }, $data['references'] ?? [])
        );
    }
}

class_alias(LanguageCreateStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\LanguageCreateStepNormalizer');
