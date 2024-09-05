<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Dashboard;

use Ibexa\Contracts\AdminUi\Tab\AbstractTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Core\Helper\TranslationHelper;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MyScheduledTab extends AbstractTab implements OrderedTabInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    protected $dateBasedPublisherService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    protected $translationHelper;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        DateBasedPublishServiceInterface $dateBasedPublisherService,
        UserService $userService,
        TranslationHelper $translationHelper
    ) {
        parent::__construct($twig, $translator);

        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->dateBasedPublisherService = $dateBasedPublisherService;
        $this->userService = $userService;
        $this->translationHelper = $translationHelper;
    }

    public function getIdentifier(): string
    {
        return 'my-scheduled';
    }

    public function getName(): string
    {
        return /** @Desc("Scheduled") */
            $this->translator->trans('tab.name.my_scheduled', [], 'ibexa_scheduler');
    }

    public function getOrder(): int
    {
        return 150;
    }

    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;

        $scheduled = $this->dateBasedPublisherService->getUserScheduledVersions(
            0,
            $limit
        );

        $pager = new Pagerfanta(
            new ArrayAdapter(
                $scheduled
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        $data = [];
        /** @var \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $scheduled */
        foreach ($pager as $scheduled) {
            $version = $scheduled->versionInfo;
            $contentInfo = $version->getContentInfo();
            try {
                $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
            } catch (NotFoundException $e) {
                // not found content skipped in the 'my scheduled' tab
                continue;
            }
            $creator = $this->userService->loadUser($version->creatorId);

            $data[] = [
                'contentId' => $contentInfo->id,
                'contributor' => $this->translationHelper->getTranslatedContentName($scheduled->user),
                'contributor_content' => $scheduled->user,
                'publicationDate' => $scheduled->date,
                'name' => $version->getName(),
                /* @deprecated since version 2.5, to be removed in 3.0. Use 'content_type.name' instead. */
                'type' => $contentType->getName(),
                'content_type' => $contentType,
                'language' => $version->initialLanguageCode,
                'version' => $version->versionNo,
                'modified' => $version->modificationDate,
                'creationDate' => $version->creationDate,
                'creator' => $creator->getName(),
            ];
        }

        return $this->twig->render('@IbexaScheduler/dashboard/tab/my_scheduled.html.twig', [
            'data' => $data,
        ]);
    }
}

class_alias(MyScheduledTab::class, 'EzSystems\DateBasedPublisher\Core\Dashboard\MyScheduledTab');
