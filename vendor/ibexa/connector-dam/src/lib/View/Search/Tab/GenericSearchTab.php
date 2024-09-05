<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\View\Search\Tab;

use Ibexa\Contracts\AdminUi\Tab\AbstractTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class GenericSearchTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    /** @var string */
    protected $identifier;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetSource */
    protected $source;

    /** @var string */
    protected $name;

    /** @var string */
    protected $searchFormType;

    /** @var \Symfony\Component\Form\FormFactory */
    protected $formFactory;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    protected $configResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        string $identifier,
        string $source,
        string $name,
        string $searchFormType,
        FormFactory $formFactory,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($twig, $translator);
        $this->source = $source;
        $this->name = $name;
        $this->formFactory = $formFactory;
        $this->searchFormType = $searchFormType;
        $this->identifier = $identifier;
        $this->configResolver = $configResolver;
    }

    public function getOrder(): int
    {
        return 1;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return /** @Ignore */ $this->translator->trans($this->name);
    }

    public function renderView(array $parameters): string
    {
        return $this->twig->render('@ibexadesign/ui/modal/generic_form.html.twig', [
            'form' => $this->getForm()->createView(),
            'source' => $this->source,
        ]);
    }

    public function evaluate(array $parameters): bool
    {
        $enabledConnectors = $this->configResolver->getParameter('content.dam');

        return \in_array($this->source, $enabledConnectors, true);
    }

    protected function getForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            $this->getIdentifier() . '-form',
            $this->searchFormType,
            null,
            [
                'csrf_protection' => false,
            ]
        );
    }
}

class_alias(GenericSearchTab::class, 'Ibexa\Platform\Connector\Dam\View\Search\Tab\GenericSearchTab');
