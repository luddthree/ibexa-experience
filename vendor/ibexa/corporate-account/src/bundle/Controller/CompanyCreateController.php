<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ContentForms\Content\Form\Provider\GroupedContentFormFieldsProviderInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\View\CompanyCreateView;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;

class CompanyCreateController extends Controller
{
    private CompanyFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private ConfigResolverInterface $configResolver;

    private LocationService $locationService;

    private LanguageService $languageService;

    private GroupedContentFormFieldsProviderInterface $groupedContentFormFieldsProvider;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        CompanyFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        TranslatableNotificationHandlerInterface $notificationHandler,
        ConfigResolverInterface $configResolver,
        LocationService $locationService,
        LanguageService $languageService,
        GroupedContentFormFieldsProviderInterface $groupedContentFormFieldsProvider
    ) {
        parent::__construct($corporateAccount);

        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->notificationHandler = $notificationHandler;
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->groupedContentFormFieldsProvider = $groupedContentFormFieldsProvider;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function createAction(Request $request)
    {
        $languages = $this->configResolver->getParameter('languages');
        $corporateLocationRemoteId = $this->corporateAccount->getParentLocationRemoteId();

        $language = $this->languageService->loadLanguage(reset($languages));
        $parentLocation = $this->locationService->loadLocationByRemoteId($corporateLocationRemoteId);

        /** @var \Symfony\Component\Form\Form $createForm */
        $createForm = $this->formFactory->getCreateForm(
            $parentLocation,
            $language->languageCode
        );

        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid() && null !== $createForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $createForm,
                $createForm->getData(),
                $createForm->getClickedButton()->getName()
            );

            $this->notificationHandler->success(
                /** @Desc("Company created.") */
                'company.create.success',
                [],
                'ibexa_corporate_account'
            );

            return $this->redirectToRoute('ibexa.corporate_account.company.list');
        }

        $groupedFields = $this->groupedContentFormFieldsProvider->getGroupedFields(
            $createForm->get('fieldsData')->all()
        );

        return new CompanyCreateView(
            '@ibexadesign/corporate_account/company/create/create_company.html.twig',
            $createForm,
            $parentLocation,
            $language,
            $groupedFields
        );
    }
}
