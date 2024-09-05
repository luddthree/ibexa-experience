<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Renderer\Tag;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Seo\Renderer\Tag\TagRendererInterface;
use Twig\Environment as TwigEnvironment;

final class DynamicType implements TagRendererInterface
{
    private TwigEnvironment $twig;

    private string $type;

    private string $field;

    private string $propertyName;

    public function __construct(
        TwigEnvironment $twig,
        string $type,
        string $field,
        string $propertyName
    ) {
        $this->twig = $twig;
        $this->type = $type;
        $this->field = $field;
        $this->propertyName = $propertyName;
    }

    public function support(string $type, string $field): bool
    {
        return $type === $this->type && $field === $this->field;
    }

    public function render(
        Content $content,
        string $type,
        string $field,
        string $value,
        array $fieldConfig
    ): string {
        return $this->twig->render('@ibexadesign/ibexa/seo/tag/dynamic_type.html.twig', [
            'property_name' => $this->propertyName,
            'field' => $fieldConfig['key'] ?? $this->field,
            'value' => $value,
        ]);
    }
}
