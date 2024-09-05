<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use Exception;

abstract class ExportException extends Exception implements IbexaPersonalizationException
{
}

class_alias(ExportException::class, 'EzSystems\EzRecommendationClient\Exception\ExportException');
