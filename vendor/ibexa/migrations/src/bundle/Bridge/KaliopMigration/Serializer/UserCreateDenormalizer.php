<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\Action\User\AssignRole;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class UserCreateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const USER_CONTENT_TYPE = 'user';
    private const FIELD_IDENTIFIER_FIRST_NAME = 'first_name';
    private const FIELD_IDENTIFIER_LAST_NAME = 'last_name';

    protected function getHandledKaliopType(): string
    {
        return 'user';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'create';
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
            'metadata' => $this->prepareMetadata($data),
            'groups' => $data['groups'] ?? [],
            'fields' => [
                $this->prepareField(self::FIELD_IDENTIFIER_FIRST_NAME, $data['first_name']),
                $this->prepareField(self::FIELD_IDENTIFIER_LAST_NAME, $data['last_name']),
            ],
        ];

        if (isset($data['roles'])) {
            Assert::isArray($data['roles']);
            $actions = $this->denormalizer->denormalize($data['roles'], AssignRole::class, $format, $context);
            $convertedData['actions'] = $actions;
        }

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
    private function prepareMetadata(array $data): array
    {
        return [
            'login' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'contentType' => self::USER_CONTENT_TYPE,
        ];
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

class_alias(UserCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserCreateDenormalizer');
