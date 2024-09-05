<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class VisibilityOptionNormalizer implements
    NormalizerAwareInterface,
    DenormalizerAwareInterface,
    ContextAwareNormalizerInterface,
    ContextAwareDenormalizerInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;

    private const MARKER = self::class . '.applied';

    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        if ($type !== FilteringCriterion::class) {
            return false;
        }

        if (!is_array($data)) {
            return false;
        }

        if (!isset($context[AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY])
            || $context[AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY] !== 'content') {
            return false;
        }

        if (($context[self::MARKER] ?? false) === true) {
            return false;
        }

        return true;
    }

    /**
     * @param array<mixed> $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $context[self::MARKER] = true;
        $addVisibilityCriterion = $data['only_visible_content'] ?? true;
        unset($data['only_visible_content']);

        $innerCriterion = $this->denormalizer->denormalize($data, $type, $format, $context);

        if ($addVisibilityCriterion === false) {
            return $innerCriterion;
        }

        if ($innerCriterion instanceof LogicalAnd) {
            $innerCriterion->criteria[] = new Visibility(Visibility::VISIBLE);

            return $innerCriterion;
        }

        return new LogicalAnd([
            $innerCriterion,
            new Visibility(Visibility::VISIBLE),
        ]);
    }

    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     *
     * @return bool
     */
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (!$data instanceof FilteringCriterion) {
            return false;
        }

        if (!isset($context[AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY])
            || $context[AbstractContextAwareCriterionNormalizer::CRITERION_CONTEXT_KEY] !== 'content') {
            return false;
        }

        if (($context[self::MARKER] ?? false) === true) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $object
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                self::MARKER => true,
            ] + $context
        );

        Assert::isArray($data);
        $data['only_visible_content'] = true;

        return $data;
    }
}
