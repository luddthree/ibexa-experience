<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Component\Preview;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class SingleColumnBaseFieldTypePreview extends Component implements SingleColumnFieldTypePreviewInterface
{
    protected LocatorInterface $parentLocator;

    public function verifyRemovedData(string $expectedRemovedData): void
    {
        $valueRemoved = implode(' ', $this->getRemovedElements());

        Assert::assertEquals($expectedRemovedData, $valueRemoved);
    }

    public function verifyAddedData(string $expectedAddedData): void
    {
        $valueAdded = implode(' ', $this->getAddedElements());

        Assert::assertEquals($expectedAddedData, $valueAdded);
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return in_array($fieldTypeIdentifier, [
            'ezstring',
            'ezrichtext',
            'ezauthor',
            'eztext',
            'ezurl',
            'ezselection',
            'ezboolean',
            'ezemail',
            'ezfloat',
            'ezisbn',
            'ezinteger',
            'ezkeyword',
            'ezmatrix',
            'ezgmaplocation',
            'ezcountry',
            'ezdate',
            'ezdatetime',
            'eztime',
        ]);
    }

    public function setParentLocator(LocatorInterface $locator): void
    {
        $this->parentLocator = $locator;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->parentLocator)->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('valueAdded', 'ins.diffmod,.ibexa-compared-part--added'),
            new VisibleCSSLocator('valueRemoved', 'del.diffmod,.ibexa-compared-part--removed'),
        ];
    }

    protected function getRemovedElements(): array
    {
        return $this->getHTMLPage()->find($this->parentLocator)->findAll($this->getLocator('valueRemoved'))->mapBy(new ElementTextMapper());
    }

    protected function getAddedElements(): array
    {
        return $this->getHTMLPage()->find($this->parentLocator)->findAll($this->getLocator('valueAdded'))->mapBy(new ElementTextMapper());
    }
}
