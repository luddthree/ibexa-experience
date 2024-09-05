<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\CorporateAccount\Event\Application\BeforeCreateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\BeforeDeleteApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\BeforeUpdateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\CreateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\DeleteApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\UpdateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService as ApplicationServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationUpdateStruct;
use Ibexa\CorporateAccount\Event\ApplicationService;

final class ApplicationServiceTest extends AbstractServiceTest
{
    protected function getEventServiceClass(): string
    {
        return ApplicationService::class;
    }

    protected function getServiceInterface(): string
    {
        return ApplicationServiceInterface::class;
    }

    public function getTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);

        return [
            'test application update' => [
                'updateApplication',
                [
                    BeforeUpdateApplicationEvent::class => static function (BeforeUpdateApplicationEvent $event) {},
                    UpdateApplicationEvent::class => static function (UpdateApplicationEvent $event) {},
                ],
                [
                    new Application($contentMock),
                    new ApplicationUpdateStruct(),
                ],
                new Application($contentMock),
            ],
            'test application create' => [
                'createApplication',
                [
                    BeforeCreateApplicationEvent::class => static function (BeforeCreateApplicationEvent $event) {},
                    CreateApplicationEvent::class => static function (CreateApplicationEvent $event) {},
                ],
                [
                    new ApplicationCreateStruct(),
                ],
                new Application($contentMock),
            ],
            'test application delete' => [
                'deleteApplication',
                [
                    BeforeDeleteApplicationEvent::class => static function (BeforeDeleteApplicationEvent $event) {},
                    DeleteApplicationEvent::class => static function (DeleteApplicationEvent $event) {},
                ],
                [
                    new Application($contentMock),
                ],
            ],
        ];
    }

    public function getInterruptedTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);
        $interruptedReturn = new Application($contentMock);

        return [
            'test interrupted application update' => [
                'updateApplication',
                [
                    BeforeUpdateApplicationEvent::class => static function (BeforeUpdateApplicationEvent $event) use ($interruptedReturn) {
                        $event->setUpdatedApplication($interruptedReturn);
                        $event->stopPropagation();
                    },
                    UpdateApplicationEvent::class => static function (UpdateApplicationEvent $event) {
                        self::fail(
                            BeforeUpdateApplicationEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . UpdateApplicationEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Application($contentMock),
                    new ApplicationUpdateStruct(),
                ],
                new Application($contentMock),
                $interruptedReturn,
            ],
            'test interrupted application create' => [
                'createApplication',
                [
                    BeforeCreateApplicationEvent::class => static function (BeforeCreateApplicationEvent $event) use ($interruptedReturn) {
                        $event->setApplication($interruptedReturn);
                        $event->stopPropagation();
                    },
                    CreateApplicationEvent::class => static function (CreateApplicationEvent $event) {
                        self::fail(
                            BeforeCreateApplicationEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . CreateApplicationEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new ApplicationCreateStruct(),
                ],
                new Application($contentMock),
                $interruptedReturn,
            ],
            'test interrupted application delete' => [
                'deleteApplication',
                [
                    BeforeDeleteApplicationEvent::class => static function (BeforeDeleteApplicationEvent $event) {
                        $event->stopPropagation();
                    },
                    DeleteApplicationEvent::class => static function (DeleteApplicationEvent $event) {},
                ],
                [
                    new Application($contentMock),
                ],
            ],
        ];
    }
}
