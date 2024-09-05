<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FormSubmission;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry;
use Ibexa\FormBuilder\FormSubmission\FormSubmissionService;
use Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway;
use Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionGateway;
use PHPUnit\Framework\TestCase;

final class FormSubmissionServiceTest extends TestCase
{
    private const EXAMPLE_FORM_SUBMISSION_ID = 1;

    public function testLoadByIdThrowsNotFoundException(): void
    {
        $gateway = $this->createMock(FormSubmissionGateway::class);
        $gateway->method('loadById')->with(self::EXAMPLE_FORM_SUBMISSION_ID)->willReturn([]);

        $service = new FormSubmissionService(
            $this->createMock(Repository::class),
            $gateway,
            $this->createMock(FormSubmissionDataGateway::class),
            $this->createMock(FieldSubmissionConverterRegistry::class)
        );

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("FormSubmission' with identifier '1'");

        $service->loadById(self::EXAMPLE_FORM_SUBMISSION_ID);
    }
}

class_alias(FormSubmissionServiceTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FormSubmission\FormSubmissionServiceTest');
