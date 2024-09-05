<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

abstract class AbstractCriterionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var string */
    private $fieldName;

    public function __construct(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     */
    abstract protected function createCriterion(array $data, string $type, ?string $format, array $context): FilteringCriterion;

    final protected function getField(): string
    {
        return $this->fieldName;
    }

    /**
     * @param mixed $data
     * @param array<mixed> $context
     */
    final public function denormalize($data, string $type, string $format = null, array $context = []): FilteringCriterion
    {
        Assert::isArray($data);

        return $this->createCriterion($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === FilteringCriterion::class && is_array($data) && $data['field'] === $this->getField();
    }

    /**
     * @param mixed $object
     * @param array<mixed> $context
     *
     * @return array{field: string, value: int|float|string|bool|array<int|float|string|bool>}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        assert($object instanceof Criterion);

        $value = $object->value;
        if (is_array($value) && count($value) === 1) {
            // Single element arrays are unpacked to present a single value
            [$value] = $value;
        }

        return [
            'field' => $this->getField(),
            'value' => $value,
        ];
    }
}

class_alias(AbstractCriterionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Criterion\AbstractCriterionNormalizer');
