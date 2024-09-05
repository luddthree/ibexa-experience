<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Mapper;

use Ibexa\Contracts\Core\Persistence\ValueObject as PersistenceValueObject;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

interface ValueMapper
{
    /**
     * Maps value objects from Persistence to API.
     *
     * @param \Ibexa\Contracts\Core\Persistence\ValueObject $object
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ValueObject
     */
    public function fromPersistenceValue(PersistenceValueObject $object): ValueObject;
}

class_alias(ValueMapper::class, 'EzSystems\EzPlatformWorkflow\Mapper\ValueMapper');
