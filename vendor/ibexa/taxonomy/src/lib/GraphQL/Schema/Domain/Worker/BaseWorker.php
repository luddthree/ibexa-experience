<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker;

use Ibexa\GraphQL\Schema\Worker;
use Ibexa\Taxonomy\GraphQL\Schema\NameHelperInterface;

/**
 * @internal
 */
abstract class BaseWorker implements Worker
{
    private NameHelperInterface $nameHelper;

    public function setNameHelper(NameHelperInterface $nameHelper): void
    {
        $this->nameHelper = $nameHelper;
    }

    protected function getNameHelper(): NameHelperInterface
    {
        return $this->nameHelper;
    }
}
