<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\ProductFieldGroupsViewSubscriber;
use Ibexa\Bundle\ProductCatalog\View\ProductCreateView;
use Ibexa\Bundle\ProductCatalog\View\ProductUpdateView;
use Ibexa\Contracts\ContentForms\Content\Form\Provider\GroupedContentFormFieldsProviderInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;

final class ProductFieldGroupsViewSubscriberTest extends AbstractLocalViewSubscriberTest
{
    private const TEMPLATE_IDENTIFIER = 'example.html.twig';
    private const GROUPED_FIELDS_PARAMETER = 'grouped_fields';

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function testOnPreContentCreateView(): void
    {
        $view = new ProductCreateView(
            self::TEMPLATE_IDENTIFIER,
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(Language::class),
            $this->getFormMock(),
        );

        $subscriber = $this->getSubscriber();
        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue(
            $view->hasParameter(self::GROUPED_FIELDS_PARAMETER)
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function testOnPreContentUpdateView(): void
    {
        $view = new ProductUpdateView(
            self::TEMPLATE_IDENTIFIER,
            $this->createMock(ProductInterface::class),
            $this->createMock(Language::class),
            $this->getFormMock(),
        );

        $subscriber = $this->getSubscriber();
        $subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue(
            $view->hasParameter(self::GROUPED_FIELDS_PARAMETER)
        );
    }

    private function getSubscriber(): ProductFieldGroupsViewSubscriber
    {
        return new ProductFieldGroupsViewSubscriber(
            $this->createSiteAccessServiceMock(true),
            $this->createConfigProvider(true),
            $this->createMock(GroupedContentFormFieldsProviderInterface::class)
        );
    }

    /**
     * @return \Symfony\Component\Form\FormInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getFormMock(): FormInterface
    {
        $formChildMock = $this->createMock(FormInterface::class);
        $formMock = $this->createMock(FormInterface::class);

        $fields = [
            $this->createMock(Button::class),
            $this->createMock(SubmitButton::class),
        ];

        $formChildMock->method('all')->willReturn($fields);
        $formMock->method('get')->willReturn($formChildMock);

        return $formMock;
    }
}
