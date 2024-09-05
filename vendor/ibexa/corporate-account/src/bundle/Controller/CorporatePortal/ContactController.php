<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Configuration\CorporateAccount;
use Ibexa\CorporateAccount\View\CorporatePortal\ContactView;
use Symfony\Component\HttpFoundation\Request;

final class ContactController extends Controller
{
    private MemberResolver $memberResolver;

    public function __construct(
        CorporateAccount $corporateAccount,
        MemberResolver $memberResolver
    ) {
        parent::__construct($corporateAccount);
        $this->memberResolver = $memberResolver;
    }

    public function showAction(Request $request): BaseView
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();

        return new ContactView(
            '@ibexadesign/customer_portal/contact/contact.html.twig',
            $company
        );
    }
}
