<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Exception;

use RuntimeException;

class MissingWorkflowTimelineEntryBlockException extends RuntimeException
{
}

class_alias(MissingWorkflowTimelineEntryBlockException::class, 'EzSystems\EzPlatformWorkflow\Exception\MissingWorkflowTimelineEntryBlockException');
