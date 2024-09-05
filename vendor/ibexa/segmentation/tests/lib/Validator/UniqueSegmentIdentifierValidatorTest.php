<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Tests\Validator;

use Ibexa\Segmentation\Exception\Persistence\SegmentNotFoundException;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use Ibexa\Segmentation\Validator\Constraints\UniqueSegmentIdentifier;
use Ibexa\Segmentation\Validator\Constraints\UniqueSegmentIdentifierValidator;
use Ibexa\Segmentation\Value\Persistence\Segment;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class UniqueSegmentIdentifierValidatorTest extends ConstraintValidatorTestCase
{
    private const UNIQUE_IDENTIFIER = 'unique';

    private const NOT_UNIQUE_IDENTIFIER = 'not-unique';

    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private HandlerInterface $handler;

    protected function createValidator(): UniqueSegmentIdentifierValidator
    {
        $this->handler = $this->createMock(HandlerInterface::class);

        return new UniqueSegmentIdentifierValidator($this->handler);
    }

    public function testValid(): void
    {
        $this
            ->handler
            ->method('loadSegmentByIdentifier')
            ->with(self::UNIQUE_IDENTIFIER)
            ->willThrowException(new SegmentNotFoundException(self::UNIQUE_IDENTIFIER));

        $this->validator->validate(self::UNIQUE_IDENTIFIER, new UniqueSegmentIdentifier());

        self::assertNoViolation();
    }

    public function testInvalid(): void
    {
        $this
            ->handler
            ->method('loadSegmentByIdentifier')
            ->with(self::NOT_UNIQUE_IDENTIFIER)
            ->willReturn(new Segment([
                'id' => 1,
                'identifier' => self::NOT_UNIQUE_IDENTIFIER,
                'name' => 'Not Unique',
            ]));

        $constraint = new UniqueSegmentIdentifier();
        $this->validator->validate(self::NOT_UNIQUE_IDENTIFIER, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ identifier }}', self::NOT_UNIQUE_IDENTIFIER)
            ->assertRaised();
    }
}
