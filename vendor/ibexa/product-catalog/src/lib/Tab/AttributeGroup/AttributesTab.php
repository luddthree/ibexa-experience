<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\AttributeGroup;

use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionSearchType;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\AttributeGroupIdentifierCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\AttributeDefinitionListAdapter;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

class AttributesTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-attribute-group-attributes';

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $router,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $router;
        $this->configResolver = $configResolver;
    }

    public function getIdentifier(): string
    {
        return 'attributes';
    }

    public function getName(): string
    {
        /** @Desc("Attributes") */
        return $this->translator->trans('tab.name.attributes', [], 'ibexa_product_catalog');
    }

    public function getOrder(): int
    {
        return 100;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/attribute_group/tab/attributes.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $attributeGroup = $contextParameters['attribute_group'];
        Assert::isInstanceOf($attributeGroup, AttributeGroupInterface::class);

        $request = $contextParameters['request'];
        Assert::isInstanceOf($request, Request::class);

        Assert::boolean($contextParameters['is_editable']);

        $query = new AttributeDefinitionQuery();
        $attributeGroupIdentifier = $attributeGroup->getIdentifier();

        $searchForm = $this->createSearchForm($attributeGroupIdentifier);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $nameQuery = $searchForm->getData()->getQuery();
            if (!empty($nameQuery)) {
                $query->and(new NameCriterion(
                    $searchForm->getData()->getQuery(),
                    FieldValueCriterion::COMPARISON_STARTS_WITH,
                ));
            }
        }

        $query->and(new AttributeGroupIdentifierCriterion($attributeGroupIdentifier));

        $adapter = new AttributeDefinitionListAdapter(
            $this->attributeDefinitionService,
            $query,
        );

        $attributesDefinitions = new Pagerfanta($adapter);
        $attributesDefinitions->setMaxPerPage($this->configResolver->getParameter(
            'product_catalog.pagination.attribute_definitions_limit'
        ));
        $attributesDefinitions->setCurrentPage($request->query->getInt('page', 1));

        $viewParameters = [
            'is_editable' => $contextParameters['is_editable'],
            'attributes_definitions' => $attributesDefinitions,
            'attribute_group' => $attributeGroup,
            'search_form' => $searchForm->createView(),
            'bulk_delete_form' => $this->createBulkDeleteForm()->createView(),
            'no_attributes' => $this->isMissingAtLeastOneAttribute($attributeGroup->getIdentifier()),
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    private function createSearchForm(string $attributeGroupIdentifier): FormInterface
    {
        return $this->formFactory->create(AttributeDefinitionSearchType::class, null, [
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.attribute_group.view', [
                'attributeGroupIdentifier' => $attributeGroupIdentifier,
                '_fragment' => self::URI_FRAGMENT,
            ]),
            'csrf_protection' => false,
            'method' => Request::METHOD_GET,
        ]);
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(AttributeDefinitionBulkDeleteType::class, null, [
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.attribute_definition.bulk_delete'),
            'method' => Request::METHOD_POST,
        ]);
    }

    private function isMissingAtLeastOneAttribute(string $attributeGroupIdentifier): bool
    {
        $query = new AttributeDefinitionQuery(
            new AttributeGroupIdentifierCriterion($attributeGroupIdentifier)
        );
        $query->setLimit(0);

        $attributeDefinitionList = $this->attributeDefinitionService->findAttributesDefinitions($query);

        return $attributeDefinitionList->getTotalCount() === 0;
    }
}
