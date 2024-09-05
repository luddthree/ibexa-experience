<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Connect\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Test\IbexaKernelTestTrait;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field as FormModelField;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\FormBuilder\FieldType\FormFactory;
use Ibexa\FormBuilder\FieldType\Value;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FormBuilderSubscriberTest extends KernelTestCase
{
    use IbexaKernelTestTrait;

    public function testIntegrationWithFormBuilderSubmission(): void
    {
        $httpClient = self::getServiceByClassName(HttpClientInterface::class, 'ibexa.connect.http_client');
        self::assertInstanceOf(TraceableHttpClient::class, $httpClient);
        self::assertCount(0, $httpClient->getTracedRequests());

        $eventDispatcher = $this->getContainer()->get(EventDispatcherInterface::class);
        self::assertInstanceOf(TraceableEventDispatcher::class, $eventDispatcher);
        self::assertCount(0, $eventDispatcher->getCalledListeners());

        self::assertTrue($eventDispatcher->hasListeners(MVCEvents::PRE_CONTENT_VIEW));

        $formFactory = self::getServiceByClassName(FormFactory::class);
        self::assertInstanceOf(FormFactory::class, $formFactory);

        $fields = [
            new FormModelField(
                'single_line_field_id',
                'single_line',
                'FooValue',
            ),
            new FormModelField(
                'button_field_id',
                'button',
                'Submit',
                [
                    new Attribute('ibexa_connect_webhook_url', 'https://foo.ibexa/foo'),
                ],
            ),
        ];
        $formModel = new Form(
            0,
            null,
            'eng-GB',
            $fields,
        );
        $form = $formFactory->createForm($formModel);
        $formName = $form->getName();

        $requestStack = $this->getContainer()->get('request_stack');
        self::assertInstanceOf(RequestStack::class, $requestStack);
        self::assertNull($requestStack->getMainRequest());
        $request = Request::create(
            '/',
            'POST',
            [
                $formName => [
                    'fields' => [
                        'single_line_field_id' => 'single_line_field_foo_value',
                    ],
                ],
            ]
        );
        $request->attributes->set('siteaccess', new SiteAccess('admin'));
        $requestStack->push($request);

        $content = $this->createMock(Content::class);

        $fields = [
            new Field([
                'fieldTypeIdentifier' => 'ezform',
                'value' => new Value(
                    $formModel,
                    $form,
                ),
            ]),
        ];

        $content
            ->method('__get')
            ->willReturnCallback(static function (string $propertyName) {
                if ($propertyName === 'id') {
                    return 0;
                }

                if ($propertyName === 'contentInfo') {
                    return new ContentInfo();
                }

                return null;
            });

        $content
            ->expects(self::once())
            ->method('getFieldsByLanguage')
            ->willReturn($fields);
        $view = new ContentView();
        $view->setContent($content);

        $eventDispatcher->dispatch(new PreContentViewEvent($view), MVCEvents::PRE_CONTENT_VIEW);

        $requests = $httpClient->getTracedRequests();
        self::assertCount(1, $requests);
        [ $request ] = $requests;
        self::assertSame('POST', $request['method']);
        self::assertSame('https://foo.ibexa/foo', $request['url']);
        self::assertSame([
            'json' => [
                'languageCode' => 'eng-GB',
                'contentId' => 0,
                'contentFieldId' => null,
                'fields' => [
                    [
                        'field_type_identifier' => 'single_line',
                        'field_name' => 'FooValue',
                        'value' => 'single_line_field_foo_value',
                    ], [
                        'field_type_identifier' => 'button',
                        'field_name' => 'Submit',
                        'value' => null,
                    ],
                ],
            ],
        ], $request['options']);
    }
}
