<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;

class FormPreview extends Component implements PagePreviewInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(Session $session, ContentTypeService $contentTypeService)
    {
        parent::__construct($session);
        $this->contentTypeService = $contentTypeService;
    }

    public function fillField(string $label, string $value)
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('formField'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('label'), $label))
            ->find($this->getLocator('input'))
            ->setValue($value);
    }

    public function submit()
    {
        $this->getHTMLPage()->find($this->getLocator('submit'))->click();
    }

    public function isEmpty(): bool
    {
        return $this->getHTMLPage()->findAll($this->getLocator('label'))->empty();
    }

    public function verifyIsLoaded(): void
    {
        $this->verifyPreviewData();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('submit', 'form[name|=form] button[type="submit"]'),
            new VisibleCSSLocator('label', 'label[for],.col-form-label,[type=submit]'),
            new VisibleCSSLocator('input', 'input'),
            new VisibleCSSLocator('formField', 'form[name|=form]'),
            new VisibleCSSLocator('submitMessage', 'h3'),
        ];
    }

    public function setExpectedPreviewData(array $data)
    {
    }

    public function verifyPreviewData()
    {
        $this->getHTMLPage()->find($this->getLocator('formField'))->find($this->getLocator('label'))->assert()->isVisible();
        $this->getHTMLPage()->find($this->getLocator('formField'))->find($this->getLocator('submit'))->assert()->isVisible();
    }

    public function supports(string $contentTypeIdentifier, string $viewType): bool
    {
        return strtolower($contentTypeIdentifier) === 'form' ||
            $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier)->hasFieldDefinitionOfType('ezform');
    }
}
