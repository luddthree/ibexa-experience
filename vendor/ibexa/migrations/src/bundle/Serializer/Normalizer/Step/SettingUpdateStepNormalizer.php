<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\SettingUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\SettingUpdateStep
 * >
 */
final class SettingUpdateStepNormalizer extends AbstractStepNormalizer
{
    /**
     * @param \Ibexa\Migration\ValueObject\Step\SettingUpdateStep $object
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        return [
            'group' => $object->group,
            'identifier' => $object->identifier,
            'value' => $object->value,
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'group');
        Assert::stringNotEmpty($data['group']);
        Assert::keyExists($data, 'identifier');
        Assert::stringNotEmpty($data['identifier']);
        Assert::keyExists($data, 'value');

        return new SettingUpdateStep(
            $data['group'],
            $data['identifier'],
            $data['value'],
        );
    }

    public function getHandledClassType(): string
    {
        return SettingUpdateStep::class;
    }

    public function getType(): string
    {
        return 'setting';
    }

    public function getMode(): string
    {
        return Mode::UPDATE;
    }
}
