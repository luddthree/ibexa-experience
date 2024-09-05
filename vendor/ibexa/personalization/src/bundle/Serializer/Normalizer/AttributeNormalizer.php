<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Serializer\Normalizer;

use Ibexa\Personalization\Value\Output\Attribute;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AttributeNormalizer implements NormalizerInterface
{
    public const ATTR_NAME = 'attribute';

    /**
     * {@inheritdoc}()
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var $object \Ibexa\Personalization\Value\Output\Attribute */
        return [self::ATTR_NAME => [
            '@key' => $object->getName(),
            '@value' => $object->getValue(),
            '@type' => $object->getType(),
        ]];
    }

    /**
     * {@inheritdoc}()
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Attribute;
    }
}

class_alias(AttributeNormalizer::class, 'EzSystems\EzRecommendationClientBundle\Serializer\Normalizer\AttributeNormalizer');
