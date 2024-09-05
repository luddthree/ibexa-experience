<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Form\Type;

use Generator;
use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Bundle\SiteContext\Form\Choice\SiteContextChoiceView;
use Ibexa\Bundle\SiteContext\Form\Choice\SiteContextChoiceViewFactory;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\SiteFactory\SiteAccessProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SiteContextChoiceType extends AbstractType
{
    private SiteAccessResolverInterface $siteaccessResolver;

    private SiteAccessServiceInterface $siteAccessService;

    private ?SiteServiceInterface $siteService;

    private SiteContextChoiceViewFactory $choiceViewFactory;

    public function __construct(
        SiteAccessResolverInterface $siteAccessResolver,
        SiteAccessServiceInterface $siteAccessService,
        ?SiteServiceInterface $siteService,
        SiteContextChoiceViewFactory $choiceViewFactory
    ) {
        $this->siteaccessResolver = $siteAccessResolver;
        $this->siteAccessService = $siteAccessService;
        $this->siteService = $siteService;
        $this->choiceViewFactory = $choiceViewFactory;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $siteAccessMapping = $this->getSiteAccessMapping();

        /** @var \Symfony\Component\Form\ChoiceList\View\ChoiceView $choice */
        foreach ($view->vars['choices'] as $key => $choice) {
            /** @var \Ibexa\Core\MVC\Symfony\SiteAccess $siteaccess */
            $siteaccess = $choice->data;

            if ($siteaccess->provider === SiteAccessProvider::class) {
                $choice = $this->choiceViewFactory->createFromSite($choice, ...$siteAccessMapping[$siteaccess->name]);
            } else {
                $choice = $this->choiceViewFactory->createFromStaticSiteAccess($choice, $siteaccess);
            }

            $view->vars['choices'][$key] = $choice;
        }

        uasort($view->vars['choices'], [SiteContextChoiceView::class, 'compare']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => iterator_to_array($this->getChoices()),
            'choice_label' => 'name',
            'choice_value' => 'name',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * Return mapping between site access identifier to related site / public access pair.
     *
     * @return array<array-key, array{
     *     \Ibexa\Contracts\SiteFactory\Values\Site\Site,
     *     \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess
     * }>
     */
    private function getSiteAccessMapping(): array
    {
        $map = [];
        if ($this->siteService !== null) {
            $sites = $this->siteService->loadSites();
            foreach ($sites->getSites() as $site) {
                foreach ($site->publicAccesses as $publicAccess) {
                    $map[$publicAccess->identifier] = [$site, $publicAccess];
                }
            }
        }

        return $map;
    }

    /**
     * @return \Generator<\Ibexa\Core\MVC\Symfony\SiteAccess>
     */
    private function getChoices(): Generator
    {
        if ($this->siteService === null) {
            yield from $this->siteaccessResolver->getSiteAccessesList();

            return;
        }

        $sites = $this->siteService->loadSites();
        foreach ($sites->getSites() as $site) {
            foreach ($site->publicAccesses as $publicAccess) {
                try {
                    yield $this->siteAccessService->get($publicAccess->identifier);
                } catch (NotFoundException $e) {
                    // Ignored
                }
            }
        }

        $pages = $this->siteService->loadPages();
        foreach ($pages['items'] as $page) {
            try {
                yield $this->siteAccessService->get($page);
            } catch (NotFoundException $e) {
                // Ignored
            }
        }
    }
}
