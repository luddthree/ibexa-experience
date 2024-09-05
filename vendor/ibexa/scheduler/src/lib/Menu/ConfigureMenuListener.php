<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Menu;

use Ibexa\AdminUi\Menu\ContentCreateRightSidebarBuilder;
use Ibexa\AdminUi\Menu\ContentEditRightSidebarBuilder;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigureMenuListener implements TranslationContainerInterface
{
    public const MENU_DATE_BASED_PUBLISHER = 'menu.date_based_publisher';
    public const MENU_DATE_BASED_PUBLISHER_DISCARD = 'menu.date_based_publisher.discard';

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $dateBasedPublisherService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var array<string> */
    private array $publishButtonsNames;

    /**
     * @param array<string> $publishButtonsNames
     */
    public function __construct(
        DateBasedPublishServiceInterface $dateBasedPublisherService,
        PermissionResolver $permissionResolver,
        TranslatorInterface $translator,
        LocationService $locationService,
        array $publishButtonsNames = [
            ContentCreateRightSidebarBuilder::ITEM__PUBLISH,
            ContentEditRightSidebarBuilder::ITEM__PUBLISH,
        ]
    ) {
        $this->dateBasedPublisherService = $dateBasedPublisherService;
        $this->permissionResolver = $permissionResolver;
        $this->translator = $translator;
        $this->locationService = $locationService;
        $this->publishButtonsNames = $publishButtonsNames;
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     *
     * @throws \InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onAdminUiMenuConfigure(ConfigureMenuEvent $event)
    {
        $root = $event->getMenu();
        $options = $event->getOptions();

        $publishAttributes = [
            'class' => 'ibexa-btn--extra-actions',
            'data-actions' => 'publish-later',
            'data-focus-element' => '.dbp-publish-later__pick-input',
        ];

        if (!$this->canContentBePublished($options)) {
            $publishAttributes['disabled'] = 'disabled';
        }

        $scheduleLaterOptions = [
            'attributes' => $publishAttributes,
            'extras' => [
                'translation_domain' => 'ibexa_scheduler',
                'template' => '@IbexaScheduler/publish_later_widget.html.twig',
                'orderNumber' => 20,
            ],
        ];

        $this->appendScheduleLaterToMenu($root, $scheduleLaterOptions);

        if ($this->shouldAddDiscardItem($options)) {
            $root->addChild(
                self::MENU_DATE_BASED_PUBLISHER_DISCARD,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_discard_schedule_publish',
                    ],
                    'extras' => [
                        'translation_domain' => 'ibexa_scheduler',
                        'orderNumber' => 30,
                    ],
                ]
            );
        }
    }

    private function appendScheduleLaterToMenu(ItemInterface $root, array $options)
    {
        $publishItems = array_filter(array_map(
            static fn ($buttonName): ?ItemInterface => $root->getChild($buttonName),
            $this->publishButtonsNames
        ));

        if (count($publishItems) > 0) {
            $publishItem = current($publishItems);

            $publishItem->addChild(
                self::MENU_DATE_BASED_PUBLISHER,
                $options
            );
        } else {
            $root->addChild(
                self::MENU_DATE_BASED_PUBLISHER,
                $options
            );
        }
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onPageBuilderMenuConfigure(ConfigureMenuEvent $event)
    {
        $root = $event->getMenu();
        $options = $event->getOptions();

        if ($this->canContentBePublished($options)) {
            $scheduleLaterOptions = [
                'attributes' => [
                    'class' => 'ibexa-btn--extra-actions',
                    'data-actions' => 'publish-later',
                ],
                'extras' => [
                    'translation_domain' => 'ibexa_scheduler',
                    'template' => '@IbexaScheduler/publish_later_widget.html.twig',
                    'orderNumber' => 15,
                ],
            ];

            $this->appendScheduleLaterToMenu($root, $scheduleLaterOptions);
        }

        if ($this->shouldAddDiscardItem($options)) {
            $root->addChild(
                self::MENU_DATE_BASED_PUBLISHER_DISCARD,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_discard_schedule_publish',
                    ],
                    'extras' => [
                        'translation_domain' => 'ibexa_scheduler',
                        'orderNumber' => 30,
                        'adaptiveItemsForceHide' => true,
                    ],
                ]
            );
        }
    }

    /**
     * @param array $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return bool
     */
    private function canContentBePublished(array $options): bool
    {
        $targets = [];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'] ?? $options['content_create_struct'];

        $locationCreateStruct = \array_key_exists('content_create_struct', $options) ? $options['content_create_struct']->getLocationStructs()[0] : [];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location */
        $location = $options['location'] ?? null;

        if (!empty($locationCreateStruct)) {
            $targets[] = $locationCreateStruct;
        } elseif ($location !== null) {
            $targets[] = $location;
        }

        if (!empty($options['language']) && $options['language'] instanceof Language) {
            $targets[] = (new Target\Builder\VersionBuilder())
                ->changeStatusTo(VersionInfo::STATUS_PUBLISHED)
                ->updateFieldsTo($options['language']->languageCode, [])
                ->build();
        }

        if ($content instanceof ContentCreateStruct && !empty($locationCreateStruct)) {
            try {
                /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $parentLocation */
                $parentLocation = $this->locationService->loadLocation($locationCreateStruct->parentLocationId);
                $content->sectionId = $parentLocation->contentInfo->sectionId;
            } catch (NotFoundException | UnauthorizedException $e) {
                // If any of those exceptions occurs, simply do not set sectionId
            }
        }

        $canPublish = $this->permissionResolver->canUser(
            'content',
            'publish',
            $content,
            $targets
        );

        if (!$canPublish) {
            return $canPublish;
        }

        $isPublished = $content->contentInfo->published ?? false;
        if ($isPublished) {
            return $this->permissionResolver->canUser(
                'content',
                'edit',
                $content,
                $targets
            );
        }

        $isDraft = isset($content->contentInfo) && $content->contentInfo->isDraft();
        if (!$isDraft) {
            return $this->permissionResolver->canUser(
                'content',
                'create',
                $content,
                $targets
            );
        }

        return true;
    }

    /**
     * @param array $options
     *
     * @return bool
     */
    private function shouldAddDiscardItem(array $options): bool
    {
        if (isset($options['content'])) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            $content = $options['content'];

            $scheduledVersions = $this->dateBasedPublisherService->getVersionsEntriesForContent($content->id);

            if (\count($scheduledVersions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of messages.
     *
     * @return array<Message>
     */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::MENU_DATE_BASED_PUBLISHER, 'ibexa_scheduler'))->setDesc('Publish later'),
            (new Message(self::MENU_DATE_BASED_PUBLISHER_DISCARD, 'ibexa_scheduler'))->setDesc(
                'Discard publish later'
            ),
        ];
    }
}

class_alias(ConfigureMenuListener::class, 'EzSystems\DateBasedPublisher\Core\Menu\ConfigureMenuListener');
