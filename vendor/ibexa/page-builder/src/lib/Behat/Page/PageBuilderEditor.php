<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Page;

use Behat\Mink\Session;
use function count;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\ContentTypePicker;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Notification;
use Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage;
use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Behat\Browser\Element\Action\MouseOverAndClick;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementsCountCondition;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewInterface;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\FieldTypePage\ApplicationConfig\Providers\LayoutDefinitions;
use Ibexa\PageBuilder\Behat\Component\BlockPreview\BlockPreviewRegistry;
use Ibexa\PageBuilder\Behat\Component\Blocks\BlockRegistry;
use Ibexa\PageBuilder\Behat\Component\Blocks\PageBuilderBlockInterface;
use Ibexa\PageBuilder\Behat\Component\PageBuilderActionBar;
use Ibexa\PageBuilder\Behat\Component\PageBuilderCreatorPopup;
use Ibexa\PageBuilder\Behat\Component\PageBuilderIntroductionPopup;
use Ibexa\PageBuilder\Behat\Component\Timeline;
use Ibexa\PageBuilder\Behat\Component\Visibility;
use Ibexa\PageBuilder\UI\Config\Provider\IsPageBuilderVisited;
use PHPUnit\Framework\Assert;

class PageBuilderEditor extends Page
{
    /** @var \Ibexa\PageBuilder\Behat\Component\PageBuilderActionBar */
    private $actionBar;

    /** @var int */
    private $expectedLocationId;

    /** @var \Ibexa\AdminUi\Behat\Component\ContentActionsMenu */
    private $contentActionsMenu;

    /** @var \Ibexa\AdminUi\Behat\Component\ContentTypePicker */
    private $contentTypePicker;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    /** @var \Ibexa\PageBuilder\Behat\Component\Timeline */
    private $timeline;

    /** @var \Ibexa\PageBuilder\Behat\Component\PageBuilderCreatorPopup */
    private $pageBuilderCreatorPopup;

    private PageBuilderIntroductionPopup $pageBuilderIntroductionPopup;

    /** @var \Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage */
    private $contentUpdateItemPage;

    /** @var \Ibexa\PageBuilder\Behat\Component\Blocks\BlockRegistry */
    private $blockRegistry;

    /**
     * @var \Ibexa\PageBuilder\Behat\Component\BlockPreview\BlockPreviewRegistry
     */
    private $blockPreviewRegistry;

    /** @var \Ibexa\Behat\API\Facade\ContentFacade */
    private $contentFacade;

    /** @var \Ibexa\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    /** @var \Ibexa\FieldTypePage\ApplicationConfig\Providers\LayoutDefinitions */
    private $layoutDefinitionsProvider;

    private IsPageBuilderVisited $isPageBuilderVisitedProvider;

    /** @var \Ibexa\Behat\Browser\Page\Preview\PagePreviewInterface */
    private $previewPage;

    /** @var \Ibexa\AdminUi\Behat\Component\Notification */
    private $notification;

    /** @var \Ibexa\PageBuilder\Behat\Component\Blocks\PageBuilderBlockInterface */
    private $currentlyOpenedBlock;

    /** @var \Ibexa\PageBuilder\Behat\Component\Visibility */
    private Visibility $visibility;

    private PermissionResolver $permissionResolver;

    public function __construct(
        Session $session,
        Router $router,
        PageBuilderActionBar $actionBar,
        ContentActionsMenu $contentActionsMenu,
        ContentTypePicker $contentTypePicker,
        Dialog $dialog,
        Timeline $timeline,
        Visibility $visibility,
        PageBuilderCreatorPopup $pageBuilderCreatorPopup,
        PageBuilderIntroductionPopup $pageBuilderIntroductionPopup,
        ContentUpdateItemPage $contentUpdateItemPage,
        BlockRegistry $blockRegistry,
        BlockPreviewRegistry $blockPreviewRegistry,
        ContentFacade $contentFacade,
        ArgumentParser $argumentParser,
        LayoutDefinitions $layoutDefinitionsProvider,
        IsPageBuilderVisited $isPageBuilderVisitedProvider,
        Notification $notification,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($session, $router);
        $this->actionBar = $actionBar;
        $this->contentActionsMenu = $contentActionsMenu;
        $this->contentTypePicker = $contentTypePicker;
        $this->dialog = $dialog;
        $this->timeline = $timeline;
        $this->pageBuilderCreatorPopup = $pageBuilderCreatorPopup;
        $this->contentUpdateItemPage = $contentUpdateItemPage;
        $this->blockRegistry = $blockRegistry;
        $this->blockPreviewRegistry = $blockPreviewRegistry;
        $this->contentFacade = $contentFacade;
        $this->argumentParser = $argumentParser;
        $this->layoutDefinitionsProvider = $layoutDefinitionsProvider;
        $this->notification = $notification;
        $this->visibility = $visibility;
        $this->isPageBuilderVisitedProvider = $isPageBuilderVisitedProvider;
        $this->pageBuilderIntroductionPopup = $pageBuilderIntroductionPopup;
        $this->permissionResolver = $permissionResolver;
    }

