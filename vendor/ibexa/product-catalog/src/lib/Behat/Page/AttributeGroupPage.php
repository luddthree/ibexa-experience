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
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Webmozart\Assert\Assert;

final class AttributeGroupPage extends Page
{
    private string $expectedAttributeGroupName;

    private string $expectedAttributeGroupIdentifier;

    private Dialog $dialog;

    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(Session $session, Router $router, Dialog $dialog, AttributeGroupServiceInterface $attributeGroupService)
    {
        parent::__construct($session, $router);
        $this->dialog = $dialog;
        $this->attributeGroupService = $attributeGroupService;
    }

    public function verifyAttributeGroup(string $attributeName, string $attributeIdentifier, string $attributePosition): void
    {
        $this->getHTMLPage()->find($this->getLocator('attributeGroupInformationName'))->assert()->textEquals($attributeName);
        $this->getHTMLPage()->find($this->getLocator('attributeGroupInformationIdentifier'))->assert()->textEquals($attributeIdentifier);
        $this->getHTMLPage()->find($this->getLocator('attributeGroupInformationPosition'))->assert()->textEquals($attributePosition);
    }

    public function deleteAttributeGroup(): void
    {
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function setExpectedAttributeGroupName(string $attributeGroupName): void
    {
        $this->expectedAttributeGroupName = $attributeGroupName;
        $attributeGroups = $this->attributeGroupService->findAttributeGroups(new AttributeGroupQuery($attributeGroupName))->getAttributeGroups();
        Assert::notEmpty($attributeGroups, 'Attribute group not found');
        $attributeGroup = $attributeGroups[0];
        $this->expectedAttributeGroupIdentifier = $attributeGroup->getIdentifier();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('attributeGroupPageHeader'))->assert()->textEquals($this->expectedAttributeGroupName);
    }

    public function getName(): string
    {
        return 'Attribute group';
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/attribute-group/%s',
            $this->expectedAttributeGroupIdentifier
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('attributeGroupPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('deleteButton', '.ibexa-btn--delete'),
            new VisibleCSSLocator('attributeGroupInformationName', 'div.ibexa-details__item:nth-of-type(1) .ibexa-details__item-content'),
            new VisibleCSSLocator('attributeGroupInformationIdentifier', 'div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'),
            new VisibleCSSLocator('attributeGroupInformationPosition', 'div.ibexa-details__item:nth-of-type(3) .ibexa-details__item-content'),
        ];
    }
}
