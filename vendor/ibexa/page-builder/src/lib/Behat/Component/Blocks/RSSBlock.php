<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

class RSSBlock extends PageBuilderBlock
{
    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->setInputField('URL', 'https://www.ibexa.co/rss/feed/my_feed');
        $this->setInputField('Limit', '3');
        $this->setInputField('Offset', '1');
        $this->submitForm();
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => '3'];
    }

    public function getBlockType(): string
    {
        return 'RSS';
    }
}
