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
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\CorporateAccount\Event\DispatcherEvents;
use Ibexa\CorporateAccount\Form\Data\Company\CompanyUpdateDefaultShippingAddressData;
use Ibexa\CorporateAccount\Form\Data\ShippingAddress\ShippingAddressItemDeleteData;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ShippingAddressFormProcessor extends FormProcessor
{
    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DispatcherEvents::SHIPPING_ADDRESS_PUBLISH => ['processPublish', 10],
            DispatcherEvents::SHIPPING_ADDRESS_SET_AS_DEFAULT => ['updateDefault', 10],
            DispatcherEvents::SHIPPING_ADDRESS_DELETE => ['processDelete', 10],
        ];
    }

    public function updateDefault(FormActionEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof CompanyUpdateDefaultShippingAddressData) {
            return;
        }

        $company = $this->companyService->getCompany($data->getCompany()->id);
        $shippingAddress = $this->shippingAddressService->getShippingAddress($data->getAddress()->id);

        $this->companyService->setDefaultShippingAddress(
            $company,
            $shippingAddress
        );
    }

    public function processDelete(FormActionEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof ShippingAddressItemDeleteData) {
            return;
        }

        $toBeRemovedList = $data->getAddressBookItems();
        if (!is_array($toBeRemovedList)) {
            return;
        }

        /** @var \Ibexa\Contracts\CorporateAccount\Values\Company $company */
        $company = $event->getOption('company');
        $currentDefault = $this->shippingAddressService->getCompanyDefaultShippingAddress($company);

        foreach ($toBeRemovedList as $shippingAddress) {
            $this->shippingAddressService->deleteShippingAddress($shippingAddress);
        }

        $this->setNewDefaultIfOldIsDeleted($company, $toBeRemovedList, $currentDefault);
    }

    /**
     * @param \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[] $toBeRemovedList
     */
    private function setNewDefaultIfOldIsDeleted(
        Company $company,
        array $toBeRemovedList,
        ?ShippingAddress $currentDefault = null
    ): void {
        if ($currentDefault === null) {
            return;
        }

        $addressesId = array_map(static fn (ShippingAddress $shippingAddress) => $shippingAddress->getId(), $toBeRemovedList);
        $defaultIsDeleted = in_array($currentDefault->getId(), $addressesId, true);

        if (!$defaultIsDeleted) {
            return;
        }

        $addressList = $this->shippingAddressService->getCompanyShippingAddresses($company);
        if (empty($addressList)) {
            return;
        }

        $this->companyService->setDefaultShippingAddress(
            $company,
            reset($addressList)
        );
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

        $company = $event->getOption('company');

        if ($data->isNew()) {
            /** @var \Ibexa\ContentForms\Data\Content\ContentCreateData $data */
            $this->createShippingAddress($company, $data, $languageCode);
        } else {
            /** @var \Ibexa\ContentForms\Data\Content\ContentUpdateData $data */
            $this->updateShippingAddress($data, $languageCode);
        }

        $redirectUrl = ($form['redirectUrlAfterPublish'] && $form['redirectUrlAfterPublish']->getData())
            ? $form['redirectUrlAfterPublish']->getData()
            : $this->urlGenerator->generate(
                'ibexa.corporate_account.company.details',
                [
                    'companyId' => $company->getId(),
                    '_fragment' => 'ibexa-tab-address_book',
                ]
            );

        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    public function createShippingAddress(
        Company $company,
        ContentCreateData $contentCreateData,
        string $languageCode
    ): ShippingAddress {
        $companyCreateStruct = $this->shippingAddressService->newShippingAddressCreateStruct();
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

        return $this->shippingAddressService->createShippingAddress($company, $companyCreateStruct);
    }

    public function updateShippingAddress(
        ContentUpdateData $contentUpdateData,
        string $languageCode
    ): ShippingAddress {
        $companyUpdateStruct = $this->shippingAddressService->newShippingAddressUpdateStruct();
        $companyUpdateStruct->initialLanguageCode = $contentUpdateData->initialLanguageCode;
        $companyUpdateStruct->creatorId = $contentUpdateData->creatorId;

        $mainLanguageCode = $this->resolveMainLanguageCode($contentUpdateData);

        foreach ($contentUpdateData->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $companyUpdateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        $shippingAddress = $this->shippingAddressService->getShippingAddress($contentUpdateData->contentDraft->id);

        return $this->shippingAddressService->updateShippingAddress($shippingAddress, $companyUpdateStruct);
    }
}