    public function createNew(string $contentType): void
    {
        $this->actionBar->createNew();
        $this->contentTypePicker->verifyIsLoaded();
        $this->contentTypePicker->select($contentType);
    }

    public function chooseLayout(string $layout): void
    {
        $this->pageBuilderCreatorPopup->verifyIsLoaded();
        $this->pageBuilderCreatorPopup->chooseLayout($layout);
        $this->pageBuilderCreatorPopup->finishCreating();
    }

    public function closeIntroductionModal(): void
    {
        $this->pageBuilderIntroductionPopup->verifyIsLoaded();
        $this->pageBuilderIntroductionPopup->closeIntroductoryModal();
    }

    public function toggleFieldsForm(): void
    {
        $this->actionBar->toggleFieldsForm();
    }

    public function setPreviewedPage(PagePreviewInterface $previewPage)
    {
        $this->previewPage = $previewPage;
    }

    public function verifyPreviewData(): void
    {
        if ($this->previewPage === null) {
            throw new BadStateException('previewPage', "Please call 'setPreviewedPage' before calling 'verifyPreviewData'");
        }

        $this->switchIntoPreviewIframe();
        $this->previewPage->verifyPreviewData();
        $this->switchBackToEditorIframe();
    }

    public function publish(): void
    {
        $this->contentActionsMenu->verifyIsLoaded();
        $this->contentActionsMenu->clickButton('Publish');
    }

    public function clickMenuButton(string $menuItem, string $groupName = null): void
    {
        $this->contentActionsMenu->verifyIsLoaded();
        $this->contentActionsMenu->clickButton($menuItem, $groupName);
    }

    public function getCurrentMode(): string
    {
        return $this->actionBar->getCurrentMode();
    }

    public function getCurrentEditingMode(): string
    {
        return $this->actionBar->getCurrentEditingMode();
    }

    public function deleteDraft(): void
    {
        $this->contentActionsMenu->verifyIsLoaded();
        $this->contentActionsMenu->clickButton('Delete draft');
    }

    public function switchToEditMode(?string $language): void
    {
        $this->actionBar->switchToEditMode($language);
    }

    public function toggleTimeline(): void
    {
        $this->actionBar->toggleTimeline();
    }

    public function toggleVisibility(): void
    {
        $this->actionBar->toggleVisibility();
    }

    public function goToUpcomingEvent(int $eventPosition, string $expectedEventDetails): void
    {
        $this->timeline->verifyIsLoaded();
        $actualEventDetails = $this->timeline->getEventDetails($eventPosition);
        Assert::assertEquals($expectedEventDetails, $actualEventDetails);
        $this->timeline->goTo($eventPosition);
    }

    public function sendToTrash(): void
    {
        $this->actionBar->selectFromAdditionalMenu('Delete');
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function openVersionsTable(): void
    {
        $this->actionBar->selectFromAdditionalMenu('Versions');
    }

    public function canCurrentUserEdit(): bool
    {
        return $this->actionBar->canCurrentUserEdit();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(20)
            ->waitUntilCondition(
                new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('loadingSelector'))
            );
        $this->actionBar->verifyIsLoaded();
    }

    public function verifyPreviewingInTheFuture()
    {
        $this->actionBar->verifyPreviewingInTheFuture();
    }

