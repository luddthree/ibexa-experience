<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener;

use Ibexa\Bundle\Core\EventListener\ContentDownloadRouteReferenceListener;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFileValue;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\Event\RouteReferenceGenerationEvent;
use Ibexa\Core\MVC\Symfony\Routing\RouteReference;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

class ContentDownloadRouteReferenceListenerTest extends TestCase
{
    /** @var \Ibexa\Core\Helper\TranslationHelper|\PHPUnit\Framework\MockObject\MockObject */
    protected $translationHelperMock;

    protected function setUp(): void
    {
        $this->translationHelperMock = $this->createMock(TranslationHelper::class);
    }

    public function testIgnoresOtherRoutes()
    {
        $routeReference = new RouteReference('some_route');
        $event = new RouteReferenceGenerationEvent($routeReference, new Request());
        $eventListener = $this->getListener();

        $eventListener->onRouteReferenceGeneration($event);

        self::assertEquals('some_route', $routeReference->getRoute());
    }

    public function testThrowsExceptionOnBadContentParameter()
    {
        $this->expectException(\InvalidArgumentException::class);

        $routeReference = new RouteReference(
            ContentDownloadRouteReferenceListener::ROUTE_NAME,
            [
                ContentDownloadRouteReferenceListener::OPT_CONTENT => new stdClass(),
                ContentDownloadRouteReferenceListener::OPT_FIELD_IDENTIFIER => null,
            ]
        );
        $event = new RouteReferenceGenerationEvent($routeReference, new Request());
        $eventListener = $this->getListener();

        $eventListener->onRouteReferenceGeneration($event);
    }

    public function testThrowsExceptionOnBadFieldIdentifier()
    {
        $this->expectException(\InvalidArgumentException::class);

        $content = new Content(
            [
                'internalFields' => [],
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(['id' => 1, 'mainLanguageCode' => 'eng-GB']),
                    ]
                ),
            ]
        );

        $routeReference = new RouteReference(
            ContentDownloadRouteReferenceListener::ROUTE_NAME,
            [
                ContentDownloadRouteReferenceListener::OPT_CONTENT => $content,
                ContentDownloadRouteReferenceListener::OPT_FIELD_IDENTIFIER => 'field',
            ]
        );
        $event = new RouteReferenceGenerationEvent($routeReference, new Request());
        $eventListener = $this->getListener();

        $eventListener->onRouteReferenceGeneration($event);
    }

    public function testGeneratesCorrectRouteReference()
    {
        $content = $this->getCompleteContent();

        $routeReference = new RouteReference(
            ContentDownloadRouteReferenceListener::ROUTE_NAME,
            [
                ContentDownloadRouteReferenceListener::OPT_CONTENT => $content,
                ContentDownloadRouteReferenceListener::OPT_FIELD_IDENTIFIER => 'file',
            ]
        );
        $event = new RouteReferenceGenerationEvent($routeReference, new Request());
        $eventListener = $this->getListener();

        $this
            ->translationHelperMock
            ->expects($this->once())
            ->method('getTranslatedField')
            ->will($this->returnValue($content->getField('file', 'eng-GB')));
        $eventListener->onRouteReferenceGeneration($event);

        self::assertEquals('42', $routeReference->get(ContentDownloadRouteReferenceListener::OPT_CONTENT_ID));
        self::assertEquals('file', $routeReference->get(ContentDownloadRouteReferenceListener::OPT_FIELD_IDENTIFIER));
        self::assertEquals('Test-file.pdf', $routeReference->get(ContentDownloadRouteReferenceListener::OPT_DOWNLOAD_NAME));
    }

    public function testDownloadNameOverrideWorks()
    {
        $content = $this->getCompleteContent();

        $routeReference = new RouteReference(
            ContentDownloadRouteReferenceListener::ROUTE_NAME,
            [
                ContentDownloadRouteReferenceListener::OPT_CONTENT => $content,
                ContentDownloadRouteReferenceListener::OPT_FIELD_IDENTIFIER => 'file',
                ContentDownloadRouteReferenceListener::OPT_DOWNLOAD_NAME => 'My-custom-filename.pdf',
            ]
        );
        $event = new RouteReferenceGenerationEvent($routeReference, new Request());
        $eventListener = $this->getListener();

        $eventListener->onRouteReferenceGeneration($event);

        self::assertEquals('My-custom-filename.pdf', $routeReference->get(ContentDownloadRouteReferenceListener::OPT_DOWNLOAD_NAME));
    }

    /**
     * @return \Ibexa\Core\Repository\Values\Content\Content
     */
    protected function getCompleteContent()
    {
        return new Content(
            [
                'internalFields' => [
                        new Field(
                            [
                                'fieldDefIdentifier' => 'file',
                                'languageCode' => 'eng-GB',
                                'value' => new BinaryFileValue(['fileName' => 'Test-file.pdf']),
                            ]
                        ),
                    ],
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(['id' => 42, 'mainLanguageCode' => 'eng-GB']),
                    ]
                ),
            ]
        );
    }

    protected function getListener()
    {
        return new ContentDownloadRouteReferenceListener($this->translationHelperMock);
    }
}

class_alias(ContentDownloadRouteReferenceListenerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\ContentDownloadRouteReferenceListenerTest');
