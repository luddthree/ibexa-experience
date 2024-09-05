<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\UI\Component;

use Ibexa\Bundle\SiteContext\Form\Data\ChangeSiteContextData;
use Ibexa\Bundle\SiteContext\Form\Type\ChangeSiteContextType;
use Ibexa\Bundle\SiteContext\Specification\IsAdmin;
use Ibexa\Contracts\AdminUi\Component\Renderable;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\SiteContext\Specification\IsContextAware;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class SiteContextFormComponent implements Renderable
{
    private Environment $twig;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private SiteContextServiceInterface $siteContextService;

    private RequestStack $requestStack;

    private ConfigResolverInterface $configResolver;

    private SiteAccessServiceInterface $siteAccessService;

    private TranslatorInterface $translator;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        SiteContextServiceInterface $siteContextService,
        RequestStack $requestStack,
        ConfigResolverInterface $configResolver,
        SiteAccessServiceInterface $siteAccessService,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->siteContextService = $siteContextService;
        $this->requestStack = $requestStack;
        $this->configResolver = $configResolver;
        $this->siteAccessService = $siteAccessService;
        $this->translator = $translator;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return '';
        }

        $location = null;
        $view = $request->attributes->get('view');
        if ($view instanceof ContentView && $this->isAdminSiteAccess()) {
            $location = $view->getLocation();
        }

        return $this->twig->render(
            '@ibexadesign/site_context/form.html.twig',
            [
                'change_context_form' => $this->createChangeSiteContextForm($location)->createView(),
            ]
        );
    }

    /**
     * Returns true if current siteaccess is administrative.
     */
    private function isAdminSiteAccess(): bool
    {
        $siteAccess = $this->siteAccessService->getCurrent();
        if ($siteAccess === null) {
            return false;
        }

        return (new IsAdmin())->isSatisfiedBy($siteAccess);
    }

    private function createChangeSiteContextForm(?Location $location): FormInterface
    {
        if ($location !== null && $this->isContextAware($location)) {
            $options = [
                'method' => Request::METHOD_POST,
                'action' => $this->urlGenerator->generate(
                    'ibexa.site_context.change',
                    [
                        'locationId' => $location->id,
                    ]
                ),
            ];
        } else {
            $options = [
                'disabled' => true,
                'attr' => [
                    'title' => $this->translator->trans(
                        /** @Desc("Site context is active only when you are in the content structure.") */
                        'site_context.switcher.not_available.tooltip',
                        [],
                        'ibexa_site_context'
                    ),
                ],
            ];
        }

        return $this->formFactory->create(
            ChangeSiteContextType::class,
            new ChangeSiteContextData($this->siteContextService->getCurrentContext()),
            $options
        );
    }

    private function isContextAware(Location $location): bool
    {
        return IsContextAware::fromConfiguration($this->configResolver)->isSatisfiedBy($location);
    }
}
