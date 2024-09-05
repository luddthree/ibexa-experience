<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\GraphQL\Schema\Domain\Worker;

use Ibexa\GraphQL\Schema\Domain\NameValidator;
use Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker\BaseWorker;
use Ibexa\Taxonomy\GraphQL\Schema\NameHelperInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractWorkerTest extends TestCase
{
    protected BaseWorker $worker;

    /** @var \Ibexa\Taxonomy\GraphQL\Schema\NameHelperInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected NameHelperInterface $nameHelper;

    protected function setUp(): void
    {
        $this->nameHelper = $this->createMock(NameHelperInterface::class);

        $this->worker = $this->getWorker();
        $this->worker->setNameHelper($this->nameHelper);
    }

    /**
     * @return \Ibexa\GraphQL\Schema\Domain\NameValidator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createNameValidatorMock(): NameValidator
    {
        $nameValidator = $this->createMock(NameValidator::class);
        $nameValidator
            ->method('isValidName')
            ->willReturn(true);

        return $nameValidator;
    }

    abstract protected function getWorker(): BaseWorker;
}
