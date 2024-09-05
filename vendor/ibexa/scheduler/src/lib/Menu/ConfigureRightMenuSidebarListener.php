<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Menu;

use Ibexa\AdminUi\Menu\ContentRightSidebarBuilder;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface;
use Ibexa\Scheduler\Form\Data\DateBasedHideData;
use Ibexa\Scheduler\Form\Type\DateBasedHideType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ConfigureRightMenuSidebarListener implements TranslationContainerInterface
{
    public const ITEM__HIDE = 'content__sidebar_right__schedule_hide';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface */
    private $dateBasedHideService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    private PermissionResolver $permissionResolver;

    public function __construct(
        TranslatorInterface $translator,
        FormFactoryInterface $formFactory,
        DateBasedHideServiceInterface $dateBasedHideService,
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        $this->translator = $translator;
        $this->formFactory = $formFactory;
        $this->dateBasedHideService = $dateBasedHideService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onAdminUiMenuConfigure(ConfigureMenuEvent $event): void
    {
        $options = $event->getOptions();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];

        if ($this->userService->isUser($content) || $this->userService->isUserGroup($content)) {
            return;
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $options['location'];

        if ($this->dateBasedHideService->isScheduledHide($content->id)) {
            $scheduledEntry = $this->dateBasedHideService->getScheduledHide($content->id);
            $timestamp = $scheduledEntry->date->getTimestamp();
        }

        $formView = $this->createFormView($location, $content, $timestamp ?? null);

        $translations = $content->getVersionInfo()->languageCodes;
        $target = (new Target\Version())->deleteTranslations($translations);
        $canHide = $this->permissionResolver->canUser(
            'content',
            'hide',
            $content,
            [$target]
        );

        $root = $event->getMenu();

        if (!$content->contentInfo->isHidden) {
            $root->addChild(
                self::ITEM__HIDE,
                [
                    'extras' => [
                        'orderNumber' => 60,
                        'translation_domain' => 'ibexa_menu',
                        'template' => '@IbexaScheduler/schedule_hide_widget.html.twig',
                        'template_parameters' => [
                            'form_date_based_hide' => $formView,
                        ],
                    ],
                    'attributes' => [
                        'class' => 'ibexa-btn--schedule-hide ibexa-btn--extra-actions',
                        'data-actions' => 'schedule-hide',
                        'data-focus-element' => '.form-check-input',
                        'disabled' => $content->contentInfo->isHidden || !$canHide,
                    ],
                ]
            );
        }

        $root->removeChild(ContentRightSidebarBuilder::ITEM__HIDE);
        $this->reorderChildren($root);
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__HIDE, 'ibexa_menu'))->setDesc('Hide'),
        ];
    }

    private function reorderChildren(ItemInterface $menuRootItem): void
    {
        $children = $menuRootItem->getChildren();

        uasort($children, static function (MenuItem $a, MenuItem $b) {
            return $a->getExtra('orderNumber') <=> $b->getExtra('orderNumber');
        });

        $menuRootItem->reorderChildren(array_keys($children));
    }

    private function createFormView(
        Location $location,
        Content $content,
        ?int $timestamp
    ): FormView {
        $fqcnToBlockPrefix = StringUtil::fqcnToBlockPrefix(DateBasedHideType::class);

        $dateBasedHideData = new DateBasedHideData(
            $location,
            $content->versionInfo,
            $timestamp
        );

        return $this->formFactory->createNamed(
            $fqcnToBlockPrefix,
            DateBasedHideType::class,
            $dateBasedHideData
        )->createView();
    }
}

class_alias(ConfigureRightMenuSidebarListener::class, 'EzSystems\DateBasedPublisher\Core\Menu\ConfigureRightMenuSidebarListener');
