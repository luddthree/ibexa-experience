<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;

class LandingPagePreview extends Component implements PagePreviewInterface
{
    private $expectedPageTitle;

    /** @var \Ibexa\PageBuilder\Behat\Component\BlockPreview\BlockPreview[] */
    protected $blockPreviews;

    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(Session $session, ContentTypeService $contentTypeService)
    {
        parent::__construct($session);
        $this->contentTypeService = $contentTypeService;
    }

    public function setExpectedPreviewData(array $data)
    {
        $this->expectedPageTitle = $data['title'];
        $this->blockPreviews = array_key_exists('blocks', $data) ? $data['blocks'] : [];
    }

    public function supports(string $contentTypeIdentifier, string $viewType): bool
    {
        return strtolower($contentTypeIdentifier) === 'landing_page' ||
            $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier)->hasFieldDefinitionOfType('ezlandingpage');
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', 'body > h2, h1 .ezstring-field, .ibexa-store-container > h2'),
            ];
    }

    public function verifyPreviewData()
    {
        $this->getHTMLPage()
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals($this->expectedPageTitle);

        foreach ($this->blockPreviews as $blockPreview) {
            $blockPreview->verifyPreview();
        }
    }

    public function verifyIsLoaded(): void
    {
        $this->verifyPreviewData();
    }
}
