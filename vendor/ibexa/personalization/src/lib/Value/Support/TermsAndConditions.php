<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Support;

final class TermsAndConditions
{
    /** @var int */
    private $version;

    /** @var string */
    private $footer;

    /** @var string */
    private $header;

    /** @var string */
    private $copyright;

    /** @var array */
    private $items;

    public function __construct(
        int $version,
        string $footer,
        string $header,
        string $copyright,
        array $items
    ) {
        $this->version = $version;
        $this->footer = $footer;
        $this->header = $header;
        $this->copyright = $copyright;
        $this->items = $items;
    }

    public static function fromArray(array $properties)
    {
        return new self(
            $properties['version'],
            $properties['footer'],
            $properties['header'] ?? '',
            $properties['copyright'],
            $properties['items']
        );
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getFooter(): string
    {
        return $this->footer;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getCopyright(): string
    {
        return $this->copyright;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}

class_alias(TermsAndConditions::class, 'Ibexa\Platform\Personalization\Value\Support\TermsAndConditions');
