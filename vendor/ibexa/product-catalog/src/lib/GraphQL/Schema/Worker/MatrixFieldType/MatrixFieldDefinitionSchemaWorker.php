<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\MatrixFieldType;

final class MatrixFieldDefinitionSchemaWorker extends AbstractMatrixFieldDefinitionSchemaWorker
{
    public function getInputTypeName(): string
    {
        return 'object';
    }

    public function getTypeNameClosure(): callable
    {
        return [$this->getNameHelper(), 'getMatrixFieldDefinitionType'];
    }
}
