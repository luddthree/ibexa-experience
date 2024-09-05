<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

class BannerBlock extends PageBuilderBlock
{
    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->addContent('Media/Images/BannerImage');
        $this->setInputField('URL', 'https://onet.pl');
        $this->submitForm();
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => 'https://onet.pl'];
    }

    public function getBlockType(): string
    {
        return 'Banner';
    }
}
