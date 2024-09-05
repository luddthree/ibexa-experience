<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\BaseElementInterface;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Exception\ElementNotFoundException;
use Ibexa\Behat\Browser\Exception\TimeoutException;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

final class TaxonomyTree extends Component
{
    private const EMPTY_SEARCH_TEXT = 'THISWILLRESULTINEMPTYRSEARCH';

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('header'))->assert()->textEquals('Tags');
        $this->clearSearch();
        $this->getHTMLPage()->setTimeout(10)->find($this->getLocator('tagItem'))->assert()->isVisible();
    }

    public function verifyTagNotExists(string $tagPath): void
    {
        Assert::assertFalse($this->tagExists($tagPath));
    }

    public function verifyTagExists(string $tagPath): void
    {
        Assert::assertTrue($this->tagExists($tagPath));
    }

    private function clearSearch(): void
    {
        $this->searchForTag('');
    }

    private function tagExists(string $tagPath): bool
    {
        $this->clearSearch();

        $pathParts = explode('/', $tagPath);

        try {
            $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('tagItem')));
        } catch (TimeoutException $e) {
            return false;
        }

        $searchedNode = $this->getHTMLPage();

        try {
            $this->searchForTag(end($pathParts));
        } catch (TimeoutException $e) {
            return false;
        }

        foreach ($pathParts as $indent => $pathPart) {
            try {
                $searchedNode = $this->findNestedTreeElement($searchedNode, $pathPart, $indent);
            } catch (ElementNotFoundException $e) {
                return false;
            } catch (TimeoutException $e) {
                return false;
            }

            if ($pathPart !== end($pathParts)) {
                $searchedNode = $searchedNode->find(new VisibleCSSLocator('', '.c-tb-list'));
            }
        }

        $this->getHTMLPage()->find($this->getLocator('search'))->setValue('');
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('tagItem')));

        return true;
    }

    private function searchForTag(string $tagName): void
    {
        $this->getHTMLPage()->find($this->getLocator('search'))->setValue(self::EMPTY_SEARCH_TEXT);
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('tagItem')));
        $this->getHTMLPage()->find($this->getLocator('search'))->setValue($tagName);
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('tagItem')));
    }

    private function findNestedTreeElement(BaseElementInterface $baseElement, string $searchedElementName, int $indent): ElementInterface
    {
        return $baseElement->findAll($this->getLocator('tagItem'))
                ->filter(static function (ElementInterface $element) use ($indent): bool {
                    return $element->findAll(
                        new CSSLocator('', sprintf('[style*="--indent:%d;"]', $indent))
                    )->any();
                })
                ->filter(static function (ElementInterface $element) use ($searchedElementName): bool {
                    return str_replace(' ', '', $element->find(
                        new VisibleCSSLocator('', '.c-tb-list-item-single__element')
                    )->getText()) === $searchedElementName;
                })
                ->first();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('header', '.ibexa-taxonomy-tree-container .c-tb-header__name-content'),
            new VisibleCSSLocator('optionsButton', '.c-tb-contextual-menu__toggler'),
            new VisibleCSSLocator('menuOption', '.c-tb-action-list__item'),
            new VisibleCSSLocator('tagItem', '.c-tb-list-item-single'),
            new VisibleCSSLocator('notExpandedTag', '.ibexa-taxonomy-tree-container__root .c-tb-list-item-single--has-sub-items:not(.c-tb-list-item-single--expanded)'),
            new VisibleCSSLocator('treeElement', '.ibexa-taxonomy-tree-container .c-tb-list-item-single__label'),
            new VisibleCSSLocator('search', '.c-tb-search .ibexa-input'),
        ];
    }
}
