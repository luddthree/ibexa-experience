<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Connector\Dam\ParamConverter;

use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

abstract class AbstractParamConverterTest extends TestCase
{
    /**
     * @return \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter|\PHPUnit\Framework\MockObject\MockObject
     */
    public function createConfiguration(string $class = null, string $name = null): ParamConverter
    {
        $config = $this
            ->getMockBuilder(ParamConverter::class)
            ->setMethods(['getClass', 'getAliasName', 'getOptions', 'getName', 'allowArray', 'isOptional'])
            ->disableOriginalConstructor()
            ->getMock();

        if ($name !== null) {
            $config->expects($this->any())
                ->method('getName')
                ->willReturn($name);
        }
        if ($class !== null) {
            $config->expects($this->any())
                ->method('getClass')
                ->willReturn($class);
        }

        return $config;
    }
}

class_alias(AbstractParamConverterTest::class, 'Ibexa\Platform\Tests\Bundle\Connector\Dam\ParamConverter\AbstractParamConverterTest');
