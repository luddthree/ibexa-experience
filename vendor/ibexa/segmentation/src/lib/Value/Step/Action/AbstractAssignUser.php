<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step\Action;

use Ibexa\Migration\ValueObject\Step\Action;
use InvalidArgumentException;

abstract class AbstractAssignUser implements Action
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $email;

    /** @var string|null */
    private $login;

    public function __construct(?int $id = null, ?string $email = null, ?string $login = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->login = $login;

        if ($this->id === null && $this->email === null && $this->login === null) {
            throw new InvalidArgumentException('"id", "email" or "login" argument must not be null');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @return array{
     *     'id': int,
     * }
     */
    public function getValue(): array
    {
        assert($this->id !== null);

        return [
            'id' => $this->id,
        ];
    }
}
