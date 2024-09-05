<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\StepExecutor\Company;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Migrations\Generator\Company\Step\CompanyCreateStep;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Webmozart\Assert\Assert;

final class CreateCompanyStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private CompanyService $companyService;

    private ShippingAddressService $shippingAddressService;

    private ContentTypeService $contentTypeService;

    private FieldTypeServiceInterface $fieldTypeService;

    private CorporateAccountConfiguration $corporateAccountConfig;

    public function __construct(
        CompanyService $companyService,
        ShippingAddressService $shippingAddressService,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        CorporateAccountConfiguration $corporateAccountConfig
    ) {
        $this->companyService = $companyService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
        $this->corporateAccountConfig = $corporateAccountConfig;
        $this->shippingAddressService = $shippingAddressService;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof CompanyCreateStep;
    }

    /**
     * @param \Ibexa\CorporateAccount\Migrations\Generator\Company\Step\CompanyCreateStep $step
     */
    protected function doHandle(StepInterface $step)
    {
        $companyCreateStruct = $this->companyService->newCompanyCreateStruct();
        $companyContentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccountConfig->getCompanyContentTypeIdentifier()
        );

        $companyCreateStruct->remoteId = $step->metadata->remoteId;
        $companyCreateStruct->ownerId = $step->metadata->creatorId;
        $companyCreateStruct->modificationDate = $step->metadata->modificationDate;

        foreach ($step->fields as $field) {
            $fieldDefinition = $companyContentType->getFieldDefinition($field->fieldDefIdentifier);
            Assert::notNull($fieldDefinition, sprintf(
                'Field definition with identifier: "%s" does not exist in content type "%s".',
                $field->fieldDefIdentifier,
                $companyContentType->identifier,
            ));

            $value = $this->fieldTypeService->getFieldValueFromHash(
                $field->value,
                $fieldDefinition->fieldTypeIdentifier,
                $fieldDefinition->getFieldSettings()
            );

            $companyCreateStruct->setField($field->fieldDefIdentifier, $value, $field->languageCode);
        }

        $company = $this->companyService->createCompany($companyCreateStruct);

        $this->getLogger()->notice(sprintf(
            'Added company "%s" (ID: %s)',
            $company->getName(),
            $company->getId(),
        ));

        $this->companyService->createCompanyMembersUserGroup($company);
        $this->companyService->createCompanyAddressBookFolder($company);

        $company = $this->companyService->getCompany($company->getId());

        $this->companyService->setDefaultShippingAddress(
            $company,
            $this->shippingAddressService->createShippingAddressFromCompanyBillingAddress($company)
        );

        return $company;
    }
}
