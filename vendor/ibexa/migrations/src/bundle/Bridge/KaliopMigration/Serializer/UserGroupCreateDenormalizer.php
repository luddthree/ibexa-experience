<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\Action\UserGroup\AssignRole;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class UserGroupCreateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    protected function getHandledKaliopType(): string
    {
        return 'user_group';
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
        $lang = $data['lang'] ?? $this->languageService->getDefaultLanguageCode();

        $convertedData = [
            'type' => 'user_group',
            'mode' => Mode::CREATE,
            'metadata' => $this->prepareMetadata($data, $lang),
            'fields' => $this->prepareFields($data, $lang),
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
    private function prepareMetadata(array $data, string $lang): array
    {
        return [
            'contentTypeIdentifier' => 'user_group',
            'mainLanguage' => $lang,
            'parentGroupId' => $data['parent_group_id'],
            'remoteId' => $data['remote_id'] ?? null,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<array<string, mixed>>
     */
    private function prepareFields(array $data, string $lang): array
    {
        $fields = [];

        if (array_key_exists('name', $data)) {
            $fields[] = $this->prepareField('name', $data['name'], $lang);
        }

        if (array_key_exists('description', $data)) {
            $fields[] = $this->prepareField('description', $data['description'], $lang);
        }

        return $fields;
    }

    /**
     * @return array<string, mixed> $context
     */
    private function prepareField(string $identifier, string $value, string $lang): array
    {
        return [
            'fieldDefIdentifier' => $identifier,
            'fieldTypeIdentifier' => 'ezstring',
            'languageCode' => $lang,
            'value' => $value,
        ];
    }
}

class_alias(UserGroupCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupCreateDenormalizer');
