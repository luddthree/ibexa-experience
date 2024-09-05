<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Renderer;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Workflow\Exception\MissingMatcherBlockException;
use Ibexa\Workflow\Exception\ValueMapperNotFoundException;
use Ibexa\Workflow\Registry\MatcherValueMapperRegistryInterface;
use Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata;
use Twig\Environment;
use Twig\TemplateWrapper;

class MatcherBlockRenderer implements MatcherBlockRendererInterface
{
    private const MATCHER_VALUE_BLOCK_NAME = 'ez_workflow_matcher_%s_value';
    private const MATCHER_VALUE_BLOCK_NAME_FALLBACK = 'ez_workflow_matcher_value_fallback';

    /** @var \Ibexa\Workflow\Registry\MatcherValueMapperRegistryInterface */
    private $valueMapperRegistry;

    /** @var \Twig\Environment */
    private $twig;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        MatcherValueMapperRegistryInterface $valueMapperRegistry,
        Environment $twig,
        ConfigResolverInterface $configResolver
    ) {
        $this->valueMapperRegistry = $valueMapperRegistry;
        $this->twig = $twig;
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    public function renderMatcherValue(
        string $identifier,
        WorkflowMatcherDefinitionMetadata $matcherDefinitionMetadata,
        array $parameters = []
    ): string {
        try {
            $blockName = $this->getValueBlockName($identifier);
            $parameters = $this->getValueBlockParameters($identifier, $matcherDefinitionMetadata, $parameters);
        } catch (ValueMapperNotFoundException $exception) {
            $blockName = self::MATCHER_VALUE_BLOCK_NAME_FALLBACK;
            $parameters = $this->getValueFallbackBlockParameters($identifier, $matcherDefinitionMetadata, $parameters);
        }

        $localTemplate = null;
        if (isset($parameters['template'])) {
            $localTemplate = $parameters['template'];
            unset($parameters['template']);
        }

        $template = $this->findTemplateWithBlock($blockName, $localTemplate);
        if ($template === null) {
            throw new MissingMatcherBlockException("Could not find block for {$identifier}: $blockName.");
        }

        return $template->renderBlock($blockName, $parameters);
    }

    /**
     * Generates value block name based on matcher identifier.
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function getValueBlockName(string $identifier): string
    {
        return sprintf(self::MATCHER_VALUE_BLOCK_NAME, strtolower($identifier));
    }

    /**
     * Find the first template containing block definition $blockName.
     *
     * @param string $blockName
     * @param string|\Twig\Template $localTemplate
     *
     * @return \Twig\TemplateWrapper|string|null
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function findTemplateWithBlock(string $blockName, $localTemplate = null): ?TemplateWrapper
    {
        if ($localTemplate !== null) {
            if (\is_string($localTemplate)) {
                $localTemplate = $this->twig->load($localTemplate);
            }

            if ($localTemplate->hasBlock($blockName)) {
                return $localTemplate;
            }
        }

        foreach ($this->getMatcherValueResources() as &$template) {
            if (\is_string($template)) {
                // Load the template if it is necessary
                $template = $this->twig->load($template);
            }

            if ($template->hasBlock($blockName)) {
                return $template;
            }
        }

        return null;
    }

    /**
     * Get parameters passed as context of value block render.
     *
     * @param string $identifier
     * @param \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata $workflowMatcherDefinitionMetadata
     * @param array $parameters
     *
     * @return array
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    protected function getValueBlockParameters(
        string $identifier,
        WorkflowMatcherDefinitionMetadata $workflowMatcherDefinitionMetadata,
        array $parameters
    ): array {
        /** @var \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata $value */
        $matcherValues = $workflowMatcherDefinitionMetadata->getConfiguration();
        $matcherValues = \is_array($matcherValues) ? $matcherValues : [$matcherValues];

        $values = $this->valueMapperRegistry
            ->get($identifier)
            ->mapMatcherValue($matcherValues);

        $parameters += [
            'matcher' => $identifier,
            'values' => $values,
        ];

        return $parameters;
    }

    /**
     * Get parameters passed as context of value fallback block.
     *
     * @param string $identifier
     * @param \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata $workflowMatcherDefinitionMetadata
     * @param array $parameters
     *
     * @return array
     */
    protected function getValueFallbackBlockParameters(
        string $identifier,
        WorkflowMatcherDefinitionMetadata $workflowMatcherDefinitionMetadata,
        array $parameters
    ): array {
        /** @var \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata $value */
        $limitations = $workflowMatcherDefinitionMetadata->getConfiguration();
        $limitations = \is_array($limitations) ? $limitations : [$limitations];

        $parameters += [
            'matcher' => $identifier,
            'values' => $limitations,
        ];

        return $parameters;
    }

    /**
     * @return string[]
     */
    protected function getMatcherValueResources(): array
    {
        $resources = $this->configResolver->getParameter('workflows_config.matcher_value_templates');

        usort($resources, static function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return array_column($resources, 'template');
    }
}

class_alias(MatcherBlockRenderer::class, 'EzSystems\EzPlatformWorkflow\Renderer\MatcherBlockRenderer');
