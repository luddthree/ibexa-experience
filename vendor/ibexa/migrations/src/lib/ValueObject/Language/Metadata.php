<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Language;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Webmozart\Assert\Assert;

final class Metadata
{
    /** @var string */
    public $languageCode;

    /** @var string */
    public $name;

    /** @var bool */
    public $enabled;

    public function __construct(
        string $languageCode,
        string $name,
        bool $enabled
    ) {
        $this->languageCode = $languageCode;
        $this->name = $name;
        $this->enabled = $enabled;
    }

    public static function create(Language $language): self
    {
        return new self(
            $language->languageCode,
            $language->name,
            $language->enabled
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        Assert::keyExists($data, 'languageCode');
        Assert::string($data['languageCode']);

        Assert::keyExists($data, 'name');
        Assert::string($data['name']);

        Assert::keyExists($data, 'enabled');
        Assert::boolean($data['enabled']);

        return new self(
            $data['languageCode'],
            $data['name'],
            $data['enabled']
        );
    }
}

class_alias(Metadata::class, 'Ibexa\Platform\Migration\ValueObject\Language\Metadata');
