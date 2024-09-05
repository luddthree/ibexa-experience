<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ConnectorQualifio;

use Ibexa\Contracts\ConnectorQualifio\Value\QualifioTokenPayloadValue as Value;

interface QualifioFieldMapResolverInterface
{
    /**
     * @param mixed $value
     */
    public function resolveFieldMapIdentifier(string $context, string $enricherIdentifier, string $fieldIdentifier, $value): ?Value;
}
