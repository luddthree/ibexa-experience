<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Component\Table;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class WorkflowListTable extends Component
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableBuilder */
    private $tableBuilder;

    public function __construct(Session $session, TableBuilder $tableBuilder)
    {
        parent::__construct($session);
        $this->tableBuilder = $tableBuilder;
    }

    public function isElementPresentInTable(string $name, string $stageName): bool
    {
        $headers = $this->getHTMLPage()
            ->findAll($this->getLocator('tableHeader'))
            ->mapBy(new ElementTextMapper());

        foreach ($headers as $i => $header) {
            if (false !== strpos($header, $stageName)) {
                $tablePosition = $i + 1;

                break;
            }
        }

        $parentLocator = new VisibleCSSLocator('parentLocator', sprintf('div.ibexa-scrollable-wrapper:nth-of-type(%d) .ibexa-table', 2 * $tablePosition));
        $table = $this->tableBuilder->newTable()->withParentLocator($parentLocator)->build();

        return $table->hasElement(['Name' => $name]);
    }

    public function verifyIsLoaded(): void
    {
        // empty
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('tableHeader', '.tab-pane.active .ibexa-table-header__headline'),
        ];
    }
}
