<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values\Catalog;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\MatchAll;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use LogicException;

final class Catalog extends ValueObject implements CatalogInterface, TranslatableInterface
{
    protected int $id;

    protected string $name;

    protected ?string $description;

    protected string $identifier;

    /** @var array<int, string> */
    private array $languages;

    protected User $creator;

    protected int $created;

    protected int $modified;

    protected string $status;

    protected ?CriterionInterface $query;

    /**
     * @param array<int, string> $languages
     */
    public function __construct(
        int $id,
        string $identifier,
        string $name,
        array $languages,
        User $user,
        int $created,
        int $modified,
        string $status,
        ?CriterionInterface $query,
        ?string $description = null
    ) {
        parent::__construct();

        $this->id = $id;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->languages = $languages;
        $this->creator = $user;
        $this->created = $created;
        $this->modified = $modified;
        $this->status = $status;
        $this->query = $query;
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

    public function getDescription(): ?string
    {
        return $this->description;
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

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getModified(): int
    {
        return $this->modified;
    }

    public function getQuery(): CriterionInterface
    {
        return $this->query ?? new LogicalAnd([new MatchAll()]);
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
