<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Personalization\Event\ConfigurePersonalizationMenuEvent;
use Ibexa\Personalization\Service\Model\ModelServiceInterface;
use Ibexa\Personalization\Value\Model\Model;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

final class ModelEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const ITEM__TRIGGER_MODEL_BUILD = 'model_edit__sidebar_right__trigger_model_build';
    public const ITEM__SAVE = 'model_edit__sidebar_right__save';
    public const ITEM__SAVE_AND_CLOSE = 'model_edit__sidebar_right__save_and_close';
    public const ITEM__CLOSE = 'model_edit__sidebar_right__close';

    private ModelServiceInterface $modelService;

    private RequestStack $requestStack;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        MenuItemFactory $factory,
        ModelServiceInterface $modelService,
        RequestStack $requestStack
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->modelService = $modelService;
        $this->requestStack = $requestStack;
    }

    protected function getConfigureEventName(): string
    {
        return ConfigurePersonalizationMenuEvent::MODEL_EDIT_SIDEBAR_RIGHT;
    }

    /**
     * @param array<mixed> $options
     */
    public function createStructure(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $saveAndCloseItem = $this->createMenuItem(
            self::ITEM__SAVE_AND_CLOSE,
            [
                'attributes' => [
                    'type' => 'button',
                    'class' => 'ibexa-btn--save-close',
                ],
                'extras' => [
                    'orderNumber' => 20,
                ],
            ]
        );

        $saveAndCloseItem->addChild(
            self::ITEM__SAVE,
            [
                'attributes' => [
                    'type' => 'button',
                    'class' => 'ibexa-btn--save',
                ],
                'extras' => [
                    'orderNumber' => 10,
                ],
            ]
        );

        $menu->addChild($saveAndCloseItem);

        $model = $this->getModel();
        if (null !== $model && $model->triggerModelBuildSupported()) {
            $menu->addChild($this->createMenuItem(
                self::ITEM__TRIGGER_MODEL_BUILD,
                [
                    'attributes' => [
                        'type' => 'button',
                        'class' => 'ibexa-btn--trigger-model-build',
                    ],
                    'extras' => [
                        'orderNumber' => 30,
                    ],
                ]
            ));
        }

        $menu->addChild($this->createMenuItem(
            self::ITEM__CLOSE,
            [
                'route' => 'ibexa.personalization.models',
                'routeParameters' => [
                    'customerId' => $this->getCustomerId($this->getRequestAttributes()),
                ],
                'attributes' => [
                    'type' => 'button',
                    'class' => 'ibexa-btn--close',
                ],
                'extras' => [
                    'orderNumber' => 40,
                ],
            ]
        ));

        return $menu;
    }

    /**
     * @return array<\JMS\TranslationBundle\Model\Message>
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SAVE, 'ibexa_menu'))->setDesc('Save'),
            (new Message(self::ITEM__SAVE_AND_CLOSE, 'ibexa_menu'))->setDesc('Save and close'),
            (new Message(self::ITEM__TRIGGER_MODEL_BUILD, 'ibexa_menu'))->setDesc('Trigger model build'),
            (new Message(self::ITEM__CLOSE, 'ibexa_menu'))->setDesc('Discard changes'),
        ];
    }

    private function getModel(): ?Model
    {
        $requestAttributes = $this->getRequestAttributes();

        $customerId = $this->getCustomerId($requestAttributes);
        $referenceCode = $this->getReferenceCode($requestAttributes);

        if (null === $customerId || null === $referenceCode) {
            return null;
        }

        return $this->modelService->getModel($customerId, $referenceCode);
    }

    private function getRequestAttributes(): ParameterBag
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return new ParameterBag();
        }

        return $request->attributes;
    }

    private function getCustomerId(ParameterBag $requestAttributes): ?int
    {
        if ($requestAttributes->has('customerId')) {
            return (int)$requestAttributes->get('customerId');
        }

        return null;
    }

    private function getReferenceCode(ParameterBag $requestAttributes): ?string
    {
        if ($requestAttributes->has('referenceCode')) {
            return $requestAttributes->get('referenceCode');
        }

        return null;
    }
}
