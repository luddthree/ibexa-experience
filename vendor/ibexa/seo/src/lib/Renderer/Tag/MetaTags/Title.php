<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Renderer\Tag\MetaTags;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Seo\Renderer\Tag\TagRendererInterface;
use Twig\Environment as TwigEnvironment;

final class Title implements TagRendererInterface
{
    public const TYPE = 'meta_tags';

    public const FIELD = 'title';

    private TwigEnvironment $twig;

    public function __construct(
        TwigEnvironment $twig
    ) {
        $this->twig = $twig;
    }

    public function support(string $type, string $field): bool
    {
        return $type === self::TYPE && $field === self::FIELD;
    }

    public function render(
        Content $content,
        string $type,
        string $field,
        string $value,
        array $fieldConfig
    ): string {
        return $this->twig->render('@ibexadesign/ibexa/seo/tag/title.html.twig', [
            'value' => $value,
        ]);
    }
}
