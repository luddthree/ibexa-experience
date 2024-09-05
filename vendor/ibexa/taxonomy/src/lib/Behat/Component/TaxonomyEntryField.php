<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\Component;

final class TaxonomyEntryField extends TaxonomyAssignField
{
    private const SELECT_TEXT = 'Select parent';

    public function getFieldTypeIdentifier(): string
    {
        return 'ibexa_taxonomy_entry';
    }

    /**
     * @param array{'value': string} $parameters
     */
    public function setValue(array $parameters): void
    {
        $tagPath = $parameters['value'];
        $tagPathParts = explode('/', $tagPath);
        $tagName = end($tagPathParts);

        $this->getHTMLPage()
            ->find($this->parentLocator)
            ->find($this->getLocator('selectButton'))
            ->click();

        $this->tagPicker->setExpectedHeader(self::SELECT_TEXT);
        $this->tagPicker->setExpectedConfirmMessage(self::SELECT_TEXT);
        $this->tagPicker->verifyIsLoaded();
        $this->tagPicker->selectTag($parameters['value']);
        $this->tagPicker->confirm();

        $this->getHTMLPage()
            ->find($this->parentLocator)
            ->find($this->getLocator('currentlySelected'))
            ->assert()->textEquals($tagName);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->parentLocator)
            ->find($this->getLocator('infoText'))
            ->assert()->textEquals(self::SELECT_TEXT);
    }
}
