<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\FieldTypePage\Twig;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    /** @var \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry */
    protected $layoutDefinitionRegistry;

    /**
     * @param \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry $layoutDefinitionRegistry
     */
    public function __construct(LayoutDefinitionRegistry $layoutDefinitionRegistry)
    {
        $this->layoutDefinitionRegistry = $layoutDefinitionRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_page_layout',
                [$this, 'getPageLayout'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_page_layout',
                ]
            ),
            new TwigFunction(
                'ibexa_page_layout',
                [$this, 'getPageLayout']
            ),
        ];
    }

    /**
     * Returns path to page template.
     *
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     *
     * @return string
     */
    public function getPageLayout(Page $page)
    {
        $layoutDefinition = $this->layoutDefinitionRegistry->getLayoutDefinitionById($page->getLayout());

        return $layoutDefinition->getTemplate();
    }
}

class_alias(LayoutExtension::class, 'EzSystems\EzPlatformPageFieldTypeBundle\Twig\LayoutExtension');