    public function verifyPreviewForBlock(string $blockName, string $blockType, string $layout, array $previewData = null)
    {
        $previewData = $previewData ?? $this->blockRegistry->getBlock($blockType)->getDefaultPreviewData();
        $preview = $this->blockPreviewRegistry->getPreviewForBlock($blockType, $layout);
        $preview->setExpectedData($previewData);
        $this->switchIntoPreviewIframe();
        $preview->verifyPreview();
        $this->switchBackToEditorIframe();
    }

    public function setExpectedLocationPath(string $locationPath)
    {
        $locationPath = $this->argumentParser->parseUrl($locationPath);
        $this->expectedLocationId = $this->contentFacade->getLocationByLocationURL($locationPath)->id;
    }

    public function getName(): string
    {
        return 'Page Builder';
    }

    public function setField(string $label, string $value): void
    {
        $this->contentUpdateItemPage->verifyIsLoaded();
        $this->contentUpdateItemPage->fillFieldWithValue($label, ['value' => $value]);
    }

    public function removeBlock(string $blockName): void
    {
        $currentCount = $this->getTotalBlockCount();
        $selectedBlock = $this->selectBlock($blockName);

        $this->switchIntoPreviewIframe();

        $selectedBlock->mouseOver();

        $selectedBlock->setTimeout(5)
            ->find(new VisibleCSSLocator('additionalOptionsButton', '.c-pb-action-menu--editable div.c-pb-action-menu__actions  button:nth-child(4)'))
            ->execute(new MouseOverAndClick());
        $this->switchBackToEditorIframe();
        $this->getHTMLPage()->setTimeout(5)
            ->find(new VisibleCSSLocator('deleteBlockButton', 'ul.c-pb-preview-block__options-menu--opened li:nth-child(3) button'))
            ->execute(new MouseOverAndClick());
        $this->switchIntoPreviewIframe();
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementsCountCondition($this->getHTMLPage(), $this->getLocator('blockAttribute'), $currentCount - 1)
            );

        $this->switchBackToEditorIframe();

