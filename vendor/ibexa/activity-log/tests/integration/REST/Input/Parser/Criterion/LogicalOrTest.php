<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalOr;

/**
 * @covers \Ibexa\ActivityLog\REST\Input\Parser\Criterion\LogicalOr
 */
final class LogicalOrTest extends AbstractCompositeTestCase
{
    protected function configureParsingDispatcher(): void
    {
        $this->addParser('application/vnd.ibexa.api.internal.activity_log.criterion.foo');
        $this->addParser('application/vnd.ibexa.api.internal.activity_log.criterion.bar');
    }

    protected function getExpectedCriterion(): CriterionInterface
    {
        return new LogicalOr([
            $this->createMock(CriterionInterface::class),
            $this->createMock(CriterionInterface::class),
        ]);
    }

    protected function getJson(): string
    {
        return <<<JSON
            {"type": "or", "criteria": [
                {"type": "foo"},
                {"type": "bar"}
            ]}
            JSON;
    }

    protected function getXml(): string
    {
        return <<<XML
            <criterion type="or">
                <criterion type="foo" />
                <criterion type="bar" />
            </criterion>
            XML;
    }
}
