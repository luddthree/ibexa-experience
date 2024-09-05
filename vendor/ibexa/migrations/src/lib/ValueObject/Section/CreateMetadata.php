<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Section;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;

final class CreateMetadata
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $name;

    private function __construct(
        string $identifier,
        string $name
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * @param array{
     *     identifier: string,
     *     name: string,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['identifier'],
            $data['name']
        );
    }

    public static function createFromApi(Section $section): self
    {
        return new self(
            $section->identifier,
            $section->name
        );
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\Section\CreateMetadata');
