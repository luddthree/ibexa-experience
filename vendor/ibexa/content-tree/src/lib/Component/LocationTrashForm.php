<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ContentTree\Component;

use Symfony\Component\Form\FormInterface;

class LocationTrashForm extends FormComponent
{
    public function getForm(): FormInterface
    {
        return $this->formFactory->trashLocation(null, 'content_tree_location_trash');
    }
}
