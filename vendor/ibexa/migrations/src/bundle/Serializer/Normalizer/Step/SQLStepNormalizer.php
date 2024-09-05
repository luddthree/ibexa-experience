<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface;
use Ibexa\Migration\ValueObject;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class SQLStepNormalizer implements StepNormalizerInterface, DenormalizerInterface, DenormalizerAwareInterface, NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;

    public function getType(): string
    {
        return 'sql';
    }

    public function getMode(): string
    {
        return 'execute';
    }

    public function getHandledClassType(): string
    {
        return ValueObject\Step\SQLExecuteStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\SQLExecuteStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     type: string,
     *     mode: string,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'type' => $this->getType(),
            'mode' => $this->getMode(),
            'query' => $this->normalizer->normalize($object->queries, $format, $context),
        ];
    }

    /**
     * @param array{
     *     type: string,
     *     mode: string,
     *     query: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\SQLExecuteStep
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::keyExists($data, 'query');
        Assert::isArray($data['query']);

        /** @var \Ibexa\Migration\ValueObject\Sql\Query[] $queries */
        $queries = $this->denormalizer->denormalize(
            $data['query'],
            ValueObject\Sql\Query::class . '[]',
            $format,
            $context
        );

        return new ValueObject\Step\SQLExecuteStep($queries);
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof ValueObject\Step\SQLExecuteStep;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === ValueObject\Step\SQLExecuteStep::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(SQLStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\SQLStepNormalizer');
