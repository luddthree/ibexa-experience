<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Search;

class Query
{
    /** @var string */
    protected $phrase;

    public function __construct(string $phrase)
    {
        $this->phrase = $phrase;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }
}

class_alias(Query::class, 'Ibexa\Platform\Contracts\Connector\Dam\Search\Query');
