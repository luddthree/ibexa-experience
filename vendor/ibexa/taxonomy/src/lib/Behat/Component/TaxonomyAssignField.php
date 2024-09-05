<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class TaxonomyAssignField extends FieldTypeComponent
{
    protected TagPicker $tagPicker;

    public function __construct(Session $session, TagPicker $tagPicker)
    {
        parent::__construct($session);
        $this->tagPicker = $tagPicker;
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ibexa_taxonomy_entry_assignment';
    }

    /**
     * @param array{'value': string} $parameters
     */
    public function setValue(array $parameters): void
    {
        while ($this->getHTMLPage()->find($this->parentLocator)
                ->findAll($this->getLocator('deleteTagButton'))
                ->any()) {
            $this->getHTMLPage()->find($this->parentLocator)->find($this->getLocator('deleteTagButton'))->click();
        }

        if (trim($parameters['value']) == 'empty') {
            return;
        }

        $this->getHTMLPage()
            ->find($this->parentLocator)
            ->find($this->getLocator('selectButton'))
            ->click();

        foreach (explode(',', $parameters['value']) as $tagPath) {
            $this->selectSingleTag($tagPath);
        }

        $this->tagPicker->confirm();
    }

    private function selectSingleTag(string $tagPath): void
    {
        $this->tagPicker->setExpectedHeader('Select tags');
        $this->tagPicker->setExpectedConfirmMessage('Select');
        $this->tagPicker->verifyIsLoaded();
        $this->tagPicker->selectTag($tagPath);
    }

    /**
     * @return array<string>
     */
    public function getValue(): array
    {
        return [
            $this->getHTMLPage()
                ->find($this->parentLocator)
                ->find($this->getLocator('currentlySelected'))
                ->getText(),
        ];
    }

    /**
     * @param array{'value': string} $values
     */
    public function verifyValueInItemView(array $values): void
    {
        $this->getHTMLPage()->find($this->parentLocator)->assert()->textEquals($values['value']);
    }

    /**
     * @param array{'value': string} $values
     */
    public function verifyValueInEditView(array $values): void
    {
        Assert::assertEquals($values['value'], $this->getValue());
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->parentLocator)
            ->find($this->getLocator('infoText'))
            ->assert()->textEquals('Select tags');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('infoText', '.ibexa-tag-view-select__info'),
            new VisibleCSSLocator('currentlySelected', '.ibexa-tag-view-select__selected-item-tag'),
            new VisibleCSSLocator('selectButton', '.ibexa-btn'),
            new VisibleCSSLocator('deleteTagButton', '.ibexa-tag-view-select__selected-item-tag-remove-btn'),
        ];
    }
}
