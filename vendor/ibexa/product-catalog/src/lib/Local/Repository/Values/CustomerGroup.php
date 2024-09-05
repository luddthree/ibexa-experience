<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use LogicException;

class CustomerGroup extends ValueObject implements CustomerGroupInterface, TranslatableInterface
{
    protected int $id;

    protected string $name;

    protected string $identifier;

    protected string $description;

    /**
     * @phpstan-var iterable<\Ibexa\Contracts\Core\Repository\Values\User\User>
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\User[]
     */
    protected iterable $users = [];

    /** @var array<int, string> */
    private array $languages;

    /** @var numeric-string */
    private string $globalPriceRate;

    /**
     * @param array<int, string> $languages
     * @param numeric-string $globalPriceRate
     */
    public function __construct(
        int $id,
        string $identifier,
        string $name,
        string $description,
        string $globalPriceRate,
        array $languages
    ) {
        parent::__construct();

        $this->id = $id;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->globalPriceRate = $globalPriceRate;
        $this->languages = $languages;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUsers(): iterable
    {
        return $this->users;
    }

    /**
     * @return numeric-string
     */
    public function getGlobalPriceRate(): string
    {
        return $this->globalPriceRate;
    }

    public function getFirstLanguage(): string
    {
        if (isset($this->languages[0])) {
            return $this->languages[0];
        }

        throw new LogicException(sprintf(
            '%s::$languages should contain at least one language entry',
            __CLASS__,
        ));
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }
}
