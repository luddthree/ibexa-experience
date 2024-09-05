<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Company;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\CorporateAccount\Form\DataTransformer\CompanyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class CompanyType extends AbstractType
{
    private CompanyService $companyService;

    public function __construct(
        CompanyService $companyService
    ) {
        $this->companyService = $companyService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer(new CompanyTransformer($this->companyService));
    }

    public function getParent(): ?string
    {
        return HiddenType::class;
    }
}
