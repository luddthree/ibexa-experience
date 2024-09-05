<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Company;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final class CompanyUpdateDefaultShippingAddressData extends ValueObject
{
    /**
     * @Assert\NotBlank()
     */
    public ?ContentInfo $address;

    /**
     * @Assert\NotBlank()
     */
    public ContentInfo $company;

    public function __construct(ContentInfo $company, ?ContentInfo $address = null)
    {
        parent::__construct();
        $this->address = $address;
        $this->company = $company;
    }

    public function getAddress(): ?ContentInfo
    {
        return $this->address;
    }

    public function setAddress(?ContentInfo $address): void
    {
        $this->address = $address;
    }

    public function getCompany(): ContentInfo
    {
        return $this->company;
    }

    public function setCompany(ContentInfo $company): void
    {
        $this->company = $company;
    }
}
