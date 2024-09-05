<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\DateAndTimePopup;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class VideoBlock extends PageBuilderBlock
{
    public function __construct(
        Session $session,
        DateAndTimePopup $dateAndTimePopup,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        IbexaDropdown $ibexaDropdown
    ) {
        parent::__construct($session, $dateAndTimePopup, $universalDiscoveryWidget, $ibexaDropdown);
        $this->locators->replace(new VisibleCSSLocator('embedButton', '.ibexa-pb-block-config__fields .ibexa-pb-block-embed-field--video button'));
    }

    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->addContent('Media/Images');
        $this->submitForm();
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => 'Images'];
    }

    public function getBlockType(): string
    {
        return 'Video';
    }
}
