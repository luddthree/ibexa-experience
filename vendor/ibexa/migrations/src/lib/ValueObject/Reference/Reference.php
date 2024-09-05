<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Reference;

use Ibexa\Migration\ValueObject\ValueObjectInterface;
use Webmozart\Assert\Assert;

final class Reference implements ValueObjectInterface
{
    private const NAME_PATTERN = '/[a-z0-1\_\-]{1,}/i';

    /** @var string */
    private $name;

    /** @var string|int */
    private $value;

    private function __construct()
    {
    }

    /**
     * @param string $name
     * @param string|int $value
     */
    public static function create(string $name, $value): self
    {
        Assert::regex(
            $name,
            self::NAME_PATTERN,
            'The name should contain only alphanumeric, underscore ( _ ) or dash ( – ) characters.'
        );
        Assert::notNull($value);

        $vo = new self();
        $vo->name = $name;
        $vo->value = $value;

        return $vo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }
}

class_alias(Reference::class, 'Ibexa\Platform\Migration\ValueObject\Reference\Reference');
