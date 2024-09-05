<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\News;

use Exception;
use Ibexa\Contracts\Dashboard\Exceptions\RSSFeedException;

final class FeedException extends Exception implements RSSFeedException
{
}
