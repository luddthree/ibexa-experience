<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Generator\Company\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Migrations\Generator\Company\CreateMetadata;
use Ibexa\CorporateAccount\Migrations\Generator\Company\Reference\ReferenceGenerator;
use Ibexa\CorporateAccount\Migrations\Generator\Company\Step\CompanyCreateStep;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

final class Create implements StepBuilderInterface
{
    private FieldTypeServiceInterface $fieldTypeService;

    private ReferenceGenerator $referenceGenerator;

    public function __construct(
        FieldTypeServiceInterface $fieldTypeService,
        ReferenceGenerator $referenceGenerator
    ) {
        $this->fieldTypeService = $fieldTypeService;
        $this->referenceGenerator = $referenceGenerator;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\CorporateAccount\Values\Company $company
     */
    public function build(ValueObject $company): StepInterface
    {
        Assert::isInstanceOf($company, Company::class);

        $fields = [];
        foreach ($company->getContent()->getFields() as $key => $field) {
            $contentType = $company->getContent()->getContentType();
            $fieldDefinition = $contentType->getFieldDefinition($field->getFieldDefinitionIdentifier());

            if ($fieldDefinition !== null && $fieldDefinition->fieldGroup === 'internal') {
                continue;
            }

            $hash = $this->fieldTypeService->getHashFromFieldValue($field->value, $field->fieldTypeIdentifier);
            $fields[$key] = Field::createFromValueObject($field, $hash);
        }

        $references = $this->referenceGenerator->generate($company);

        return new CompanyCreateStep(
            CreateMetadata::createFromApi($company),
            $fields,
            $references
        );
    }
}
