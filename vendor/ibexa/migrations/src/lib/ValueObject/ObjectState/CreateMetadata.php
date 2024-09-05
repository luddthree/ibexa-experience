<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ObjectState;

use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;

final class CreateMetadata
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $mainTranslation;

    /** @var int|string */
    public $objectStateGroup;

    /** @var int|bool */
    public $priority;

    /** @var array<array<string>> */
    public $translations;

    /**
     * @param int|string $objectStateGroup
     * @param int|bool $priority
     * @param array<array<string>> $translations
     */
    private function __construct(
        string $identifier,
        string $mainTranslation,
        $objectStateGroup,
        $priority,
        array $translations
    ) {
        $this->identifier = $identifier;
        $this->mainTranslation = $mainTranslation;
        $this->objectStateGroup = $objectStateGroup;
        $this->priority = $priority;
        $this->translations = $translations;
    }

    /**
     * @param array{
     *     identifier: string,
     *     mainTranslation: string,
     *     objectStateGroup: int|string,
     *     priority?: int|bool,
     *     translations: array,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['identifier'],
            $data['mainTranslation'],
            $data['objectStateGroup'],
            $data['priority'] ?? false,
            $data['translations'],
        );
    }

    public static function createFromApi(ObjectState $objectState): self
    {
        return new self(
            $objectState->identifier,
            $objectState->mainLanguageCode,
            $objectState->getObjectStateGroup()->id,
            $objectState->priority,
            self::prepareTranslations($objectState)
        );
    }

    /**
     * @return array<array<string>>
     */
    private static function prepareTranslations(ObjectState $objectState): array
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

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\ObjectState\CreateMetadata');
