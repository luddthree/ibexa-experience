<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Templating;

use Ibexa\Bundle\VersionComparison\DependencyInjection\Configuration\Parser\FieldComparisonTemplates;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderRegistry;
use Twig\Environment;
use Twig\TemplateWrapper;

class ComparisonResultRenderer implements ComparisonResultRendererInterface
{
    protected const FIELD_COMPARISON_SUFFIX = '_field_comparison';

    /** @var \Twig\Environment */
    private $twig;

    /** @var string[]|\Twig\TemplateWrapper[] */
    private $comparisonBlockTemplatesResources;

    /** @var \Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderRegistry */
    private $parameterProviderRegistry;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    public function __construct(
        Environment $twig,
        ConfigResolverInterface $configResolver,
        ParameterProviderRegistry $parameterProviderRegistry,
        TranslationHelper $translationHelper
    ) {
        $this->twig = $twig;
        $this->comparisonBlockTemplatesResources = $this
            ->sortComparisonBlockTemplatesResources(
                $configResolver->getParameter(FieldComparisonTemplates::NODE_KEY)
            );
        $this->parameterProviderRegistry = $parameterProviderRegistry;
        $this->translationHelper = $translationHelper;
    }

    public function sortComparisonBlockTemplatesResources(array $resources)
    {
        usort($resources, static function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return array_column($resources, 'template');
    }

    public function renderContentFieldComparisonResult(
        Content $content,
        FieldDefinition $fieldDefinition,
        ComparisonResult $comparisonResult,
        array $parameters = []
    ): string {
        $blockName = $this->getValueBlockName($fieldDefinition);

        $localTemplate = null;
        if (isset($parameters['template'])) {
            $localTemplate = $parameters['template'];
            unset($parameters['template']);
        }

        $parameters['comparison_result'] = $comparisonResult;
        $parameters['field_definition'] = $fieldDefinition;
        $parameters['field_settings'] = $fieldDefinition->getFieldSettings();
        $parameters['content'] = $content;

        $template = $this->findTemplateWithBlock($blockName, $localTemplate);
        if ($template === null) {
            return '';
        }

        // Merging passed parameters to default ones
        $parameters += [
            'parameters' => [], // parameters dedicated to template processing
            'attr' => [], // attributes to add on the enclosing HTML tags
        ];

        $field = $this->translationHelper->getTranslatedField(
            $content,
            $fieldDefinition->identifier,
            $parameters['lang'] ?? null
        );

        $parameters['field'] = $field;

        if ($this->parameterProviderRegistry->hasParameterProvider($fieldDefinition->fieldTypeIdentifier)) {
            $parameters['parameters'] += $this->parameterProviderRegistry
                ->getParameterProvider($fieldDefinition->fieldTypeIdentifier)
                ->getViewParameters($field);
        }

        return $template->renderBlock($blockName, $parameters);
    }

    protected function getValueBlockName(FieldDefinition $fieldDefinition): string
    {
        return $fieldDefinition->fieldTypeIdentifier . self::FIELD_COMPARISON_SUFFIX;
    }

    protected function findTemplateWithBlock(string $blockName, ?string $localTemplate = null): ?TemplateWrapper
    {
        if ($localTemplate !== null) {
            if (is_string($localTemplate)) {
                $localTemplate = $this->twig->load($localTemplate);
            }

            if ($localTemplate->hasBlock($blockName)) {
                return $localTemplate;
            }
        }

        foreach ($this->comparisonBlockTemplatesResources as &$template) {
            if (is_string($template)) {
                // Load the template if it is necessary
                $template = $this->twig->load($template);
            }

            if ($template->hasBlock($blockName)) {
                return $template;
            }
        }

        return null;
    }
}

class_alias(ComparisonResultRenderer::class, 'EzSystems\EzPlatformVersionComparison\Templating\ComparisonResultRenderer');
