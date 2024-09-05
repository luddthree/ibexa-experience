<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Configuration\CorporateAccount;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\View\CorporatePortal\MyProfileView;
use Ibexa\CorporateAccount\View\MemberEditView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class MyProfileController extends Controller
{
    private MemberResolver $memberResolver;

    private MemberFormFactory $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    private ActionDispatcherInterface $actionDispatcher;

    public function __construct(
        CorporateAccount $corporateAccount,
        MemberResolver $memberResolver,
        MemberFormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator,
        ActionDispatcherInterface $actionDispatcher
    ) {
        parent::__construct($corporateAccount);
        $this->memberResolver = $memberResolver;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->actionDispatcher = $actionDispatcher;
    }

    public function showAction(Request $request): BaseView
    {
        return new MyProfileView(
            '@ibexadesign/customer_portal/profile/my_profile.html.twig',
            $this->memberResolver->getCurrentMember()
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editAction(Request $request)
    {
        $member = $this->memberResolver->getCurrentMember();
        $form = $this->formFactory->getEditForm($member);

        $form->handleRequest($request);

        if ($form->isSubmitted()
            && $form->isValid()
            && null !== $form->getClickedButton()
        ) {
            $this->actionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['company' => $member->getCompany()]
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.my_profile')
            );
        }

        return new MemberEditView(
            '@ibexadesign/customer_portal/profile/my_profile_edit.html.twig',
            $member,
            $member->getCompany(),
            $form,
        );
    }
}
