<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Security\JWT\Token;

use Ibexa\ConnectorQualifio\Service\QualifioFieldMapResolver;
use Ibexa\Contracts\ConnectorQualifio\Value\QualifioTokenPayloadValue;

abstract class AbstractTokenEnricher implements TokenEnricherInterface
{
    private QualifioFieldMapResolver $fieldMapResolver;

    public function __construct(
        QualifioFieldMapResolver $fieldMapResolver
    ) {
        $this->fieldMapResolver = $fieldMapResolver;
    }

    public function resolveFieldMap(array $fieldMap, array &$payload): array
    {
        foreach ($fieldMap as $fieldIdentifier => $fieldValue) {
            $payloadValue = $this->fieldMapResolver->resolveFieldMapIdentifier(
                $this->getContext(),
                $this->getIdentifier(),
                $fieldIdentifier,
                $fieldValue,
            );

            if ($payloadValue !== null) {
                $payload = $this->attachToPayload($payload, $payloadValue);
            }
        }

        return $payload;
    }

    /**
     * @param array<mixed> $payload
     *
     * @phpstan-return array<
     *     string,
     *     array<string, mixed>,
     * >
     */
    private function attachToPayload(array $payload, QualifioTokenPayloadValue $value): array
    {
        $context = $value->getContext();
        $identifier = $value->getIdentifier();
        $base = [
            $context => [
                $identifier => $value->getValue(),
            ],
        ];

        return array_merge_recursive($base, $payload);
    }
}
