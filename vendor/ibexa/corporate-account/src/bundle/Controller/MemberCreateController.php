<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\View\MemberCreateView;
use Symfony\Component\HttpFoundation\Request;

class MemberCreateController extends Controller
{
    private MemberFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private ConfigResolverInterface $configResolver;

    private LanguageService $languageService;

    private UserService $userService;

    public function __construct(
        MemberFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        ConfigResolverInterface $configResolver,
        LanguageService $languageService,
        UserService $userService
    ) {
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->configResolver = $configResolver;
        $this->languageService = $languageService;
        $this->userService = $userService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function createAction(Request $request, Company $company)
    {
        $languages = $this->configResolver->getParameter('languages');
        $language = $this->languageService->loadLanguage(reset($languages));

        $membersId = $company->getContent()->getFieldValue('members')->destinationContentId;
        $membersGroup = $this->userService->loadUserGroup($membersId);

        /** @var \Symfony\Component\Form\Form $createForm */
        $createForm = $this->formFactory->getCreateForm(
            $membersGroup,
            $language->languageCode
        );

        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid() && null !== $createForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $createForm,
                $createForm->getData(),
                $createForm->getClickedButton()->getName(),
                ['company' => $company]
            );

            $response = $this->actionDispatcher->getResponse();
            if ($response !== null) {
                return $response;
            }

            return $this->redirectToRoute('ibexa.corporate_account.company.details', [
                'companyId' => $company->getId(),
                '_fragment' => 'ibexa-tab-members',
            ]);
        }

        return new MemberCreateView(
            '@ibexadesign/corporate_account/member/create/create_member.html.twig',
            $createForm,
            $membersGroup,
            $language,
            $company
        );
    }
}
