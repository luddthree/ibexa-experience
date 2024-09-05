<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Exception;

use Exception;

/**
 * This exception should be throw when a table with Public Accesses can't be reached or ConnectionException occurs.
 */
class DataReadException extends Exception
{
}

class_alias(DataReadException::class, 'EzSystems\EzPlatformSiteFactory\Exception\DataReadException');
