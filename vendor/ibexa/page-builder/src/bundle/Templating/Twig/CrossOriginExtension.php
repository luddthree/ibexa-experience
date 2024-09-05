<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Templating\Twig;

use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\PageBuilder\Siteaccess\ReverseMatcher;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CrossOriginExtension extends AbstractExtension
{
    /** @var \Ibexa\PageBuilder\Siteaccess\ReverseMatcher */
    private $reverseMatcher;

    /** @var \Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /**
     * @param \Ibexa\PageBuilder\Siteaccess\ReverseMatcher $reverseMatcher
     * @param \Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface $configResolver
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess $siteaccess
     */
    public function __construct(
        ReverseMatcher $reverseMatcher,
        ConfigurationResolverInterface $configResolver,
        SiteAccess $siteaccess
    ) {
        $this->reverseMatcher = $reverseMatcher;
        $this->configResolver = $configResolver;
        $this->siteaccess = $siteaccess;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_page_builder_cross_origin_helper',
                [$this, 'renderSnippet'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_page_builder_cross_origin_helper',
                ]
            ),
            new TwigFunction(
                'ibexa_page_builder_cross_origin_helper',
                [$this, 'renderSnippet'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param \Twig\Environment $environment
     * @param array $hosts
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderSnippet(Environment $environment, array $hosts = []): string
    {
        if (1 === count($hosts) || !$this->reverseMatcher->isMultiDomain()) {
            return '';
        }

        $adminSiteaccesses = $this->configResolver->reverseAdminSiteaccessMatch($this->siteaccess->name);
        if (empty($adminSiteaccesses)) {
            return '';
        }

        if (empty($hosts)) {
            $hosts = array_map([$this->reverseMatcher, 'getSchemeAndHttpHost'], $adminSiteaccesses);
        }

        return $environment->render(
            '@IbexaPageBuilder/cross_origin_helper/snippet.html.twig',
            ['hosts' => $hosts]
        );
    }
}

class_alias(CrossOriginExtension::class, 'EzSystems\EzPlatformPageBuilderBundle\Templating\Twig\CrossOriginExtension');
