<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Template;

use Traversable;

final class ExpressionLanguageContext
{
    /**
     * @var array<mixed>
     */
    private array $parameters;

    /**
     * @param array<mixed> $parameters
     */
    public function __construct(iterable $parameters = [])
    {
        if ($parameters instanceof Traversable) {
            $parameters = iterator_to_array($parameters);
        }

        $this->parameters = $parameters;
    }

    /**
     * @return array<mixed>
     */
    public function all(): array
    {
        return $this->parameters;
    }
}
