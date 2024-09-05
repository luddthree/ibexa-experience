<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Context;

use ArrayObject;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Overblog\GraphQLBundle\Definition\Argument;

class GraphQlQueryBlockContext implements BlockContextInterface
{
    public const INTENT = 'view';

    /** @var \Overblog\GraphQLBundle\Definition\Argument */
    private $fieldArgs;

    /** @var \ArrayObject */
    private $queryContext;

    /**
     * @param \Overblog\GraphQLBundle\Definition\Argument $fieldArgs
     * @param \ArrayObject $queryContext
     */
    public function __construct(Argument $fieldArgs, ArrayObject $queryContext)
    {
        $this->fieldArgs = $fieldArgs;
        $this->queryContext = $queryContext;
    }

    /**
     * @return \Overblog\GraphQLBundle\Definition\Argument
     */
    public function getFieldArgs(): Argument
    {
        return $this->fieldArgs;
    }

    /**
     * @return \ArrayObject
     */
    public function getQueryContext(): ArrayObject
    {
        return $this->queryContext;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page
    {
        return $this->queryContext['Page'];
    }
}

class_alias(GraphQlQueryBlockContext::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Context\GraphQlQueryBlockContext');
