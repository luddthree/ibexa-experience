<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Security\JWT\Token;

interface TokenEnricherInterface
{
    public function getIdentifier(): string;

    public function getContext(): string;

    /**
     * @return array<mixed>
     */
    public function getPayload(int $userId): array;

    /**
     * @param array<string, mixed> $fieldMap
     * @param array<mixed> $payload
     *
     * @return array<mixed>
     */
    public function resolveFieldMap(array $fieldMap, array &$payload): array;
}
