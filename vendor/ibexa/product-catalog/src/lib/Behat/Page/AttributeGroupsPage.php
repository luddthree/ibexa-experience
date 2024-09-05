<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

final class AttributeGroupsPage extends Page
{
    private TableInterface $table;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
    }

    public function attributeGroupExists(string $attributeGroupName): bool
    {
        return $this->table->hasElement(['Name' => $attributeGroupName]);
    }

    public function editAttributeGroup(string $attributeGroupName): void
    {
        $this->table->getTableRow(['Name' => $attributeGroupName])->edit();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('attributeGroupsPageHeader'))->assert()->textEquals('Attribute groups');
    }

    public function getName(): string
    {
        return 'Attribute groups';
    }

    protected function getRoute(): string
    {
        return '/attribute-group';
    }

    protected function specifyLocators(): array
    {
        return [
                new VisibleCSSLocator('attributeGroupsPageHeader', '.ibexa-page-title__title'),
        ];
    }
}
