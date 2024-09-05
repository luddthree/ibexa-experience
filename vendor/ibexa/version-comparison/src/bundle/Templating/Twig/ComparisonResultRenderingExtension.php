<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Templating\Twig;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Templating\ComparisonResultRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ComparisonResultRenderingExtension extends AbstractExtension
{
    /** @var \Ibexa\VersionComparison\Templating\ComparisonResultRendererInterface */
    private $comparisonBlockRenderer;

    public function __construct(ComparisonResultRendererInterface $comparisonBlockRenderer)
    {
        $this->comparisonBlockRenderer = $comparisonBlockRenderer;
    }

    public function getFunctions()
    {
        $comparisonCallable = function (
            Content $content,
            FieldDefinition $fieldDefinition,
            ComparisonResult $comparisonResult,
            array $params = []
        ) {
            return $this->comparisonBlockRenderer->renderContentFieldComparisonResult(
                $content,
                $fieldDefinition,
                $comparisonResult,
                $params
            );
        };

        return [
            new TwigFunction(
                'ez_render_comparison_result',
                $comparisonCallable,
                [
                    'is_safe' => ['html'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_render_comparison_result',
                ]
            ),
            new TwigFunction(
                'ibexa_render_comparison_result',
                $comparisonCallable,
                ['is_safe' => ['html']]
            ),
        ];
    }
}

class_alias(ComparisonResultRenderingExtension::class, 'EzSystems\EzPlatformVersionComparisonBundle\Templating\Twig\ComparisonResultRenderingExtension');
