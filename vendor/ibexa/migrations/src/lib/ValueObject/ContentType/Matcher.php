<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentType;

use Ibexa\Migration\StepExecutor\ContentType\IdentifierFinder;

final class Matcher
{
    /** @deprecated use IdentifierFinder constant instead */
    public const CONTENT_TYPE_IDENTIFIER = IdentifierFinder::CONTENT_TYPE_IDENTIFIER;

    /** @var string */
    public $field;

    /** @var scalar */
    public $value;

    /**
     * @param scalar $value
     */
    public function __construct(string $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return scalar
     */
    public function getValue()
    {
        return $this->value;
    }
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\ContentType\Matcher class instead
 */
class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\ContentType\Match');

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\ContentType\Matcher');
