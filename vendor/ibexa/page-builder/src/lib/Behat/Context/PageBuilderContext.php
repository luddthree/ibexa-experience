<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use DateTime;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\LeftMenu;
use Ibexa\AdminUi\Behat\Component\UserNotificationPopup;
use Ibexa\AdminUi\Behat\Page\ContentViewPage;
use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewRegistry;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\PageBuilder\Behat\Component\BlockPreview\BlockPreviewRegistry;
use Ibexa\PageBuilder\Behat\Component\Blocks\BlockRegistry;
use Ibexa\PageBuilder\Behat\Component\PageBuilderVersionPopup;
use Ibexa\PageBuilder\Behat\Page\PageBuilderEditor;
use Ibexa\PageBuilder\Behat\Page\SiteFactoryPage;
use Ibexa\Workflow\Behat\Page\DashboardPage;
use LogicException;
use PHPUnit\Framework\Assert;

class PageBuilderContext implements Context
{
    /** @var \Ibexa\PageBuilder\Behat\Page\PageBuilderEditor */
    private $pageBuilderEditor;

    /** @var \Ibexa\AdminUi\Behat\Page\ContentViewPage */
    private $contentViewPage;

    /** @var \Ibexa\AdminUi\Behat\Component\LeftMenu */
    private $leftMenu;

    /** @var \Ibexa\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    /** @var \Ibexa\PageBuilder\Behat\Page\SiteFactoryPage */
    private $siteFactoryPage;

    /** @var \Ibexa\PageBuilder\Behat\Component\PageBuilderVersionPopup */
    private $versionPopup;

    /** @var \Ibexa\Behat\API\Facade\ContentFacade */
    private $contentFacade;

    /** @var \Ibexa\Behat\Browser\Page\Preview\PagePreviewRegistry */
    private $pagePreviewRegistry;

    /** @var \Ibexa\PageBuilder\Behat\Component\BlockPreview\BlockPreviewRegistry */
    private $blockPreviewRegistry;

    /** @var \Ibexa\PageBuilder\Behat\Component\Blocks\BlockRegistry */
    private $blockRegistry;

    private UserNotificationPopup $userNotificationPopup;

    private DashboardPage $dashboardPage;

    private ContentActionsMenu $contentActionsMenu;

    public function __construct(
        PageBuilderEditor $pageBuilderEditor,
        ContentViewPage $contentViewPage,
        LeftMenu $leftMenu,
        ArgumentParser $argumentParser,
        SiteFactoryPage $siteFactoryPage,
        PageBuilderVersionPopup $versionPopup,
        ContentFacade $contentFacade,
        PagePreviewRegistry $pagePreviewRegistry,
        BlockPreviewRegistry $blockPreviewRegistry,
        BlockRegistry $blockRegistry,
        UserNotificationPopup $userNotificationPopup,
        DashboardPage $dashboardPage,
        ContentActionsMenu $contentActionsMenu
    ) {
        $this->pageBuilderEditor = $pageBuilderEditor;
        $this->contentViewPage = $contentViewPage;
        $this->leftMenu = $leftMenu;
        $this->argumentParser = $argumentParser;
        $this->siteFactoryPage = $siteFactoryPage;
        $this->versionPopup = $versionPopup;
        $this->contentFacade = $contentFacade;
        $this->pagePreviewRegistry = $pagePreviewRegistry;
        $this->blockPreviewRegistry = $blockPreviewRegistry;
        $this->blockRegistry = $blockRegistry;
        $this->userNotificationPopup = $userNotificationPopup;
        $this->dashboardPage = $dashboardPage;
        $this->contentActionsMenu = $contentActionsMenu;
    }

