<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ObjectStateGroup;

use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;

final class CreateMetadata
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $mainTranslation;

    /** @var array<array<string>> */
    public $translations;

    /**
     * @param array<array<string>> $translations
     */
    private function __construct(
        string $identifier,
        string $mainTranslation,
        array $translations
    ) {
        $this->identifier = $identifier;
        $this->mainTranslation = $mainTranslation;
        $this->translations = $translations;
    }

    /**
     * @param array{
     *     identifier: string,
     *     mainTranslation: string,
     *     translations: array,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['identifier'],
            $data['mainTranslation'],
            $data['translations'],
        );
    }

    public static function createFromApi(ObjectStateGroup $objectState): self
    {
        return new self(
            $objectState->identifier,
            $objectState->mainLanguageCode,
            self::prepareTranslations($objectState)
        );
    }

    /**
     * @return array<array<string>>
     */
    private static function prepareTranslations(ObjectStateGroup $objectState): array
    {
        $translations = [];

        foreach ($objectState->getNames() as $lang => $value) {
            $translations[$lang]['name'] = $value;
        }

        foreach ($objectState->getDescriptions() as $lang => $value) {
            $translations[$lang]['description'] = $value;
        }

        return $translations;
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\ObjectStateGroup\CreateMetadata');
