<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema\Worker;

abstract class BaseWorker
{
    /** @var \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper */
    private $nameHelper;

    /**
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper $nameHelper
     */
    public function setNameHelper(NameHelper $nameHelper): void
    {
        $this->nameHelper = $nameHelper;
    }

    /**
     * @return \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper
     */
    protected function getNameHelper(): NameHelper
    {
        return $this->nameHelper;
    }
}

class_alias(BaseWorker::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\Worker\BaseWorker');
