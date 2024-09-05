<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep
 * >
 */
final class UserGroupDeleteStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'user_group';
    }

    public function getMode(): string
    {
        return Mode::DELETE;
    }

    public function getHandledClassType(): string
    {
        return UserGroupDeleteStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep $object
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
     * @return \Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep
     */
    protected function denormalizeStep($data, string $type, string $format = null, array $context = []): StepInterface
    {
        /** @var \Ibexa\Migration\ValueObject\UserGroup\Matcher $match */
        $match = $this->denormalizer->denormalize($data['match'], Matcher::class, $format, $context);

        return new UserGroupDeleteStep($match);
    }
}

class_alias(UserGroupDeleteStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\UserGroupDeleteStepNormalizer');
