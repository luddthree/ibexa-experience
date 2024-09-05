<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

class ResponseClassNotImplementedException extends TransferException
{
}

class_alias(ResponseClassNotImplementedException::class, 'EzSystems\EzRecommendationClient\Exception\ResponseClassNotImplementedException');
