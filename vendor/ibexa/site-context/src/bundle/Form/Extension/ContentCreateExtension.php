<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Form\Extension;

use Ibexa\AdminUi\Form\Data\Content\Draft\ContentCreateData;
use Ibexa\AdminUi\Form\Type\Content\Draft\ContentCreateType;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ContentCreateExtension extends AbstractTypeExtension
{
    private SiteContextServiceInterface $siteAccessService;

    private LanguageService $languageService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        SiteContextServiceInterface $siteAccessService,
        LanguageService $languageService,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->languageService = $languageService;
        $this->configResolver = $configResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $data = $event->getData();
                if (!($data instanceof ContentCreateData)) {
                    return;
                }

                if ($data->getLanguage() !== null) {
                    return;
                }

                $context = $this->siteAccessService->getCurrentContext();
                if ($context !== null) {
                    $languages = $this->configResolver->getParameter('languages', null, $context->name);
                    if (!empty($languages)) {
                        $data->setLanguage($this->languageService->loadLanguage($languages[0]));
                    }
                }
            }
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentCreateType::class];
    }
}
