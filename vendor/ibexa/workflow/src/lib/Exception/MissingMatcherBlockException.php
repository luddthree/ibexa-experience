<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Exception;

use RuntimeException;

class MissingMatcherBlockException extends RuntimeException
{
}

class_alias(MissingMatcherBlockException::class, 'EzSystems\EzPlatformWorkflow\Exception\MissingMatcherBlockException');
