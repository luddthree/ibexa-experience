<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\DependencyInjection\Compiler;

use Ibexa\VersionComparison\Engine\Value\Html\ExternalHtmlDiffHandler;
use Ibexa\VersionComparison\Engine\Value\Html\HtmlDiffHandler;
use Ibexa\VersionComparison\Engine\Value\Html\PlainTextHtmlDiffHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class HtmlDiffHandlerCompilerPass implements CompilerPassInterface
{
    public const DEFAULT_METHOD = 'default';
    public const EXTERNAL_TOOL_METHOD = 'external_tool';
    public const PLAIN_TEXT_METHOD = 'plain_text';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ezplatform.version_compare.html.method')) {
            return;
        }
        $method = $container->getParameter('ezplatform.version_compare.html.method');

        if ($method === self::DEFAULT_METHOD) {
            return;
        }

        if ($method === self::EXTERNAL_TOOL_METHOD) {
            $container->setAlias(
                HtmlDiffHandler::class,
                ExternalHtmlDiffHandler::class
            );

            return;
        }

        if ($method === self::PLAIN_TEXT_METHOD) {
            $container->setAlias(
                HtmlDiffHandler::class,
                PlainTextHtmlDiffHandler::class
            );

            return;
        }
    }
}

class_alias(HtmlDiffHandlerCompilerPass::class, 'EzSystems\EzPlatformVersionComparisonBundle\DependencyInjection\Compiler\HtmlDiffHandlerCompilerPass');
