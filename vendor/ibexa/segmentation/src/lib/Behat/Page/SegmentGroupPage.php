<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\TableNavigationTab;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Behat\Component\SegmentCreatePopup;
use PHPUnit\Framework\Assert;

class SegmentGroupPage extends Page
{
    private int $expectedSegmentId;

    /**
     * @var \Ibexa\Segmentation\Behat\Component\SegmentCreatePopup
     */
    private $segmentCreatePopup;

    /**
     * @var \Ibexa\AdminUi\Behat\Component\Dialog
     */
    private $dialog;

    /**
     * @var \Ibexa\AdminUi\Behat\Component\Table\TableInterface
     */
    private $table;

    /**
     * @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface
     */
    private SegmentationServiceInterface $segmentationService;

    /**
     * @var \Ibexa\AdminUi\Behat\Component\TableNavigationTab
     */
    private TableNavigationTab $tableNavigationTab;

    public function __construct(Session $session, Router $router, SegmentCreatePopup $segmentCreatePopup, Dialog $dialog, TableBuilder $tableBuilder, SegmentationServiceInterface $segmentationService, TableNavigationTab $tableNavigationTab)
    {
        parent::__construct($session, $router);
        $this->segmentCreatePopup = $segmentCreatePopup;
        $this->dialog = $dialog;
        $this->table = $tableBuilder
            ->newTable()
            ->withParentLocator(new VisibleCSSLocator('segmentUnderThisGroupTable', 'form table.ibexa-table'))
            ->build();

        $this->segmentationService = $segmentationService;
        $this->tableNavigationTab = $tableNavigationTab;
    }

    public function goToTab(string $tabName): void
    {
        $this->tableNavigationTab->goToTab($tabName);
    }

    public function getName(): string
    {
        return 'Segment Group';
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)->find($this->getLocator('segmentGroupDetailsTitle'))->isVisible();
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/segmentation/group/view/%d',
            $this->expectedSegmentId
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('segmentGroupDetailsTitle', 'div.ibexa-page-title__top > h1'),
            new VisibleCSSLocator('segmentGroupDetailsName', 'div:nth-child(1) > div.ibexa-details__item-content'),
            new VisibleCSSLocator('segmentGroupDetailsIdentifier', 'div:nth-child(2) > div.ibexa-details__item-content'),
            new VisibleCSSLocator('createSegmentButton', '[data-bs-target="#create-segment-modal"]'),
            new VisibleCSSLocator('segmentTrashButton', '#bulk-delete-segment'),
        ];
    }

    public function verifySegmentGroupName(string $segmentGroupName): void
    {
        $this->getHTMLPage()->setTimeout(3)
            ->find($this->getLocator('segmentGroupDetailsName'))
            ->assert()->textEquals($segmentGroupName);
    }

    public function verifySegmentGroupIdentifier(string $segmentGroupIdentifier): void
    {
        $this->getHTMLPage()->setTimeout(3)
            ->find($this->getLocator('segmentGroupDetailsIdentifier'))
            ->assert()->textEquals($segmentGroupIdentifier);
    }

    public function verifyHasSegment(string $name, string $identifier): void
    {
        Assert::assertTrue($this->table->hasElement(['Segment name' => $name, 'Segment identifier' => $identifier]));
    }

    public function verifySegmentIsNotOnList(string $name): void
    {
        Assert::assertFalse($this->table->hasElement(['Name' => $name]));
    }

    public function openSegmentCreationWindow(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentButton'))->click();
    }

    public function setNewSegmentNameAndIdentifier(string $name, string $identifier): void
    {
        $this->segmentCreatePopup->verifyIsLoaded();
        $this->segmentCreatePopup->setNameAndIdentifier($name, $identifier);
    }

    public function confirmNewSegmentAddition(): void
    {
        $this->segmentCreatePopup->confirmNewSegmentAddition();
    }

    public function deleteSegment(string $segmentName): void
    {
        $this->table->getTableRow(['Segment name' => $segmentName])->select();
        $this->getHTMLPage()->find($this->getLocator('segmentTrashButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function setExpectedSegmentGroupIdentifier(string $segmentGroupIdentifier): void
    {
        $this->expectedSegmentId = $this->segmentationService->loadSegmentGroupByIdentifier($segmentGroupIdentifier)->id;
    }
}
