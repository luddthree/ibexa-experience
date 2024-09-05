<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Icons;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaIconsBundle extends Bundle
{
}

class_alias(IbexaIconsBundle::class, 'Ibexa\Platform\Bundle\Icons\IbexaPlatformIconsBundle');
