<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\CreateNewPopup;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

final class AttributesPage extends Page
{
    private TableInterface $table;

    private CreateNewPopup $popup;

    public function __construct(Session $session, Router $router, CreateNewPopup $popup, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->popup = $popup;
        $this->table = $tableBuilder->newTable()->build();
    }

    public function selectAttributeOptions(string $attributeType): void
    {
        $this->popup->verifyIsLoaded();
        $this->popup->verifyHeaderText('Add attribute definition');
        $this->popup->selectFromDropdown('Language', 'English (United Kingdom)');
        $this->popup->selectFromDropdown('Attribute type', $attributeType);
        $this->popup->confirm();
    }

    public function attributeExists(string $attributeName): bool
    {
        return $this->table->hasElement(['Name' => $attributeName]);
    }

    public function editAttribute(string $attributeName): void
    {
        $this->table->getTableRow(['Name' => $attributeName])->edit();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('attributesPageHeader'))->assert()->textEquals('Attributes');
    }

    public function getName(): string
    {
        return 'Attributes';
    }

    protected function getRoute(): string
    {
        return '/attribute-definition/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('attributesPageHeader', '.ibexa-page-title__title'),
        ];
    }
}
