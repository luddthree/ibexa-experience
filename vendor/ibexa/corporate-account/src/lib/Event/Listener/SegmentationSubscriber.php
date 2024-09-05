<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event\Listener;

use Ibexa\Contracts\CorporateAccount\Event\Company\CreateCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\DeleteCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\CreateMemberEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\DeleteMemberEvent;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SegmentationSubscriber implements EventSubscriberInterface
{
    private const CORPORATE_ACCOUNTS_GROUP = 'corporate_accounts';

    private SegmentationServiceInterface $segmentationService;

    public function __construct(
        SegmentationServiceInterface $segmentationService
    ) {
        $this->segmentationService = $segmentationService;
    }

    /**
     * @return array<string, string|array<string, int>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CreateCompanyEvent::class => 'onCreateCompany',
            DeleteCompanyEvent::class => 'onDeleteCompany',
            CreateMemberEvent::class => 'onCreateMember',
            DeleteMemberEvent::class => 'onDeleteMember',
        ];
    }

    public function onCreateCompany(CreateCompanyEvent $event): void
    {
        $segmentGroup = $this->segmentationService->loadSegmentGroupByIdentifier(
            self::CORPORATE_ACCOUNTS_GROUP
        );

        $company = $event->getCompany();

        $this->segmentationService->createSegment(
            new SegmentCreateStruct([
                'identifier' => $this->getSegmentIdentifier($company),
                'name' => $company->getName(),
                'group' => $segmentGroup,
            ])
        );
    }

    public function onDeleteCompany(DeleteCompanyEvent $event): void
    {
        $company = $event->getCompany();

        $this->segmentationService->removeSegment(
            $this->segmentationService->loadSegmentByIdentifier(
                $this->getSegmentIdentifier($company)
            )
        );
    }

    public function onCreateMember(CreateMemberEvent $event): void
    {
        $member = $event->getMember();
        $company = $event->getCompany();

        $segment = $this->segmentationService->loadSegmentByIdentifier(
            $this->getSegmentIdentifier($company)
        );

        $this->segmentationService->assignUserToSegment(
            $member->getUser(),
            $segment,
        );
    }

    public function onDeleteMember(DeleteMemberEvent $event): void
    {
        $member = $event->getMember();
        $company = $member->getCompany();

        $segment = $this->segmentationService->loadSegmentByIdentifier(
            $this->getSegmentIdentifier($company)
        );

        $this->segmentationService->unassignUserFromSegment(
            $member->getUser(),
            $segment
        );
    }

    private function getSegmentIdentifier(Company $company): string
    {
        return sprintf('corporate_account__company_%d', $company->getId());
    }
}
