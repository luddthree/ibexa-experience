<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\User\Matcher;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class UserUpdateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const FIELD_IDENTIFIER_FIRST_NAME = 'first_name';
    private const FIELD_IDENTIFIER_LAST_NAME = 'last_name';

    protected function getHandledKaliopType(): string
    {
        return 'user';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'update';
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @return array<mixed>
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $convertedData = [
            'type' => $data['type'],
            'mode' => $data['mode'],
            'match' => $this->prepareMatch($data),
            'metadata' => $this->prepareMetadata($data),
            'fields' => $this->prepareFields($data),
        ];

        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);
        if ($references) {
            $convertedData['references'] = $references;
        }

        return $convertedData;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function prepareMatch(array $data): array
    {
        $matches = array_keys($data['match']);
        if (empty($matches)) {
            throw new RuntimeException('Missing "match" property');
        }

        if (false === isset($data['match']['id'])) {
            $matches = array_keys($data['match']);
            throw new UnhandledMatchPropertyException(
                $matches,
                [
                    Matcher::ID,
                ]
            );
        }

        return [
            'field' => Matcher::ID,
            'value' => $data['match']['id'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function prepareMetadata(array $data): array
    {
        return [
            'login' => $data['username'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ?? null,
            'enabled' => $data['enabled'] ?? null,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function prepareFields(array $data): array
    {
        $fields = [];
        if (array_key_exists('first_name', $data)) {
            $fields[] = $this->prepareField(self::FIELD_IDENTIFIER_FIRST_NAME, $data['first_name']);
        }

        if (array_key_exists('last_name', $data)) {
            $fields[] = $this->prepareField(self::FIELD_IDENTIFIER_LAST_NAME, $data['last_name']);
        }

        return $fields;
    }

    /**
     * @return array<string, mixed> $context
     */
    private function prepareField(string $fieldDefIdentifier, string $value): array
    {
        return [
            'fieldDefIdentifier' => $fieldDefIdentifier,
            'fieldTypeIdentifier' => 'ezstring',
            'value' => $value,
        ];
    }
}

class_alias(UserUpdateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserUpdateDenormalizer');
