<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Form\Choice;

use Ibexa\AdminUi\Siteaccess\SiteAccessNameGeneratorInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

final class SiteContextChoiceViewFactory
{
    private SiteAccessNameGeneratorInterface $siteAccessNameGenerator;

    private LanguageService $languageService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        SiteAccessNameGeneratorInterface $siteAccessNameGenerator,
        LanguageService $languageService,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteAccessNameGenerator = $siteAccessNameGenerator;
        $this->languageService = $languageService;
        $this->configResolver = $configResolver;
    }

    public function createFromStaticSiteAccess(
        ChoiceView $view,
        SiteAccess $siteAccess
    ): SiteContextChoiceView {
        /** @var string[] $languages */
        $languages = $this->configResolver->getParameter('languages', null, $siteAccess->name);

        $decorated = SiteContextChoiceView::decorate($view);
        $decorated->label = $this->siteAccessNameGenerator->generate($siteAccess);
        $decorated->language = $this->languageService->loadLanguage($languages[0]);
        $decorated->thumbnail = null;

        return $decorated;
    }

    public function createFromSite(
        ChoiceView $view,
        Site $site,
        PublicAccess $publicAccess
    ): SiteContextChoiceView {
        $decorated = SiteContextChoiceView::decorate($view);
        $decorated->label = $site->name;
        $decorated->thumbnail = $site->template->thumbnail;
        $decorated->language = $this->languageService->loadLanguage(
            $publicAccess->getMainLanguage()
        );

        return $decorated;
    }
}
