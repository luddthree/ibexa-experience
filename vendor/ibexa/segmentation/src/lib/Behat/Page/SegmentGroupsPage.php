<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Segmentation\Behat\Component\SegmentGroupCreatePopup;
use PHPUnit\Framework\Assert;

class SegmentGroupsPage extends Page
{
    /**
     * @var \Ibexa\Segmentation\Behat\Component\SegmentGroupCreatePopup
     */
    private $segmentGroupCreatePopup;

    /**
     * @var \Ibexa\AdminUi\Behat\Component\Dialog
     */
    private $dialog;

    /**
     * @var \Ibexa\AdminUi\Behat\Component\Table\TableInterface
     */
    private $table;

    public function __construct(Session $session, Router $router, SegmentGroupCreatePopup $segmentGroupCreatePopup, TableBuilder $tableBuilder, Dialog $dialog)
    {
        parent::__construct($session, $router);
        $this->segmentGroupCreatePopup = $segmentGroupCreatePopup;
        $this->dialog = $dialog;
        $this->table = $tableBuilder
            ->newTable()
            ->withParentLocator(new VisibleCSSLocator('segmentGroupTable', '[name=segment_group_bulk_delete] div .ibexa-table'))
            ->build();
    }

    public function setNewSegmentGroupNameAndIdentifier(string $name, string $identifier): void
    {
        $this->segmentGroupCreatePopup->verifyIsLoaded();
        $this->segmentGroupCreatePopup->setNewSegmentGroupNameAndIdentifier($name, $identifier);
    }

    public function setNewSegmentNameAndIdentifier(string $name, string $identifier): void
    {
        $this->segmentGroupCreatePopup->setNewSegmentNameAndIdentifier($name, $identifier);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createSegmentGroupButton', 'div.ibexa-content-menu-wrapper > ul > li:nth-child(1) > button'),
            new VisibleCSSLocator('fieldInput', 'input'),
            new VisibleCSSLocator('title', '.ibexa-page-title__top h1'),
            new VisibleCSSLocator('segmentGroupTrashButton', 'button#bulk-delete-segment-group'),
        ];
    }

    public function openSegmentGroupCreationWindow(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentGroupButton'))->click();
    }

    public function addNewSegmentRow(): void
    {
        $this->segmentGroupCreatePopup->addNewSegmentRow();
    }

    public function confirmNewSegmentGroupCreation(): void
    {
        $this->segmentGroupCreatePopup->confirmSegmentGroupCreation();
    }

    public function deleteSegmentGroup(string $segmentGroupName): void
    {
        $this->table->getTableRow(['Name' => $segmentGroupName])->select();
        $this->getHTMLPage()->find($this->getLocator('segmentGroupTrashButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function verifySegmentGroupIsNotOnList(string $segmentGroupName): void
    {
        Assert::assertFalse($this->table->hasElement(['Name' => $segmentGroupName]));
    }

    public function getName(): string
    {
        return 'Segment Groups';
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('title'))->assert()->textEquals('Segment Groups');
    }

    protected function getRoute(): string
    {
        return 'segmentation/group/list';
    }
}
