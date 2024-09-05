<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

final class EditorContentData
{
    /** @var string|null */
    private $id;

    /** @var int|null */
    private $type;

    public function __construct(
        ?string $id = null,
        ?int $type = null
    ) {
        $this->id = $id;
        $this->type = $type;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): void
    {
        $this->type = $type;
    }
}

class_alias(EditorContentData::class, 'Ibexa\Platform\Personalization\Form\Data\EditorContentData');
