<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Criterion;

use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

abstract class AbstractContextAwareCriterionNormalizer extends AbstractCriterionNormalizer implements
    ContextAwareDenormalizerInterface,
    ContextAwareNormalizerInterface
{
    public const CRITERION_CONTEXT_KEY = 'criterion_type';

    /** @var string */
    private $contextType;

    public function __construct(string $fieldName, string $contextType)
    {
        parent::__construct($fieldName);
        $this->contextType = $contextType;
    }

    /**
     * @param mixed $data
     * @param array<mixed> $context
     */
    abstract protected function supportsNormalizationData($data, ?string $format, array $context): bool;

    /**
     * @param mixed $data
     * @param array<mixed> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (!isset($context[self::CRITERION_CONTEXT_KEY]) || $context[self::CRITERION_CONTEXT_KEY] !== $this->contextType) {
            return false;
        }

        return $this->supportsNormalizationData($data, $format, $context);
    }

    /**
     * @param mixed $data
     * @param array<mixed> $context
     *
     * @return bool
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        if (!isset($context[self::CRITERION_CONTEXT_KEY]) || $context[self::CRITERION_CONTEXT_KEY] !== $this->contextType) {
            return false;
        }

        return parent::supportsDenormalization($data, $type, $format);
    }
}

class_alias(AbstractContextAwareCriterionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Criterion\AbstractContextAwareCriterionNormalizer');
