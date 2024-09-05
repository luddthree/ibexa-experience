<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class AttributeGroupUpdateStruct extends ValueObject
{
    private ?string $identifier;

    /** @var array<string,string> */
    private array $names;

    private ?int $position;

    /** @var string[] */
    private array $languages;

    /**
     * @param array<string,string> $names
     * @param array<string> $languages
     */
    public function __construct(array $names, ?string $identifier, ?int $position, array $languages)
    {
        parent::__construct();

        $this->names = $names;
        $this->identifier = $identifier;
        $this->position = $position;
        $this->languages = $languages;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return array<string,string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}
