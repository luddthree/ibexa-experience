<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentType;

use Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class MatcherNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /** @var \Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface */
    private $contentTypeFinderRegistry;

    public function __construct(ContentTypeFinderRegistryInterface $contentTypeFinderRegistry)
    {
        $this->contentTypeFinderRegistry = $contentTypeFinderRegistry;
    }

    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Matcher
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'field');
        Assert::keyExists($data, 'value');

        Assert::string($data['field']);
        Assert::scalar($data['value']);

        $this->contentTypeFinderRegistry->getFinder($data['field']);

        return new Matcher($data['field'], $data['value']);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === Matcher::class;
    }

    /**
     * @param mixed $object
     * @param array<string, mixed> $context
     *
     * @return array{field: string, value: scalar}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        assert($object instanceof Matcher);

        return [
            'field' => $object->getField(),
            'value' => $object->getValue(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Matcher;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(MatcherNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentType\MatcherNormalizer');
