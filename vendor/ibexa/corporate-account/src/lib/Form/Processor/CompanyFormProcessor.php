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
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\CorporateAccount\Event\DispatcherEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CompanyFormProcessor extends FormProcessor
{
    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DispatcherEvents::COMPANY_PUBLISH => ['processPublish', 10],
        ];
    }

    public function processPublish(FormActionEvent $event): void
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct $data */
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data instanceof ContentUpdateData && !$data instanceof ContentCreateData) {
            throw new InvalidArgumentException(
                '$data',
                'Expected ContentUpdateData or ContentCreateData'
            );
        }

        $languageCode = $form->getConfig()->getOption('languageCode');

        if ($data->isNew()) {
            /** @var \Ibexa\ContentForms\Data\Content\ContentCreateData $data */
            $company = $this->createCompany($data, $languageCode);
        } else {
            /** @var \Ibexa\ContentForms\Data\Content\ContentUpdateData $data */
            $company = $this->updateCompany($data, $languageCode);
        }

        if ($data->isNew()) {
            $this->companyService->createCompanyMembersUserGroup($company);
            $this->companyService->createCompanyAddressBookFolder($company);

            $company = $this->companyService->getCompany($company->getId());

            $this->companyService->setDefaultShippingAddress(
                $company,
                $this->shippingAddressService->createShippingAddressFromCompanyBillingAddress($company)
            );
        }

        $redirectUrl = ($form['redirectUrlAfterPublish'] && $form['redirectUrlAfterPublish']->getData())
            ? $form['redirectUrlAfterPublish']->getData()
            : $this->urlGenerator->generate(
                'ibexa.corporate_account.company.details',
                [
                    'companyId' => $company->getId(),
                ]
            );

        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    public function createCompany(
        ContentCreateData $contentCreateData,
        string $languageCode
    ): Company {
        $companyCreateStruct = $this->companyService->newCompanyCreateStruct();
        $companyCreateStruct->contentType = $contentCreateData->contentType;
        $companyCreateStruct->mainLanguageCode = $contentCreateData->mainLanguageCode;
        $companyCreateStruct->alwaysAvailable = $contentCreateData->alwaysAvailable;

        $mainLanguageCode = $this->resolveMainLanguageCode($contentCreateData);

        foreach ($contentCreateData->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $companyCreateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        return $this->companyService->createCompany($companyCreateStruct);
    }

    public function updateCompany(
        ContentUpdateData $contentUpdateData,
        string $languageCode
    ): Company {
        $companyUpdateStruct = $this->companyService->newCompanyUpdateStruct();
        $companyUpdateStruct->initialLanguageCode = $contentUpdateData->initialLanguageCode;
        $companyUpdateStruct->creatorId = $contentUpdateData->creatorId;

        $mainLanguageCode = $this->resolveMainLanguageCode($contentUpdateData);

        foreach ($contentUpdateData->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $companyUpdateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        $company = $this->companyService->getCompany($contentUpdateData->contentDraft->id);

        return $this->companyService->updateCompany($company, $companyUpdateStruct);
    }
}
