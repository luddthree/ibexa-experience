<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class VersionTab extends Component
{
    private TableBuilder $tableBuilder;

    public function __construct(Session $session, TableBuilder $tableBuilder)
    {
        parent::__construct($session);
        $this->tableBuilder = $tableBuilder;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('tab'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('tab', '#ibexa-tab-location-view-versions'),
            new VisibleCSSLocator('tableHeader', '#ibexa-tab-location-view-versions section .ibexa-table-header__headline'),
            new VisibleCSSLocator('versionCompareButton', '[data-original-title="Version compare"]'),
            new VisibleCSSLocator('scrollableContainer', '.ibexa-back-to-top-scroll-container'),
        ];
    }

    public function enterVersionComparison(string $tableHeader, string $versionNumber): void
    {
        $this->getHTMLPage()->find($this->getLocator('scrollableContainer'))->scrollToBottom($this->getSession());
        $tableHeaders = $this->getHTMLPage()->findAll($this->getLocator('tableHeader'))->mapBy(new ElementTextMapper());
        $headerIndex = array_search($tableHeader, $tableHeaders, true);

        if ($headerIndex === false) {
            throw new ElementNotFoundException(
                sprintf(
                    'Table header with text %s not found! Found %s instead',
                    $tableHeader,
                    implode(',', $tableHeaders)
                )
            );
        }

        $parentLocator = new VisibleCSSLocator(
            'versionTable',
            sprintf('#ibexa-tab-location-view-versions section:nth-of-type(%d)', $headerIndex + 1)
        );

        $table = $this->tableBuilder->newTable()->withParentLocator($parentLocator)->build();
        $table->getTableRow(['Version' => $versionNumber])->click($this->getLocator('versionCompareButton'));
    }
}
