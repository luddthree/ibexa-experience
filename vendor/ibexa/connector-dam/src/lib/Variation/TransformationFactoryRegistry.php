<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Variation;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\TransformationFactory;

interface TransformationFactoryRegistry
{
    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getFactory(AssetSource $source): TransformationFactory;
}

class_alias(TransformationFactoryRegistry::class, 'Ibexa\Platform\Connector\Dam\Variation\TransformationFactoryRegistry');
