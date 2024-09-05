<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\UI\Tabs;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\SiteContext\Specification\IsContextAware;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PreviewTab extends AbstractEventDispatchingTab implements ConditionalTabInterface, OrderedTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-location-view-preview';

    private SiteContextServiceInterface $siteContextService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        SiteContextServiceInterface $siteContextService,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->siteContextService = $siteContextService;
        $this->configResolver = $configResolver;
    }

    public function getIdentifier(): string
    {
        return 'preview';
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("View") */
            'tab.preview.name',
            [],
            'ibexa_site_context'
        );
    }

    public function getOrder(): int
    {
        return 110;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        if ($this->siteContextService->getCurrentContext() === null) {
            return false;
        }

        $location = $parameters['location'] ?? null;
        if (!$location instanceof Location) {
            return false;
        }

        return IsContextAware::fromConfiguration($this->configResolver)->isSatisfiedBy($location);
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/content/tab/preview.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];

        $currentContext = $this->siteContextService->getCurrentContext();
        if ($currentContext !== null) {
            $contextParameters['language'] = $this->siteContextService->resolveContextLanguage($currentContext, $content);
            $contextParameters['siteaccess'] = $currentContext->name;
        }

        return $contextParameters;
    }
}
