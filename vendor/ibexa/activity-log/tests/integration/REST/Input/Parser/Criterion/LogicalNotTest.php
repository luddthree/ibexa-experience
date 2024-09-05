<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalNot as LogicalNotCriterion;

/**
 * @covers \Ibexa\ActivityLog\REST\Input\Parser\Criterion\LogicalNot
 */
final class LogicalNotTest extends AbstractCompositeTestCase
{
    protected function configureParsingDispatcher(): void
    {
        $this->addParser(
            'application/vnd.ibexa.api.internal.activity_log.criterion.foo',
            ['value' => 'some_value']
        );
    }

    protected function getXml(): string
    {
        return <<<XML
            <criterion type="not">
                <criterion type="foo">
                  <value>some_value</value>
                </criterion>
            </criterion>
            XML;
    }

    protected function getJson(): string
    {
        return <<<JSON
            {
              "type": "not",
              "criterion": {
                "type": "foo",
                "value": "some_value"
              }
            }
            JSON;
    }

    protected function getExpectedCriterion(): CriterionInterface
    {
        return new LogicalNotCriterion($this->createMock(CriterionInterface::class));
    }
}
