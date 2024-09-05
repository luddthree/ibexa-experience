<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use function is_array;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class AbstractDenormalizer implements DenormalizerInterface, ContextAwareDenormalizerInterface
{
    abstract protected function getHandledKaliopType(): string;

    abstract protected function getHandledKaliopMode(): string;

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @return array<mixed>
     */
    abstract protected function convertFromKaliopFormat(
        array $data,
        string $type,
        string $format = null,
        array $context = []
    ): array;

    /**
     * @param array<mixed> $data
     * @param array<string, mixed> $context
     *
     * @return mixed
     */
    final public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return $this->convertFromKaliopFormat($data, $type, $format, $context);
    }

    /**
     * @param mixed $data
     * @param array<mixed> $context
     *
     * @return bool
     */
    final public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        if ($type !== StepInterface::class) {
            return false;
        }

        if (!is_array($data)) {
            return false;
        }

        return $this->supportsKaliopModeAndType($data);
    }

    /**
     * @param array<mixed> $data
     */
    protected function supportsKaliopModeAndType(array $data): bool
    {
        if (!isset($data['type'], $data['mode'])) {
            return false;
        }

        return $this->getHandledKaliopType() === $data['type'] && $this->getHandledKaliopMode() === $data['mode'];
    }
}

class_alias(AbstractDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\AbstractDenormalizer');
