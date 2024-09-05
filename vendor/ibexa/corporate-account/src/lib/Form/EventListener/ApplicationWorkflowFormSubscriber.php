<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\EventListener;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Contracts\CorporateAccount\Event\Application\MapCompanyCreateStructEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\MapShippingAddressUpdateStructEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\Workflow\ApplicationWorkflowFormEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\Workflow\MapApplicationWorkflowFormEvent;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Event\ApplicationWorkflowEvents;
use Ibexa\CorporateAccount\Form\ApplicationWorkflowFormFactory;
use Ibexa\CorporateAccount\Permission\Limitation\Target\ApplicationStateTarget;
use Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\HandlerInterface;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateUpdateStruct;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ApplicationWorkflowFormSubscriber implements EventSubscriberInterface
{
    private const UNKNOWN_REASON = 'Unknown reason';

    private const STATE_ON_HOLD = 'on_hold';

    private const STATE_ACCEPT = 'accept';

    private const STATE_REJECT = 'reject';

    private CorporateAccountConfiguration $configuration;

    private ApplicationWorkflowFormFactory $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private EventDispatcherInterface $eventDispatcher;

    private CompanyService $companyService;

    private CustomerGroupServiceInterface $customerGroupService;

    private ShippingAddressService $shippingAddressService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private RoleService $roleService;

    private UserService $userService;

    private InvitationService $invitationService;

    /** @var array<string, string[]> */
    private array $siteAccessGroups;

    private ApplicationService $applicationService;

    private HandlerInterface $applicationStateHandler;

    private PermissionResolver $permissionResolver;

    /**
     * @param array<string, string[]> $siteAccessGroups
     */
    public function __construct(
        CorporateAccountConfiguration $configuration,
        ApplicationWorkflowFormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator,
        EventDispatcherInterface $eventDispatcher,
        CompanyService $companyService,
        ShippingAddressService $shippingAddressService,
        CustomerGroupServiceInterface $customerGroupService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        RoleService $roleService,
        UserService $userService,
        ApplicationService $applicationService,
        InvitationService $invitationService,
        HandlerInterface $applicationStateHandler,
        PermissionResolver $permissionResolver,
        array $siteAccessGroups
    ) {
        $this->configuration = $configuration;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->companyService = $companyService;
        $this->customerGroupService = $customerGroupService;
        $this->shippingAddressService = $shippingAddressService;
        $this->notificationHandler = $notificationHandler;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->roleService = $roleService;
        $this->userService = $userService;
        $this->invitationService = $invitationService;
        $this->applicationService = $applicationService;
        $this->applicationStateHandler = $applicationStateHandler;
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MapApplicationWorkflowFormEvent::class => 'mapApplicationWorkflowForm',
            ApplicationWorkflowEvents::APPLICATION_WORKFLOW => 'onApplicationWorkflow',
            ApplicationWorkflowEvents::getStateEvent(self::STATE_ON_HOLD) => 'applicationOnHold',
            ApplicationWorkflowEvents::getStateEvent(self::STATE_ACCEPT) => 'applicationAccept',
            ApplicationWorkflowEvents::getStateEvent(self::STATE_REJECT) => 'applicationReject',
        ];
    }

    public function mapApplicationWorkflowForm(MapApplicationWorkflowFormEvent $event): void
    {
        $form = $this->formFactory->getForm(
            $event->getState(),
            $event->getData()
        );

        $event->setForm($form);
    }

    public function onApplicationWorkflow(ApplicationWorkflowFormEvent $event): void
    {
        $applicationStateTarget = new ApplicationStateTarget(
            $event->getPreviousState(),
            $event->getNextState()
        );

        if (!$this->permissionResolver->canUser(
            'company_application',
            'workflow',
            $event->getApplication(),
            [$applicationStateTarget]
        )) {
            throw new UnauthorizedException(
                'company_application',
                'workflow',
                [
                    'from' => $applicationStateTarget->getFrom(),
                    'to' => $applicationStateTarget->getTo(),
                ]
            );
        }
    }

    public function applicationOnHold(ApplicationWorkflowFormEvent $event): void
    {
        $data = $event->getData();

        if (!is_array($data)) {
            return;
        }

        $application = $event->getApplication();
        $this->setApplicationSalesRep($application, (int)$data['sales_rep']);

        $this->setApplicationState($event->getApplicationState(), self::STATE_ON_HOLD);

        $reasons = array_flip($this->configuration->getApplicationStageReasons(self::STATE_ON_HOLD));
        $reason = $reasons[$data['reason']] ?? self::UNKNOWN_REASON;

        $this->notificationHandler->success(
            /** @Desc("Application put on hold because: %reason%") */
            'application.state.on_hold.notification',
            ['%reason%' => $reason],
            'ibexa_corporate_account_applications'
        );
    }

    public function applicationReject(ApplicationWorkflowFormEvent $event): void
    {
        $data = $event->getData();

        if (!is_array($data)) {
            return;
        }

        $this->setApplicationState($event->getApplicationState(), self::STATE_REJECT);

        $reasons = array_flip($this->configuration->getApplicationStageReasons(self::STATE_REJECT));
        $reason = $reasons[$data['reason']] ?? self::UNKNOWN_REASON;

        $this->notificationHandler->success(
            /** @Desc("Application rejected because: %reason%") */
            'application.state.reject.notification',
            ['%reason%' => $reason],
            'ibexa_corporate_account_applications'
        );

        $event->setResponse(
            new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.application.list')
            )
        );
    }

    public function applicationAccept(ApplicationWorkflowFormEvent $event): void
    {
        $data = $event->getData();

        if (!is_array($data)) {
            return;
        }

        /** @var \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value $customerGroupValue */
        $customerGroupValue = $data['customer_group'];
        $customerGroup = $this->customerGroupService->getCustomerGroup((int)$customerGroupValue->getId());
        $salesRepId = (int)$data['sales_rep'];

        $application = $event->getApplication();

        $this->setApplicationSalesRep($application, $salesRepId);
        $this->setApplicationCustomerGroup($application, $customerGroup->getId());

        $companyCreateStruct = $this->companyService->newCompanyCreateStruct();
        $this->eventDispatcher->dispatch(
            new MapCompanyCreateStructEvent(
                $companyCreateStruct,
                $application,
                $customerGroup,
                $salesRepId
            )
        );

        $company = $this->companyService->createCompany($companyCreateStruct);
        $members = $this->companyService->createCompanyMembersUserGroup($company);
        $addressBook = $this->companyService->createCompanyAddressBookFolder($company);

        $this->companyService->setCompanyMembersRelation($company, $members);
        $this->companyService->setCompanyAddressBookRelation($company, $addressBook);

        $company = $this->companyService->getCompany($company->getId());

        $shippingAddress = $this->shippingAddressService->createShippingAddressFromCompanyBillingAddress($company);
        $shippingAddressUpdateStruct = $this->shippingAddressService->newShippingAddressUpdateStruct();

        $this->eventDispatcher->dispatch(
            new MapShippingAddressUpdateStructEvent($shippingAddressUpdateStruct, $application)
        );

        $this->shippingAddressService->updateShippingAddress($shippingAddress, $shippingAddressUpdateStruct);

        $this->companyService->setDefaultShippingAddress($company, $shippingAddress);

        $siteAccessIdentifier = (string)reset($this->siteAccessGroups['corporate_group']);
        $membersGroup = $this->userService->loadUserGroup((int)$company->getMembersId());

        $userField = $application->getContent()->getField('user');
        $mainLocation = $company->getContent()->getVersionInfo()->getContentInfo()->getMainLocation();

        if (!empty($userField) && !empty($mainLocation)) {
            $email = $userField->value->email;
            $role = $this->roleService->loadRoleByIdentifier($this->configuration->getDefaultAdministratorRoleIdentifier());

            $struct = new InvitationCreateStruct(
                $email,
                $siteAccessIdentifier,
                $membersGroup,
                $role,
                new SubtreeLimitation(
                    [
                        'limitationValues' => [
                            $mainLocation->pathString,
                        ],
                    ]
                )
            );

            $this->invitationService->createInvitation($struct);
        }

        $applicationStateUpdateStruct = new ApplicationStateUpdateStruct($event->getApplicationState()->getId());
        $applicationStateUpdateStruct->state = self::STATE_ACCEPT;
        $applicationStateUpdateStruct->companyId = $company->getId();
        $this->applicationStateHandler->update($applicationStateUpdateStruct);

        $this->applicationService->deleteApplication($application);

        $this->notificationHandler->success(
            /** @Desc("Application accepted") */
            'application.state.accept.notification',
            [],
            'ibexa_corporate_account_applications'
        );

        $event->setResponse(
            new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.company.details', [
                    'companyId' => $company->getId(),
                ])
            )
        );
    }

    private function setApplicationSalesRep(Application $application, int $salesRep): void
    {
        $applicationUpdateStruct = $this->applicationService->newApplicationUpdateStruct();
        $applicationUpdateStruct->setField('sales_rep', new RelationValue($salesRep));
        $this->applicationService->updateApplication($application, $applicationUpdateStruct);
    }

    private function setApplicationCustomerGroup(Application $application, int $customerGroup): void
    {
        $applicationUpdateStruct = $this->applicationService->newApplicationUpdateStruct();
        $applicationUpdateStruct->setField('customer_group', new Value($customerGroup));
        $this->applicationService->updateApplication($application, $applicationUpdateStruct);
    }

    private function setApplicationState(ApplicationState $applicationState, string $state): void
    {
        $applicationStateUpdateStruct = new ApplicationStateUpdateStruct($applicationState->getId());
        $applicationStateUpdateStruct->state = $state;

        $this->applicationStateHandler->update($applicationStateUpdateStruct);
    }
}
