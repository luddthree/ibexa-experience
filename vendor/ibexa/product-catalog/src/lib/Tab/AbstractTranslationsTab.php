<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractTranslationsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    protected FormFactoryInterface $formFactory;

    protected PermissionResolverInterface $permissionResolver;

    protected LanguageService $languageService;

    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        PermissionResolverInterface $permissionResolver,
        LanguageService $languageService,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->formFactory = $formFactory;
        $this->permissionResolver = $permissionResolver;
        $this->languageService = $languageService;
        $this->urlGenerator = $urlGenerator;
    }

    public function getIdentifier(): string
    {
        return 'translations';
    }

    public function getName(): string
    {
        /** @Desc("Translations") */
        return $this->translator->trans('tab.name.translations', [], 'ibexa_product_catalog');
    }

    public function getOrder(): int
    {
        return 200;
    }

    abstract public function getTemplate(): string;

    /**
     * @param array<string, mixed> $parameters
     */
    abstract public function evaluate(array $parameters): bool;
}
