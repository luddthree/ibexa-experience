<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Validator;

use Symfony\Component\Validator\Constraint;

class ContainsScss extends Constraint
{
    public $message = 'The field contains an invalid SCSS code: {{ reason }}';
}

class_alias(ContainsScss::class, 'EzSystems\EzPlatformPageBuilder\Form\Validator\ContainsScss');
