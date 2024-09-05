<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter;

class ReferenceLoadDenormalizer extends AbstractDenormalizer
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter */
    private $referenceFileConverter;

    public function __construct(ReferenceFileConverter $referenceFileConverter)
    {
        $this->referenceFileConverter = $referenceFileConverter;
    }

    protected function getHandledKaliopType(): string
    {
        return 'reference';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'load';
    }

    /**
     * @param array<mixed> $data
     * @param array<string, mixed> $context
     *
     * @return array{
     *     type: 'reference',
     *     mode: 'load',
     *     filename: string,
     * }
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $fileName = $this->referenceFileConverter->convert($data['file'], $context['output']);

        return [
            'type' => 'reference',
            'mode' => 'load',
            'filename' => $fileName,
        ];
    }
}

class_alias(ReferenceLoadDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceLoadDenormalizer');
