<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Factory\Form\PersonalizationFormFactoryInterface;
use Ibexa\Personalization\Form\Data\MultiCustomerAccountsData;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Import\ImportServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ImportController extends AbstractPersonalizationController
{
    private ImportServiceInterface $importService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private PersonalizationFormFactoryInterface $formFactory;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        CustomerTypeCheckerInterface $customerTypeChecker,
        EventDispatcherInterface $eventDispatcher,
        SettingServiceInterface $settingService,
        ImportServiceInterface $importService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        PersonalizationFormFactoryInterface $formFactory
    ) {
        parent::__construct($permissionChecker, $customerTypeChecker, $eventDispatcher, $settingService);

        $this->importService = $importService;
        $this->notificationHandler = $notificationHandler;
        $this->formFactory = $formFactory;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function importAction(int $customerId): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        try {
            return $this->render('@ibexadesign/personalization/import/list.html.twig', [
                'imported_items' => $this->importService->getImportedItems($customerId),
                'customer_switcher' => $this->formFactory->createMultiCustomerAccountsForm(
                    new MultiCustomerAccountsData($customerId)
                )->createView(),
                'customer_id' => $customerId,
            ]);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->render('@ibexadesign/personalization/import/list.html.twig', [
                    'imported_items' => [],
                    'customer_id' => $customerId,
                ]);
            }

            throw $exception;
        }
    }
}

class_alias(ImportController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\ImportController');