    /**
     * @Given I start creating a new Landing Page :name
     * @Given I start creating a new Landing Page :name of type :contentTypeName
     */
    public function startCreatingNewLandingPage(string $name, string $contentTypeName = 'Landing page', string $layout = 'default'): void
    {
        $shouldIntroductionModalBeDisplayed = $this->pageBuilderEditor->shouldIntroductionModalBeDisplayed();
        $this->contentViewPage->startCreatingContent($contentTypeName);
        if ($this->pageBuilderEditor->shouldLayoutSelectorPopupBeDisplayed()) {
            $this->pageBuilderEditor->chooseLayout($layout);
        }
        if ($shouldIntroductionModalBeDisplayed) {
            $this->pageBuilderEditor->closeIntroductionModal();
        }
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->toggleFieldsForm();
        $this->pageBuilderEditor->setField('Title', $name);
        $this->pageBuilderEditor->setField('Description', $name);
        $this->pageBuilderEditor->toggleFieldsForm();
        $this->pageBuilderEditor->verifyIsLoaded();
    }

    /**
     * @Given I start creating a new Landing Page from Page Builder named :title
     * @Given I start creating a new Landing Page from Page Builder named :title of type :contentTypeName
     * @Given I start creating a new Landing Page from Page Builder named :title of type :contentTypeName with layout :layout
     */
    public function goToPageBuilderCreatorAndAdd(string $title, string $contentTypeName = 'Landing page', string $layout = 'default'): void
    {
        $shouldIntroductionModalBeDisplayed = $this->pageBuilderEditor->shouldIntroductionModalBeDisplayed();
        $this->pageBuilderEditor->createNew($contentTypeName);
        if ($this->pageBuilderEditor->shouldLayoutSelectorPopupBeDisplayed()) {
            $this->pageBuilderEditor->chooseLayout($layout);
        }
        if ($shouldIntroductionModalBeDisplayed) {
            $this->pageBuilderEditor->closeIntroductionModal();
        }
        $this->pageBuilderEditor->toggleFieldsForm();
        $this->pageBuilderEditor->setField('Title', $title);
        $this->pageBuilderEditor->setField('Description', $title);
        $this->pageBuilderEditor->toggleFieldsForm();
    }

    /**
     * @Given I select Landing Page layout :layout
     */
    public function selectLayout(string $layout): void
    {
        $this->pageBuilderEditor->chooseLayout($layout);
    }

    /**
     * @Given I set Landing Page properties
     */
    public function setLandingPageProperties(TableNode $properties): void
    {
        $this->pageBuilderEditor->toggleFieldsForm();
        foreach ($properties->getHash() as $row) {
            $this->pageBuilderEditor->setField($row['field'], $row['value']);
        }
        $this->pageBuilderEditor->toggleFieldsForm();
    }

    /**
     * @Given I enter :value as :fieldName field value in the block
     */
    public function fillBlockFieldValue($value, $fieldName): void
    {
        $this->pageBuilderEditor->getCurrentBlock()->setInputField($fieldName, $value);
    }

    /**
     * @Given I open settings for :blockType block named :blockName
     * @Given I open :blockName block settings
     */
    public function openBlockSettings(string $blockName, string $blockType = ''): void
    {
        if ($blockType === '') {
            $blockType = $blockName;
        }

        $this->pageBuilderEditor->openBlockSettings($blockName, $blockType);
    }

    /**
     * @Given I open the :locationPath Content Item in Page Builder
     */
    public function openLandingPageInPageBuilder(string $locationPath)
    {
        $this->pageBuilderEditor->setExpectedLocationPath($locationPath);
        $this->pageBuilderEditor->open('admin');
        $this->pageBuilderEditor->verifyIsLoaded();
    }

    /**
     * @Given I go to Page Builder
     */
    public function goToPage(string $siteaccess = 'site'): void
    {
        $this->leftMenu->goToTab('Site');
        $this->leftMenu->goToSubTab('List');
        $this->siteFactoryPage->verifyIsLoaded();
        $this->siteFactoryPage->goToLocationPreview($siteaccess);
        $this->pageBuilderEditor->verifyIsLoaded();
    }

