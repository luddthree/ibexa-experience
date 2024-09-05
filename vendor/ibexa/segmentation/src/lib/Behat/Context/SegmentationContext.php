<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Behat\Context;

use Behat\Behat\Context\Context;
use Ibexa\Segmentation\Behat\Page\SegmentGroupPage;
use Ibexa\Segmentation\Behat\Page\SegmentGroupsPage;

class SegmentationContext implements Context
{
    /**
     * @var \Ibexa\Segmentation\Behat\Page\SegmentGroupsPage
     */
    private SegmentGroupsPage $segmentGroupsPage;

    /**
     * @var \Ibexa\Segmentation\Behat\Page\SegmentGroupPage
     */
    private SegmentGroupPage $segmentGroupPage;

    public function __construct(SegmentGroupsPage $segmentGroupsPage, SegmentGroupPage $segmentGroupPage)
    {
        $this->segmentGroupsPage = $segmentGroupsPage;
        $this->segmentGroupPage = $segmentGroupPage;
    }

    /** @When I start creating a new segment group
     */
    public function iStartCreatingANewSegmentGroup(): void
    {
        $this->segmentGroupsPage->openSegmentGroupCreationWindow();
    }

    /**
     * @When I fill :segmentGroupName name field and :segmentGroupIdentifier identifier field during segment group fields configuration
     */
    public function iFillSegmentGroupConfigurationFields(string $segmentGroupName, string $segmentGroupIdentifier): void
    {
        $this->segmentGroupsPage->verifyIsLoaded();
        $this->segmentGroupsPage->setNewSegmentGroupNameAndIdentifier($segmentGroupName, $segmentGroupIdentifier);
    }

    /**
     * @When I add segment with :name name and :identifier identifier to segment group during segment group creation
     */
    public function iAddSegmentToSegmentGroupDuringCreation(string $name, string $identifier): void
    {
        $this->segmentGroupsPage->addNewSegmentRow();
        $this->segmentGroupsPage->setNewSegmentNameAndIdentifier($name, $identifier);
    }

    /**
     * @When I add segment with :name name and :identifier identifier to segment group
     */
    public function iAddSegmentToSegmentGroupDuringEdition(string $name, string $identifier): void
    {
        $this->segmentGroupPage->openSegmentCreationWindow();
        $this->segmentGroupPage->setNewSegmentNameAndIdentifier($name, $identifier);
        $this->segmentGroupPage->confirmNewSegmentAddition();
    }

    /**
     * @When I confirm creation of new segment group
     */
    public function iConfirmNewSegmentGroupCreation(): void
    {
        $this->segmentGroupsPage->confirmNewSegmentGroupCreation();
    }

    /**
     * @When There's segment group with :segmentGroupName name and :segmentGroupIdentifier identifier in Details section
     */
    public function iVerifySegmentGroupInformationSection(string $segmentGroupName, string $segmentGroupIdentifier): void
    {
        $this->segmentGroupPage->verifyIsLoaded();
        $this->segmentGroupPage->goToTab('Details');
        $this->segmentGroupPage->verifySegmentGroupName($segmentGroupName);
        $this->segmentGroupPage->verifySegmentGroupIdentifier($segmentGroupIdentifier);
    }

    /**
     * @When There's segment with :segmentName name and :segmentIdentifier identifier in Segments Under This Group section
     */
    public function iVerifyAddedSegmentInformationSection(string $segmentName, string $segmentIdentifier): void
    {
        $this->segmentGroupPage->goToTab('Segments under this group');
        $this->segmentGroupPage->verifyHasSegment($segmentName, $segmentIdentifier);
    }

    /**
     * @When I delete :segmentName segment from Segments group
     */
    public function iDeleteSegment(string $segmentName): void
    {
        $this->segmentGroupPage->deleteSegment($segmentName);
    }

    /**
     * @When I delete :segmentGroupName segment group
     */
    public function iDeleteSegmentGroup(string $segmentGroupName): void
    {
        $this->segmentGroupsPage->deleteSegmentGroup($segmentGroupName);
    }

    /**
     * @When There's no segment group with :segmentGroupName name
     */
    public function iVerifySegmentGroupIsDeleted(string $segmentGroupName): void
    {
        $this->segmentGroupsPage->verifySegmentGroupIsNotOnList($segmentGroupName);
    }

    /**
     * @When There's no segment with :segmentName name
     */
    public function iVerifySegmentIsDeleted(string $segmentName): void
    {
        $this->segmentGroupPage->verifySegmentIsNotOnList($segmentName);
    }

    /**
     * @When I open segment group with :segmentGroupIdentifier identifier
     */
    public function iOpenSegmentGroup(string $segmentGroupIdentifier): void
    {
        $this->segmentGroupPage->setExpectedSegmentGroupIdentifier($segmentGroupIdentifier);
        $this->segmentGroupPage->open('admin');
        $this->segmentGroupPage->verifyIsLoaded();
    }
}
