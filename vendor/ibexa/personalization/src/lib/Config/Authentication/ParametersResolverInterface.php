<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\Authentication;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Personalization\Value\Authentication\Parameters;

/**
 * @internal
 */
interface ParametersResolverInterface
{
    public function resolveForContent(Content $content): ?Parameters;

    /**
     * Returns array of authentication parameters for each language.
     *
     * @return array<string, \Ibexa\Personalization\Value\Authentication\Parameters>
     */
    public function resolveAllForContent(Content $content): array;
}
