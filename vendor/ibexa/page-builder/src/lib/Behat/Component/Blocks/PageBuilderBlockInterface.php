<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

interface PageBuilderBlockInterface
{
    public function setDefaultTestingConfiguration(string $blockName);

    public function getDefaultPreviewData(): array;

    public function setInputField(string $fieldLabel, string $value): void;

    public function switchTab(string $tabName): void;

    public function setLayout(string $layoutName);

    public function setRevealDate(\DateTime $date);

    public function setHideDate(\DateTime $date);

    public function submitForm(): void;

    public function submitFormWithNameLengthAssertion(): void;

    public function cancelForm(): void;

    public function addContent(string $locationPath): void;

    public function getBlockType(): string;
}
