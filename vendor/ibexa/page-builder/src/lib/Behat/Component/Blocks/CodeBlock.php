<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

class CodeBlock extends PageBuilderBlock
{
    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->setInputField('Content', '<h1>TestHeader</h1>');
        $this->submitForm();
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => '<h1>TestHeader</h1>'];
    }

    public function getBlockType(): string
    {
        return 'Code';
    }
}