        Assert::assertEquals($currentCount - 1, $this->getTotalBlockCount());
    }

    public function openBlockSettings(string $blockName, string $blockType)
    {
        $selectedBlock = $this->selectBlock($blockName);
        $this->switchIntoPreviewIframe();
        usleep(250000);
        $selectedBlock->mouseOver();
        usleep(250000);
        $selectedBlock
            ->find(new VisibleCSSLocator('settings', '[data-original-title="Block settings"]'))
            ->click();
        $this->switchBackToEditorIframe();

        if ($this->notification->isVisible()) {
            $this->notification->verifyAlertSuccess();
            $this->notification->closeAlert();
        }

        $this->currentlyOpenedBlock = $this->blockRegistry->getBlock($blockType);
    }

    public function addBlock(string $blockType)
    {
        $this->getHTMLPage()->find(new VisibleCSSLocator('blockFilterInout', '.c-pb-toolbox__toolbox-filter'))->setValue($blockType);
        $blockToAdd = $this->getHTMLPage()->findAll($this->getLocator('toolboxBlock'))->getByCriterion(new ElementTextCriterion($blockType));

        $hoverLocator = $this->getTotalBlockCount() > 0
            ? $this->getLocator('blockInZone')
            : $this->getLocator('zone');
        $currentBlockCount = $this->getTotalBlockCount();

        $iframeId = $this->getLocator('iframeID')->getSelector();

        $fromExpression = sprintf('document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue', $blockToAdd->getXPath());
        $hoverExpression = sprintf("document.querySelector('%s').contentDocument.querySelector('%s')", $iframeId, $hoverLocator->getSelector());
        $placeholderExpression = sprintf(
            "document.querySelector('%s').contentDocument.querySelector('%s')",
            $iframeId,
            $this->getLocator('blockPlaceholder')->getSelector()
        );

        $this->getHTMLPage()->dragAndDrop($fromExpression, $hoverExpression, $placeholderExpression);

        $this->switchIntoPreviewIframe();
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementsCountCondition(
                $this->getHTMLPage(),
                $this->getLocator('blockAttribute'),
                $currentBlockCount + 1
            ));
        $this->switchBackToEditorIframe();

        $this->currentlyOpenedBlock = $this->blockRegistry->getBlock($blockType);
    }

    protected function selectBlock(string $blockName): ElementInterface
    {
        $this->switchIntoPreviewIframe();

        $blocks = $this->getHTMLPage()
            ->findAll($this->getLocator('blockInZone'))->toArray();

        $foundBlock = null;

        foreach ($blocks as $i => $block) {
            $script = sprintf(
                "document.querySelector('[data-type=\"preview\"]:nth-of-type(%d)').dispatchEvent(new Event('click'))",
                $i + 1 // CSS selectors are 1 indexed
            );

            $this->getSession()->executeScript($script);
            $block->find($this->getLocator('blockTitle'))->isVisible();

            $currentBlockName = $block->find($this->getLocator('blockTitle'))->getText();

            if ($currentBlockName === $blockName) {
                $foundBlock = $block;

                break;
            }
        }

        if ($foundBlock === null) {
            throw new ElementNotFoundException(sprintf('Block named: %s not found.', $blockName));
        }

        $this->switchBackToEditorIframe();

        return $foundBlock;
    }

    public function getTotalBlockCount(): int
    {
        $this->switchIntoPreviewIframe();
        $totalBlockCount = $this->getHTMLPage()->setTimeout(3)->findAll($this->getLocator('blockAttribute'))->count();
        $this->switchBackToEditorIframe();

        return $totalBlockCount;
    }

    public function shouldLayoutSelectorPopupBeDisplayed(): bool
    {
        return count($this->layoutDefinitionsProvider->getConfig()) > 1;
    }

    public function shouldIntroductionModalBeDisplayed(): bool
    {
        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $userId = $this->getHTMLPage()->executeJavaScript("document.querySelector('[name=UserId]').getAttribute('content')");
        $userIdInt = (int) $userId;

        $this->permissionResolver->setCurrentUserReference(new UserReference($userIdInt));

        $isPageBuilderVisited = $this->isPageBuilderVisitedProvider->getConfig() === false;

        $this->permissionResolver->setCurrentUserReference(new UserReference($currentUserId));

        return $isPageBuilderVisited;
    }

    public function getCurrentBlock(): PageBuilderBlockInterface
    {
        return $this->currentlyOpenedBlock;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('iframeID', '#page-builder-preview'),
            new VisibleCSSLocator('spinner', '.ibexa-pb-app__loading-wrapper .ibexa-icon.ibexa-spin'),
            new VisibleCSSLocator('backgroundFade', '.modal-backdrop.fade.show'),
            new VisibleCSSLocator('blockAttribute', '[data-ez-block-id]'),
            new VisibleCSSLocator('blockInZone', '.landing-page__zone [data-ez-block-id]'),
            new VisibleCSSLocator('blockTitle', '.c-pb-block-preview .c-pb-block-preview__inner--empty strong'),
            new VisibleCSSLocator('toolboxBlock', '.c-pb-toolbox-block__label'),
            new VisibleCSSLocator('blockPlaceholder', '.droppable-placeholder'),
            new VisibleCSSLocator('zone', '.landing-page__zone'),
            new VisibleCSSLocator('loadingSelector', '.c-pb-iframe__loading'),
            new VisibleCSSLocator('openedBlockType', '.c-pb-config-popup__subtitle'),
        ];
    }

    protected function getRoute(): string
    {
        return sprintf('page/preview/%s', $this->expectedLocationId);
    }

    protected function switchIntoPreviewIframe(): void
    {
        $iframeName = 'page-builder-preview';
        $this->getSession()->getDriver()->switchToIFrame($iframeName);
    }

    protected function switchBackToEditorIframe(): void
    {
        $this->getSession()->getDriver()->switchToIFrame(null);
    }

    public function verifyBlockTypeIsNotAvailable(string $blockName): void
    {
        $blockLabelList = $this->getLocator('toolboxBlock');
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementExistsCondition($this->getHTMLPage(), $blockLabelList)
            )
            ->findAll($blockLabelList)
            ->assert()->hasElements()
            ->filterBy(new ElementTextCriterion($blockName))
            ->assert()->isEmpty();
    }

    public function selectSegmentInVisibilityTab(string $segmentName): void
    {
        $this->visibility->verifyIsLoaded();
        $this->visibility->selectSegmentUnderSegmentGroup($segmentName);
    }
}
