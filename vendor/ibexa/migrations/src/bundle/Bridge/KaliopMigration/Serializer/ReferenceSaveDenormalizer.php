<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

class ReferenceSaveDenormalizer extends AbstractDenormalizer
{
    /** @var string */
    private $referencesFilesDirName;

    public function __construct(string $referencesFilesDirName)
    {
        $this->referencesFilesDirName = $referencesFilesDirName;
    }

    protected function getHandledKaliopType(): string
    {
        return 'reference';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'save';
    }

    /**
     * @param array<mixed> $data
     * @param array<string, mixed> $context
     *
     * @return array{
     *     type: 'reference',
     *     mode: 'save',
     *     filename: string,
     * }
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $fileName = pathinfo($data['file'], PATHINFO_FILENAME) . '.' . pathinfo($data['file'], PATHINFO_EXTENSION);

        return [
            'type' => 'reference',
            'mode' => 'save',
            'filename' => $this->referencesFilesDirName . \DIRECTORY_SEPARATOR . $fileName,
        ];
    }
}

class_alias(ReferenceSaveDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceSaveDenormalizer');
