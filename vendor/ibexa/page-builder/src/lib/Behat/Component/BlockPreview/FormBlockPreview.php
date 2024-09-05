<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\FormBuilder\Behat\Component\FormPreview;

class FormBlockPreview extends BlockPreview
{
    /** @var string */
    private $expectedHeader;

    /** @var \Ibexa\FormBuilder\Behat\Component\FormPreview */
    private $formPreview;

    public function __construct(Session $session, FormPreview $formPreview)
    {
        parent::__construct($session);
        $this->formPreview = $formPreview;
    }

    public function setExpectedData(array $data): void
    {
        $this->expectedHeader = $data['parameter1'];
    }

    public function verifyPreview(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('formHeader'))
            ->assert()->textEquals($this->expectedHeader);

        $this->formPreview->verifyPreviewData();
    }

    public function getSupportedBlockType(): string
    {
        return 'Form';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('formHeader', '.block-form span.ezstring-field'),
        ];
    }
}
