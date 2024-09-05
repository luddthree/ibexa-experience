<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateAccountData
{
    /**
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     * )
     */
    private ?string $name;

    private ?string $type;

    public function __construct(
        ?string $name = null,
        ?string $type = null
    ) {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
