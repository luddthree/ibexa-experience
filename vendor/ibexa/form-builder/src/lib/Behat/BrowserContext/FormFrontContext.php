<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\FormBuilder\Behat\Component\FormPreview;

class FormFrontContext implements Context
{
    /** @var \Ibexa\FormBuilder\Behat\Component\FormPreview */
    private $formFrontPage;

    public function __construct(FormPreview $formFrontPage)
    {
        $this->formFrontPage = $formFrontPage;
    }

    /**
     * @When I fill the form with data
     */
    public function fillForm(TableNode $data)
    {
        foreach ($data->getHash() as $row) {
            $this->formFrontPage->fillField($row['label'], $row['value']);
        }
    }

    /**
     * @When I submit the form
     */
    public function submitForm()
    {
        $this->formFrontPage->submit();
    }
}
