<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\OutputType;

use Ibexa\Personalization\Value\Content\AbstractItemType;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @internal
 */
interface OutputTypeResolverInterface
{
    public function resolveFromParameterBag(ParameterBag $parameterBag): AbstractItemType;
}
