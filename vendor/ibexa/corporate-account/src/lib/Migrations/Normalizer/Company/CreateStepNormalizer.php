<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Normalizer\Company;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\CorporateAccount\Migrations\Generator\Company\CreateMetadata;
use Ibexa\CorporateAccount\Migrations\Generator\Company\Step\CompanyCreateStep;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CreateStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'company';
    }

    public function getMode(): string
    {
        return Mode::CREATE;
    }

    public function getHandledClassType(): string
    {
        return CompanyCreateStep::class;
    }

    /**
     * @param \Ibexa\CorporateAccount\Migrations\Generator\Company\Step\CompanyCreateStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     metadata: mixed,
     *     fields: mixed,
     *     references: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'metadata' => $this->normalizer->normalize($object->metadata, $format, $context),
            'fields' => $this->normalizer->normalize($object->fields, $format, $context),
            'references' => $this->normalizer->normalize($object->references, $format, $context),
        ];
    }

    /**
     * @param array{
     *     metadata: array<string, mixed>,
     *     fields: array<string, mixed>,
     *     references: array<string, mixed>|null,
     * } $data
     * @param array<string, mixed> $context
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\CorporateAccount\Migrations\Generator\Company\CreateMetadata $metadata */
        $metadata = $this->denormalizer->denormalize($data['metadata'], CreateMetadata::class, $format, $context);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = $this->denormalizer->denormalize($data['fields'], Field::class . '[]', $format, $context);

        /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references */
        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);

        return new CompanyCreateStep(
            $metadata,
            $fields,
            $references
        );
    }
}
