<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\User;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\Generator\AbstractCriterionFactory;

final class CriterionFactory extends AbstractCriterionFactory
{
    protected function getCommonCriteria(): array
    {
        return [new Criterion\ContentTypeIdentifier('user')];
    }
}

class_alias(CriterionFactory::class, 'Ibexa\Platform\Migration\Generator\User\CriterionFactory');
