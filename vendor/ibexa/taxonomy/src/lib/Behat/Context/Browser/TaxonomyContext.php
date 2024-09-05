<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\Context\Browser;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\CreateNewPopup;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Taxonomy\Behat\Component\TagPicker;
use Ibexa\Taxonomy\Behat\Component\TaxonomyTree;
use Ibexa\Taxonomy\Behat\Page\TaxonomyPage;
use PHPUnit\Framework\Assert;

final class TaxonomyContext implements Context
{
    private TaxonomyPage $taxonomyPage;

    private ContentActionsMenu $contentActionsMenu;

    private Dialog $dialog;

    private TaxonomyTree $taxonomyTree;

    private TagPicker $tagPicker;

    private TableInterface $table;

    private CreateNewPopup $createNewPopup;

    private LanguageService $languageService;

    public function __construct(
        TaxonomyPage $taxonomyPage,
        ContentActionsMenu $contentActionsMenu,
        Dialog $dialog,
        TaxonomyTree $taxonomyTree,
        TagPicker $tagPicker,
        TableBuilder $tableBuilder,
        CreateNewPopup $createNewPopup,
        LanguageService $languageService
    ) {
        $this->taxonomyPage = $taxonomyPage;
        $this->contentActionsMenu = $contentActionsMenu;
        $this->dialog = $dialog;
        $this->taxonomyTree = $taxonomyTree;
        $this->tagPicker = $tagPicker;
        $this->table = $tableBuilder->newTable()->build();
        $this->createNewPopup = $createNewPopup;
        $this->languageService = $languageService;
    }

    /**
     * @Given I start creating a new Tag
     */
    public function startCreatingNewTag(): void
    {
        $this->contentActionsMenu->clickButton('Create');

        $languages = $this->languageService->loadLanguages();
        Assert::assertIsArray($languages);

        if (count($languages) == 1) {
            return;
        }

        $this->createNewPopup->verifyIsLoaded();
        $this->createNewPopup->confirm();
    }

    /**
     * @Given I am viewing the Taxonomy Tag named :tagName
     */
    public function openTagPage(string $tagName): void
    {
        $this->taxonomyPage->setExpectedTagName($tagName);
        $this->taxonomyPage->open('admin');
        $this->taxonomyPage->verifyIsLoaded();
    }

    /**
     * @Given I should be viewing the Taxonomy Tag named :tagName
     */
    public function shouldBeViewingTagPage(string $expectedTagName): void
    {
        $this->taxonomyPage->setExpectedTagName($expectedTagName);
        $this->taxonomyPage->verifyIsLoaded();
    }

    /**
     * @When I delete the Tag
     */
    public function iDeleteTheTag(): void
    {
        $this->contentActionsMenu->clickButton('Delete');
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    /**
     * @Given Tag :tagPath exists in the Taxonomy tree
     */
    public function tagExistsInTheTaxonomyTree(string $tagPath): void
    {
        $this->taxonomyTree->verifyIsLoaded();
        $this->taxonomyTree->verifyTagExists($tagPath);
    }

    /**
     * @Given Tag :tagPath doesn't exist in the Taxonomy tree
     */
    public function tagDoesnTExistInTheTaxonomyTree(string $tagPath): void
    {
        $this->taxonomyTree->verifyIsLoaded();
        $this->taxonomyTree->verifyTagNotExists($tagPath);
    }

    /**
     * @When I move the Tag under :tagPath
     */
    public function iMoveTheTagUnder(string $tagPath): void
    {
        $this->contentActionsMenu->clickButton('Move');
        $this->tagPicker->setExpectedHeader('Select new parent');
        $this->tagPicker->setExpectedConfirmMessage('Select new parent');
        $this->tagPicker->verifyIsLoaded();
        $this->tagPicker->selectTag($tagPath);
        $this->tagPicker->confirm();
    }

    /**
     * @Then Content Items are displayed as assigned to that Tag
     */
    public function contentItemsAreDisplayedAsAssignedToThatTag(TableNode $table): void
    {
        $contentItemNames = array_column($table->getColumnsHash(), 'itemName');

        foreach ($contentItemNames as $contentItemName) {
            Assert::assertTrue($this->table->hasElement(['Name' => $contentItemName]));
        }
    }
}
