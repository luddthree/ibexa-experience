<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\CorporatePortal;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;

class ContactView extends BaseView
{
    private Company $company;

    public function __construct(
        string $templateIdentifier,
        Company $company
    ) {
        parent::__construct($templateIdentifier);

        $this->company = $company;
    }

    /**
     * @return array{
     *     company: \Ibexa\Contracts\CorporateAccount\Values\Company
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'company' => $this->company,
        ];
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
