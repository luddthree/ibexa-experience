<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer;

/**
 * This interface tags the class as a RenderRequest which means in should carry the data needed to render a template.
 *
 * Best example is TwigRenderRequest class which handles rendering using Twig engine.
 */
interface RenderRequestInterface
{
}

class_alias(RenderRequestInterface::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\RenderRequestInterface');
