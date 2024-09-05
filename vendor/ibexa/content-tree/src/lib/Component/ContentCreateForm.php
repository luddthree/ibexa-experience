<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ContentTree\Component;

use Symfony\Component\Form\FormInterface;

class ContentCreateForm extends FormComponent
{
    public function getForm(): FormInterface
    {
        return $this->formFactory->createContent(null, 'content_tree_content_create');
    }
}
