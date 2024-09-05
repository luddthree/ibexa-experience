<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Tests\Validator;

use Ibexa\Segmentation\Exception\Persistence\SegmentGroupNotFoundException;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use Ibexa\Segmentation\Validator\Constraints\UniqueSegmentGroupIdentifier;
use Ibexa\Segmentation\Validator\Constraints\UniqueSegmentGroupIdentifierValidator;
use Ibexa\Segmentation\Value\Persistence\SegmentGroup;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class UniqueSegmentGroupIdentifierValidatorTest extends ConstraintValidatorTestCase
{
    private const UNIQUE_IDENTIFIER = 'unique';

    private const NOT_UNIQUE_IDENTIFIER = 'not-unique';

    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private HandlerInterface $handler;

    protected function createValidator(): UniqueSegmentGroupIdentifierValidator
    {
        $this->handler = $this->createMock(HandlerInterface::class);

        return new UniqueSegmentGroupIdentifierValidator($this->handler);
    }

    public function testValid(): void
    {
        $this
            ->handler
            ->method('loadSegmentGroupByIdentifier')
            ->with(self::UNIQUE_IDENTIFIER)
            ->willThrowException(new SegmentGroupNotFoundException(self::UNIQUE_IDENTIFIER));

        $this->validator->validate(self::UNIQUE_IDENTIFIER, new UniqueSegmentGroupIdentifier());

        self::assertNoViolation();
    }

    public function testInvalid(): void
    {
        $this
            ->handler
            ->method('loadSegmentGroupByIdentifier')
            ->with(self::NOT_UNIQUE_IDENTIFIER)
            ->willReturn(new SegmentGroup([
                'id' => 1,
                'identifier' => self::NOT_UNIQUE_IDENTIFIER,
                'name' => 'Not Unique',
            ]));

        $constraint = new UniqueSegmentGroupIdentifier();
        $this->validator->validate(self::NOT_UNIQUE_IDENTIFIER, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ identifier }}', self::NOT_UNIQUE_IDENTIFIER)
            ->assertRaised();
    }
}
