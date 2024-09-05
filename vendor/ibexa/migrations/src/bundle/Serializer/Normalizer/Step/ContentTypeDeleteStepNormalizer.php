<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep
 * >
 */
final class ContentTypeDeleteStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'content_type';
    }

    public function getMode(): string
    {
        return Mode::DELETE;
    }

    public function getHandledClassType(): string
    {
        return ContentTypeDeleteStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->getMatch(), $format, $context),
        ];
    }

    /**
     * @param array{
     *     match: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep
     */
    public function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'match');
        Assert::isArray($data['match']);

        /** @var \Ibexa\Migration\ValueObject\ContentType\Matcher $match */
        $match = $this->denormalizer->denormalize(
            $data['match'],
            Matcher::class,
            $format,
            $context
        );

        return new ContentTypeDeleteStep($match);
    }
}

class_alias(ContentTypeDeleteStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeDeleteStepNormalizer');
