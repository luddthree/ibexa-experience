<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\Sql\Query;
use Webmozart\Assert\Assert;

final class SQLExecuteStep implements StepInterface
{
    /** @var \Ibexa\Migration\ValueObject\Sql\Query[] */
    public $queries;

    /**
     * @param \Ibexa\Migration\ValueObject\Sql\Query[] $queries
     */
    public function __construct(array $queries)
    {
        Assert::allIsInstanceOf($queries, Query::class);
        $this->queries = $queries;
    }
}

class_alias(SQLExecuteStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\SQLExecuteStep');
