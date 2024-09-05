<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Templating\Twig;

use Ibexa\Workflow\Renderer\MatcherBlockRendererInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MatcherValueRenderingExtension extends AbstractExtension
{
    /** @var \Ibexa\Workflow\Renderer\MatcherBlockRendererInterface */
    private $matcherRenderer;

    /**
     * @param \Ibexa\Workflow\Renderer\MatcherBlockRendererInterface $matcherRenderer
     */
    public function __construct(MatcherBlockRendererInterface $matcherRenderer)
    {
        $this->matcherRenderer = $matcherRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $workflowMatcherValueCallable = function (Environment $twig, $identifier, $matcherDefinitionMetadata, array $params = []) {
            return $this->matcherRenderer->renderMatcherValue($identifier, $matcherDefinitionMetadata, $params);
        };

        return [
            new TwigFunction(
                'ez_render_workflow_matcher_value',
                $workflowMatcherValueCallable,
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_render_workflow_matcher_value',
                ]
            ),
            new TwigFunction(
                'ibexa_render_workflow_matcher_value',
                $workflowMatcherValueCallable,
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }
}

class_alias(MatcherValueRenderingExtension::class, 'EzSystems\EzPlatformWorkflowBundle\Templating\Twig\MatcherValueRenderingExtension');
