<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class FileField extends FormBuilderField
{
    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    public function __construct(Session $session, IbexaDropdown $ibexaDropdown, UniversalDiscoveryWidget $universalDiscoveryWidget)
    {
        parent::__construct($session, $ibexaDropdown);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
    }

    protected function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('selectLocationButton', '.ibexa-fb-form-field-config-location__btn-select-path'),
                new VisibleCSSLocator('selectedLocationName', '.ibexa-tag-view-select__selected-item-tag'),
            ]
        );
    }

    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom file');
        $this->fillField('Help text', 'Custom help');
        $this->selectLocation('Select file upload location', 'Media/Images');
        $this->switchTab('Validation');
        $this->check('Required');
        $this->fillField('Maximum allowed file size (MB)', '1');
        $this->fillField('Allowed file extensions', 'jpg');
    }

    public function selectLocation(string $label, string $path): void
    {
        $this->switchIntoSettingsIframe();
        $this->getField($label)
            ->find($this->getLocator('selectLocationButton'))
            ->click();
        $this->switchBackToEditor();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($path);
        $this->universalDiscoveryWidget->confirm();
    }

    public function getLocation(string $label): string
    {
        $this->switchIntoSettingsIframe();
        $value = $this->getField($label)
            ->find($this->getLocator('selectedLocationName'))
            ->getText();
        $this->switchBackToEditor();

        return $value;
    }

    public function getType(): string
    {
        return 'File';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom file',
            $this->getFieldValue('Name'),
            'Wrong "Name" field value.'
        );
        Assert::assertEquals(
            'Custom help',
            $this->getFieldValue('Help text'),
            'Wrong "Help text" field value.'
        );
        Assert::assertStringContainsString(
            'Images',
            $this->getLocation('Select file upload location'),
            'Wrong Location field value.'
        );
        $this->switchTab('Validation');
        Assert::assertTrue(
            $this->getCheckValue('Required'),
            'Wrong "Required" field value.'
        );
        Assert::assertEquals(
            '1',
            $this->getFieldValue('Maximum allowed file size (MB)'),
            'Wrong "Maximum allowed file size (MB)" field value.'
        );
        Assert::assertEquals(
            'jpg',
            $this->getFieldValue('Allowed file extensions'),
            'Wrong "Allowed file extensions" field value.'
        );
    }
}
