<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Service;

use Ibexa\Contracts\ConnectorQualifio\QualifioFieldMapResolverInterface;
use Ibexa\Contracts\ConnectorQualifio\Value\QualifioTokenPayloadValue;

final class QualifioFieldMapResolver implements QualifioFieldMapResolverInterface
{
    /** @var array<string, array<string, array<string, mixed>>> */
    private array $variableMap;

    /**
     * @param array<string, array<string, array<string, mixed>>> $variableMap
     */
    public function __construct(
        array $variableMap
    ) {
        $this->variableMap = $variableMap;
    }

    public function resolveFieldMapIdentifier(
        string $context,
        string $enricherIdentifier,
        string $fieldIdentifier,
        $value
    ): ?QualifioTokenPayloadValue {
        if (isset($this->variableMap[$context][$enricherIdentifier])
            && array_key_exists($fieldIdentifier, $this->variableMap[$context][$enricherIdentifier])
        ) {
            $identifier = $this->variableMap[$context][$enricherIdentifier][$fieldIdentifier];

            return new QualifioTokenPayloadValue($context, $identifier, $value);
        }

        return null;
    }
}
