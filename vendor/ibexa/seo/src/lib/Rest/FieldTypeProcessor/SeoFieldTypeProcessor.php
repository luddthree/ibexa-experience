<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Rest\FieldTypeProcessor;

use Ibexa\Contracts\Rest\FieldTypeProcessor;

final class SeoFieldTypeProcessor extends FieldTypeProcessor
{
    /**
     * @param array{value: string} $outgoingValueHash
     */
    public function postProcessValueHash($outgoingValueHash): string
    {
        return (string) $outgoingValueHash['value'];
    }
}
