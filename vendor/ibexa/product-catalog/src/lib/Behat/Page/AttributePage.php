<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Webmozart\Assert\Assert;

final class AttributePage extends Page
{
    private string $expectedAttributeIdentifier;

    private string $expectedAttributeName;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private Dialog $dialog;

    public function __construct(Session $session, Router $router, Dialog $dialog, AttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        parent::__construct($session, $router);
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->dialog = $dialog;
    }

    public function verifyAttribute(string $attributeName, string $attributeIdentifier, string $attributeDescription, string $attributeType, string $attributeGroup, string $attributePosition): void
    {
        $this->getHTMLPage()->find($this->getLocator('attributeInformationName'))->assert()->textEquals($attributeName);
        $this->getHTMLPage()->find($this->getLocator('attributeInformationIdentifier'))->assert()->textEquals($attributeIdentifier);
        $this->getHTMLPage()->find($this->getLocator('attributeInformationDescription'))->assert()->textEquals($attributeDescription);
        $this->getHTMLPage()->find($this->getLocator('attributeInformationType'))->assert()->textEquals($attributeType);
        $this->getHTMLPage()->find($this->getLocator('attributeInformationGroup'))->assert()->textEquals($attributeGroup);
        $this->getHTMLPage()->find($this->getLocator('attributeInformationPosition'))->assert()->textEquals($attributePosition);
    }

    public function deleteAttribute(): void
    {
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function setExpectedAttributeName(string $attributeName): void
    {
        $this->expectedAttributeName = $attributeName;
        $query = new AttributeDefinitionQuery();
        $query->and(new NameCriterion($attributeName));
        $attributes = $this->attributeDefinitionService->findAttributesDefinitions($query)->getAttributeDefinitions();
        Assert::notEmpty($attributes, 'Attribute not found');
        $attribute = current($attributes);
        Assert::eq($attribute->getName(), $attributeName);
        $this->expectedAttributeIdentifier = $attribute->getIdentifier();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('attributePageHeader'))->assert()->textEquals($this->expectedAttributeName);
    }

    public function getName(): string
    {
        return 'Attribute';
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/attribute-definition/%s',
            $this->expectedAttributeIdentifier
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('attributePageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('deleteButton', '.ibexa-btn--secondary'),
            new VisibleCSSLocator('attributeInformationName', 'div.ibexa-details__item:nth-of-type(1) .ibexa-details__item-content'),
            new VisibleCSSLocator('attributeInformationIdentifier', 'div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'),
            new VisibleCSSLocator('attributeInformationDescription', 'div.ibexa-details__item:nth-of-type(3) .ibexa-details__item-content'),
            new VisibleCSSLocator('attributeInformationType', 'div.ibexa-details__item:nth-of-type(4) .ibexa-details__item-content'),
            new VisibleCSSLocator('attributeInformationGroup', 'div.ibexa-details__item:nth-of-type(5) .ibexa-details__item-content'),
            new VisibleCSSLocator('attributeInformationPosition', 'div.ibexa-details__item:nth-of-type(6) .ibexa-details__item-content'),
        ];
    }
}