    /**
     * @Given I add Landing page blocks
     *
     * @param \Behat\Gherkin\Node\TableNode $table Table with values to load
     */
    public function dragBlocksToZones(TableNode $table): void
    {
        if (count($table->getHash()) > 1) {
            throw new LogicException('To add more than 1 block use PageBuilderEditor::dragSeriesOfBlocksToZones instead');
        }

        foreach ($table->getHash() as $row) {
            $blockType = $row['blockType'];
            if (array_key_exists('zone', $row)) {
                $this->pageBuilderEditor->addBlock($blockType, $row['zone']);
            } else {
                $this->pageBuilderEditor->addBlock($blockType);
            }
        }
    }

    /**
     * @Given I add a series of Landing page blocks
     *
     * @param \Behat\Gherkin\Node\TableNode $table Table with values to load
     */
    public function dragSeriesOfBlocksToZones(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $blockType = $row['blockType'];
            if (array_key_exists('zone', $row)) {
                $this->pageBuilderEditor->addBlock($blockType, $row['zone']);
            } else {
                $this->pageBuilderEditor->addBlock($blockType);
            }
            $this->pageBuilderEditor->getCurrentBlock()->cancelForm();
        }
    }

    /**
     * @Given I remove Landing page blocks
     *
     * @param \Behat\Gherkin\Node\TableNode $table Table with values to load
     */
    public function removeBlocksFromZones(TableNode $table): void
    {
        foreach ($table as $row) {
            $this->pageBuilderEditor->removeBlock($row['blockName']);
        }
    }

    /**
     * @Then there are no blocks displayed
     */
    public function thereAreNoBlocks(): void
    {
        Assert::assertEquals(0, $this->pageBuilderEditor->getTotalBlockCount());
    }

    /**
     * @Given I save the draft
     */
    public function saveTheDraft(): void
    {
        $this->pageBuilderEditor->clickMenuButton('Save', 'Save and close');
    }

    /**
     * @Then I should be in Landing Page :mode mode for :locationURL Landing Page
     */
    public function iShouldBeInLandingPageMode(string $mode, string $locationURL): void
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);

        $location = $this->contentFacade->getLocationByLocationURL($locationURL);
        $pageTitle = $location->getContent()->getName();
        $contentTypeIdentifier = $location->getContent()->getContentType()->identifier;

        $previewedPage = $this->pagePreviewRegistry->getPreview($contentTypeIdentifier);
        $previewedPage->setExpectedPreviewData(['title' => $pageTitle]);
        $this->pageBuilderEditor->setPreviewedPage($previewedPage);
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewData();

