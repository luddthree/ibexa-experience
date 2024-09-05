<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Migration\Action\PutBlockOntoPage;

use Ibexa\FieldTypePage\Migration\Data\ZonesBlocksList;
use Ibexa\Migration\ValueObject\Step\Action;

/**
 * @internal
 */
final class PutBlockOntoPageAction implements Action
{
    public const ACTION_NAME = 'ibexa.landing_page.put_block_onto_page';

    private ZonesBlocksList $zonesBlocksList;

    private string $fieldDefinitionIdentifier;

    public function __construct(string $fieldDefinitionIdentifier, ZonesBlocksList $zones)
    {
        $this->fieldDefinitionIdentifier = $fieldDefinitionIdentifier;
        $this->zonesBlocksList = $zones;
    }

    /**
     * @return array{}
     */
    public function getValue(): array
    {
        return [];
    }

    public function getZoneBlocksList(): ZonesBlocksList
    {
        return $this->zonesBlocksList;
    }

    public function getFieldDefinitionIdentifier(): string
    {
        return $this->fieldDefinitionIdentifier;
    }

    public function getSupportedType(): string
    {
        return self::ACTION_NAME;
    }
}
