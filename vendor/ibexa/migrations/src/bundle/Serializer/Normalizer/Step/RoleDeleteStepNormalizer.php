<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\RoleDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\RoleDeleteStep
 * >
 */
final class RoleDeleteStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'role';
    }

    public function getMode(): string
    {
        return Mode::DELETE;
    }

    public function getHandledClassType(): string
    {
        return RoleDeleteStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\RoleDeleteStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     match: mixed,
     * }
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'match' => $this->normalizer->normalize($object->match, $format, $context),
        ];
    }

    /**
     * @param array{
     *     match: array,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\RoleDeleteStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Migration\ValueObject\Step\Role\Matcher $match */
        $match = $this->denormalizer->denormalize($data['match'], Matcher::class, $format, $context);

        return new RoleDeleteStep($match);
    }
}

class_alias(RoleDeleteStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\RoleDeleteStepNormalizer');