//        Assert::assertEqualsIgnoringCase($mode, $this->pageBuilderEditor->getCurrentMode()); TODO: uncomment after Page preview is reintroduced
    }

    /**
     * @Then I should be in Landing Page :mode mode for Landing Page draft called :contentItemName of type :contentTypeIdentifier
     */
    public function iShouldBeInLandingPageModeForDraft(string $mode, string $contentItemName, string $contentTypeIdentifier): void
    {
        $previewedPage = $this->pagePreviewRegistry->getPreview($contentTypeIdentifier);
        $previewedPage->setExpectedPreviewData(['title' => $contentItemName]);
        $this->pageBuilderEditor->setPreviewedPage($previewedPage);
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewData();

//        Assert::assertEqualsIgnoringCase($mode, $this->pageBuilderEditor->getCurrentMode()); TODO: uncomment after Page preview is reintroduced
    }

    /**
     * @Then I start editing :locationUrl Landing Page draft
     */
    public function iStartEditingLandingPage(string $locationURL): void
    {
        $shouldIntroductionModalBeDisplayed = $this->pageBuilderEditor->shouldIntroductionModalBeDisplayed();
        $this->contentActionsMenu->clickButton('Edit');

        if ($shouldIntroductionModalBeDisplayed) {
            $this->pageBuilderEditor->closeIntroductionModal();
        }

        $locationURL = $this->argumentParser->parseUrl($locationURL);

        $location = $this->contentFacade->getLocationByLocationURL($locationURL);
        $pageTitle = $location->getContent()->getName();
        $contentTypeIdentifier = $location->getContent()->getContentType()->identifier;

        $previewedPage = $this->pagePreviewRegistry->getPreview($contentTypeIdentifier);
        $previewedPage->setExpectedPreviewData(['title' => $pageTitle]);
        $this->pageBuilderEditor->setPreviewedPage($previewedPage);
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewData();
    }

    /**
     * @Then I go to Landing Page draft called :contentItemName of type :contentTypeIdentifier with user notification with details:
     */
    public function iStartEditingLandingPageInMode(TableNode $notificationDetails, string $contentItemName, string $contentTypeIdentifier): void
    {
        $shouldIntroductionModalBeDisplayed = $this->pageBuilderEditor->shouldIntroductionModalBeDisplayed();

        $type = $notificationDetails->getHash()[0]['Type'];
        $description = $notificationDetails->getHash()[0]['Description'];

        $this->userNotificationPopup->verifyIsLoaded();
        $this->userNotificationPopup->clickNotification($type, $description);

        if ($shouldIntroductionModalBeDisplayed) {
            $this->pageBuilderEditor->closeIntroductionModal();
        }

        $previewedPage = $this->pagePreviewRegistry->getPreview($contentTypeIdentifier);
        $previewedPage->setExpectedPreviewData(['title' => $contentItemName]);
        $this->pageBuilderEditor->setPreviewedPage($previewedPage);
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewData();
    }

    /**
     * @Then I start reviewing Landing Page draft called :contentItemName of type :contentTypeIdentifier
     */
    public function iStartReviewingLandingPageInMode(string $contentItemName, string $contentTypeIdentifier): void
    {
        $shouldIntroductionModalBeDisplayed = $this->pageBuilderEditor->shouldIntroductionModalBeDisplayed();

        $this->dashboardPage->verifyIsLoaded();
        $this->dashboardPage->editDraftFromReviewQueue($contentItemName);

        if ($shouldIntroductionModalBeDisplayed) {
            $this->pageBuilderEditor->closeIntroductionModal();
        }

        $previewedPage = $this->pagePreviewRegistry->getPreview($contentTypeIdentifier);
        $previewedPage->setExpectedPreviewData(['title' => $contentItemName]);
        $this->pageBuilderEditor->setPreviewedPage($previewedPage);
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewData();
    }

    /**
     * @Then I should be in Landing Page :launchMode editor launch mode
     */
    public function iShouldBeInLandingPageLaunchModeForDraft(string $launchMode): void
    {
        $this->pageBuilderEditor->verifyIsLoaded();
        Assert::assertEqualsIgnoringCase($launchMode, $this->pageBuilderEditor->getCurrentEditingMode());
    }

    /**
     * @When I delete the draft
     */
    public function deleteDraft(): void
    {
        $this->pageBuilderEditor->deleteDraft();
    }

    /**
     * @When I switch to edit mode
     * @When I switch to edit mode in :language language
     */
    public function switchToEditMode(string $language = null): void
    {
        $this->pageBuilderEditor->switchToEditMode($language);
        $this->pageBuilderEditor->verifyIsLoaded();
    }

    /**
     * @Given I publish the Landing Page
     */
    public function publishPage(): void
    {
        $this->pageBuilderEditor->publish();
    }

    /**
     * @Given I set up block :blockname :blockType with default testing configuration
     */
    public function addBlockWithDefaultTestingConfiguration(string $blockName, string $blockType): void
    {
        $this->pageBuilderEditor->addBlock($blockType);
        $this->pageBuilderEditor->getCurrentBlock()->setDefaultTestingConfiguration($blockName);
    }

    /**
     * @Given I start creating a new Content Item :contentType from Page Builder
     */
    public function iStartCreatingANewContentFromPageEditor(string $contentType): void
    {
        $this->pageBuilderEditor->createNew($contentType);
    }

    /**
     * @Given I should be viewing :locationURL in Page Editor
     * @Given I am viewing :locationURL in Page Editor
     */
    public function iShouldBeViewingItemInPageEditor(string $locationURL, ?TableNode $data = null)
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $location = $this->contentFacade->getLocationByLocationURL($locationURL);
        $pageTitle = $location->getContent()->getName();
        $contentTypeIdentifier = $location->getContent()->getContentType()->identifier;

        $blocksData = $this->getExpectedBlockPreviewData($data);

        $expectedPreviewData = ['title' => $pageTitle];

        if ($blocksData) {
            $expectedPreviewData['blocks'] = $blocksData;
        }

        $previewedPage = $this->pagePreviewRegistry->getPreview($contentTypeIdentifier);
        $previewedPage->setExpectedPreviewData($expectedPreviewData);
        $this->pageBuilderEditor->setPreviewedPage($previewedPage);
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewData();

        Assert::assertEquals('view', $this->pageBuilderEditor->getCurrentMode());
    }

    /**
     * @Then I see the :blockName :blockType block and its preview
     */
    public function iSeeTheBlockAndItsPreview(string $blockName, string $blockType, ?TableNode $previewData = null)
    {
        $parsedPreviewData = $previewData ? $previewData->getHash()[0] : null;

        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewForBlock($blockName, $blockType, 'default', $parsedPreviewData);
    }

    /**
     * @Given I set reveal date to a month later
     */
    public function iSetRevealDate()
    {
        $this->pageBuilderEditor->getCurrentBlock()->setRevealDate(new DateTime('+1Month'));
    }

    /**
     * @Given I set hide date to a month later
     */
    public function iSetHideDate()
    {
        $this->pageBuilderEditor->getCurrentBlock()->setHideDate(new DateTime('+1Month'));
    }

    /**
     * @Given I submit the block pop-up form
     */
    public function iSubmitBlockForm()
    {
        $this->pageBuilderEditor->getCurrentBlock()->submitForm();
        $this->pageBuilderEditor->verifyIsLoaded();
    }

    /**
     * @Given I submit the block pop-up form with Name field length assertion
     */
    public function iSubmitBlockFormWithNameLengthAssertion()
    {
        $this->pageBuilderEditor->getCurrentBlock()->submitFormWithNameLengthAssertion();
    }

    /**
     * @Given I open the timeline
     * @Given I close the timeline
     */
    public function iOpenTimeline()
    {
        $this->pageBuilderEditor->toggleTimeline();
    }

    /**
     * @Given I open the visibility tab
     * @Given I close the visibility tab
     */
    public function iOpenVisibility(): void
    {
        $this->pageBuilderEditor->toggleVisibility();
    }

    /**
     * @Given I go to scheduled upcoming event
     */
    public function iGoToEvent(TableNode $expectedEventDetails)
    {
        $expectedEventPosition = (int) $expectedEventDetails->getHash()[0]['position'];
        $expectedEventDetails = $expectedEventDetails->getHash()[0]['event'];
        $this->pageBuilderEditor->goToUpcomingEvent($expectedEventPosition, $expectedEventDetails);
    }

    /**
     * @Given I am previewing Content in the future
     */
    public function previewingContentInTheFuture()
    {
        $this->pageBuilderEditor->verifyPreviewingInTheFuture();
    }

    /**
     * @Given I select :layoutName block layout
     */
    public function setBlockLayout(string $layoutName)
    {
        $this->pageBuilderEditor->getCurrentBlock()->setLayout($layoutName);
    }

    /**
     * @Given I add Content Items to the Collection block
     * @Given I add Content Items to the Content Scheduler block
     */
    public function iAddItemsToBlock(TableNode $itemsToAdd)
    {
        $itemPaths = array_column($itemsToAdd->getHash(), 'item');
        $this->pageBuilderEditor->getCurrentBlock()->addContentItems($itemPaths);
    }

    /**
     * @Given I set :defaultContentItem as default Content Item to the Targeting block
     */
    public function iSetDefaultItemToTargetingBlock(string $defaultContentItem): void
    {
        $this->pageBuilderEditor->getCurrentBlock()->setDefaultContentItem($defaultContentItem);
    }

    /**
     * @Given I add :contentItemForSegment Content Item to :selectedSegment Segment in Targeting block
     */
    public function iAddContentItemInSegmentSetup(string $contentItemForSegment, string $selectedSegment): void
    {
        $this->pageBuilderEditor->getCurrentBlock()->selectSegment($selectedSegment);
        $this->pageBuilderEditor->getCurrentBlock()->addContentItemInSegmentSetup($contentItemForSegment);
    }

    /**
     * @Given I delete the :itemName item from Collection block
     * @Given I delete the :itemName item from Content Scheduler block
     */
    public function deleteItemFromBlock($itemName)
    {
        $this->pageBuilderEditor->getCurrentBlock()->deleteItem($itemName);
    }

    /**
     * @Given I move item :itemName1 behind the item :itemName2 in Collection block
     * @Given I move item :itemName1 behind the item :itemName2 in Content Scheduler block
     */
    public function reorderItemsInBlock(string $itemName1, string $itemName2)
    {
        $this->pageBuilderEditor->getCurrentBlock()->moveItemToAnother($itemName1, $itemName2);
    }

    /**
     * @Given I change airtime of :itemName item to a month later
     */
    public function changeItemAirtime($itemName)
    {
        $this->pageBuilderEditor->getCurrentBlock()->changeAirtime($itemName, new DateTime('+1Month'));
    }

    /**
     * @Given I send the to Trash from Page Builder
     */
    public function sentToTrashFromPageBuilder()
    {
        $this->pageBuilderEditor->sendToTrash();
    }

    /**
     * @Given I open Versions table
     */
    public function openVersionsTable()
    {
        $this->pageBuilderEditor->openVersionsTable();
    }

    /**
     * @Given I see versions data
     */
    public function iSeeVersionsData(TableNode $expectedVersionsData)
    {
        $this->versionPopup->verifyIsLoaded();
        foreach ($expectedVersionsData->getHash() as $expectedVersionData) {
            $actualLabels = $this->versionPopup->getLabels($expectedVersionData['version']);
            $labelsLowercase = strtolower($expectedVersionData['labels']);
            $expectedLabels = explode(',', $labelsLowercase);

            sort($actualLabels);
            sort($expectedLabels);
            Assert::assertEquals($expectedLabels, $actualLabels);
        }
    }

    /**
     * @Given I preview version :versionNumber
     */
    public function previewVersion($versionNumber)
    {
        $this->versionPopup->verifyIsLoaded();
        $this->versionPopup->preview($versionNumber);
        $this->pageBuilderEditor->verifyIsLoaded();
    }

    /**
     * @Given I'm not able to edit in Page Builder
     */
    public function iAmNotAbleToEdit()
    {
        Assert::assertFalse($this->pageBuilderEditor->canCurrentUserEdit());
    }

    private function getExpectedBlockPreviewData(?TableNode $data): array
    {
        $expectedBlockPreviewData = [];

        if ($data === null) {
            return $expectedBlockPreviewData;
        }

        foreach ($data->getHash() as $row) {
            $blockType = $row['blockType'];
            $layout = array_key_exists('layout', $row) ? $row['layout'] : 'default';
            $blockPreview = $this->blockPreviewRegistry->getPreviewForBlock($blockType, $layout);
            $blockPreview->setExpectedData(array_key_exists('parameter1', $row) ? $row : $this->blockRegistry->getBlock($blockType)->getDefaultPreviewData());
            $expectedBlockPreviewData[] = $blockPreview;
        }

        return $expectedBlockPreviewData;
    }

    /**
     * @Given It's not possible to add :blockName block to the Landing Page
     */
    public function addingBlockToLandingPageIsNotPossible(string $blockName): void
    {
        $this->pageBuilderEditor->verifyBlockTypeIsNotAvailable($blockName);
    }

    /**
     * @Given I select :segmentName Segment under Segment Group in visibility tab
     */
    public function iSelectSegmentInVisibilityTab(string $segmentName): void
    {
        $this->pageBuilderEditor->selectSegmentInVisibilityTab($segmentName);
    }
}
