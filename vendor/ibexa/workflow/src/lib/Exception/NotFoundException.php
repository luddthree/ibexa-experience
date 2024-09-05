<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Exception;

use Ibexa\Core\Base\Exceptions\NotFoundException as CoreNotFoundException;

class NotFoundException extends CoreNotFoundException
{
}

class_alias(NotFoundException::class, 'EzSystems\EzPlatformWorkflow\Exception\NotFoundException');
