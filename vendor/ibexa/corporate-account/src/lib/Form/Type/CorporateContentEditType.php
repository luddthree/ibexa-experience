<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type;

use Ibexa\ContentForms\Form\Type\Content\ContentEditType;
use Symfony\Component\Form\AbstractType;

abstract class CorporateContentEditType extends AbstractType
{
    public function getParent(): string
    {
        return ContentEditType::class;
    }
}
