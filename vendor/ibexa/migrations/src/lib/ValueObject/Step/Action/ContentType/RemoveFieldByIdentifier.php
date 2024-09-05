<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\ContentType;

use Ibexa\Migration\ValueObject\Step\Action;

final class RemoveFieldByIdentifier implements Action
{
    public const TYPE = 'remove_field_by_identifier';

    /** @var string */
    private $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->identifier;
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}

class_alias(RemoveFieldByIdentifier::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\ContentType\RemoveFieldByIdentifier');
