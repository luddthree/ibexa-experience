<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Processor;

use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\ApplicationEmail;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\CorporateAccount\Event\DispatcherEvents;
use Ibexa\CorporateAccount\Values\Mapper\DomainMapperInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApplicationFormProcessor extends FormProcessor
{
    private ApplicationService $applicationService;

    private Repository $repository;

    public function __construct(
        ContentService $contentService,
        UserService $userService,
        RoleService $roleService,
        UrlGeneratorInterface $urlGenerator,
        DomainMapperInterface $domainMapper,
        CompanyService $companyService,
        MemberService $memberService,
        ShippingAddressService $shippingAddressService,
        Repository $repository,
        ApplicationService $applicationService
    ) {
        parent::__construct(
            $contentService,
            $userService,
            $roleService,
            $urlGenerator,
            $domainMapper,
            $companyService,
            $memberService,
            $shippingAddressService
        );

        $this->applicationService = $applicationService;
        $this->repository = $repository;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DispatcherEvents::APPLICATION_EDIT_PUBLISH => ['processCreate', 10],
        ];
    }

    public function processCreate(FormActionEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data instanceof ContentCreateData && !$data instanceof ContentUpdateData) {
            throw new InvalidArgumentException(
                '$data',
                'Expected ContentCreateData or ContentUpdateData'
            );
        }

        $languageCode = $form->getConfig()->getOption('languageCode');

        if ($data->isNew() && $data instanceof ContentCreateData) {
            $email = $data->fieldsData['user']->value->email;
            $applicationsCount = $this->repository->sudo(function () use ($email): int {
                return $this->applicationService->getApplicationsCount(
                    new ApplicationEmail(Operator::EQ, $email)
                );
            });

            if ($applicationsCount > 0) {
                $event->setResponse(new RedirectResponse(
                    $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.corporate_account.register.confirmation')
                ));

                return;
            }
            $application = $this->createApplication($data, $languageCode);
        } elseif ($data instanceof ContentUpdateData) {
            $application = $this->updateApplication($data, $languageCode);
        }

        $event->setPayload('application', $application ?? null);
    }

    protected function createApplication(
        ContentCreateData $contentCreateData,
        string $languageCode
    ): Application {
        $applicationCreateStruct = $this->applicationService->newApplicationCreateStruct();
        $applicationCreateStruct->contentType = $contentCreateData->contentType;
        $applicationCreateStruct->mainLanguageCode = $contentCreateData->mainLanguageCode;
        $applicationCreateStruct->alwaysAvailable = $contentCreateData->alwaysAvailable;

        $mainLanguageCode = $this->resolveMainLanguageCode($contentCreateData);

        foreach ($contentCreateData->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $applicationCreateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        return $this->applicationService->createApplication($applicationCreateStruct);
    }

    protected function updateApplication(
        ContentUpdateData $contentUpdateData,
        string $languageCode
    ): Application {
        $applicationUpdateStruct = $this->applicationService->newApplicationUpdateStruct();
        $applicationUpdateStruct->initialLanguageCode = $contentUpdateData->initialLanguageCode;
        $applicationUpdateStruct->creatorId = $contentUpdateData->creatorId;

        $mainLanguageCode = $this->resolveMainLanguageCode($contentUpdateData);

        foreach ($contentUpdateData->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $applicationUpdateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        $application = $this->applicationService->getApplication($contentUpdateData->contentDraft->id);

        return $this->applicationService->updateApplication($application, $applicationUpdateStruct);
    }
}
