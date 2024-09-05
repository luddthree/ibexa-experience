<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\ContentForms\Content\Form\Provider\GroupedContentFormFieldsProviderInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Exception\ApplicationRateLimitExceededException;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ApplicationFormFactory;
use Ibexa\CorporateAccount\View\CorporateAccountApplicationAlreadyExistsView;
use Ibexa\CorporateAccount\View\CorporateAccountApplicationCreateView;
use Ibexa\CorporateAccount\View\CorporateAccountApplicationSuccessView;
use Ibexa\CorporateAccount\View\CorporateAccountApplicationWaitView;
use Symfony\Component\HttpFoundation\Request;

final class ApplicationController extends Controller
{
    private ApplicationFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private ConfigResolverInterface $configResolver;

    private LanguageService $languageService;

    private ContentTypeService $contentTypeService;

    private GroupedContentFormFieldsProviderInterface $groupedContentFormFieldsProvider;

    private LocationService $locationService;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        ApplicationFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        ConfigResolverInterface $configResolver,
        LanguageService $languageService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        GroupedContentFormFieldsProviderInterface $groupedContentFormFieldsProvider
    ) {
        parent::__construct($corporateAccount);

        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->configResolver = $configResolver;
        $this->languageService = $languageService;
        $this->contentTypeService = $contentTypeService;
        $this->groupedContentFormFieldsProvider = $groupedContentFormFieldsProvider;
        $this->locationService = $locationService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function createAction(Request $request)
    {
        $languages = $this->configResolver->getParameter('languages');
        $language = $this->languageService->loadLanguage(reset($languages));

        $parentLocation = $this->locationService->loadLocationByRemoteId(
            $this->corporateAccount->getApplicationParentLocationRemoteId()
        );

        /** @var \Symfony\Component\Form\Form $createForm */
        $createForm = $this->formFactory->getCreateForm(
            $parentLocation,
            $language->languageCode
        );

        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid() && null !== $createForm->getClickedButton()) {
            try {
                $this->actionDispatcher->dispatchFormAction(
                    $createForm,
                    $createForm->getData(),
                    $createForm->getClickedButton()->getName()
                );
            } catch (ApplicationRateLimitExceededException $exception) {
                return $this->redirectToRoute('ibexa.corporate_account.customer_portal.corporate_account.register.wait');
            }

            if ($this->actionDispatcher->getResponse() !== null) {
                return $this->actionDispatcher->getResponse();
            }

            return $this->redirectToRoute('ibexa.corporate_account.customer_portal.corporate_account.register.confirmation');
        }

        $groupedFields = $this->groupedContentFormFieldsProvider->getGroupedFields(
            $createForm->get('fieldsData')->all()
        );

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccount->getContentTypeIdentifier('application')
        );

        return new CorporateAccountApplicationCreateView(
            '@ibexadesign/customer_portal/registration/registration.html.twig',
            $createForm,
            $parentLocation,
            $language,
            $contentType,
            $groupedFields,
            false
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function confirmationAction(Request $request)
    {
        return new CorporateAccountApplicationSuccessView(
            '@ibexadesign/customer_portal/registration/registration_confirmation.html.twig'
        );
    }

    /**
     * @deprecated since version 4.6, to be removed in 5.0.
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function alreadyExistsAction(Request $request)
    {
        return new CorporateAccountApplicationAlreadyExistsView(
            '@ibexadesign/customer_portal/registration/registration_already_exists.html.twig'
        );
    }

    public function waitAction(Request $request): CorporateAccountApplicationWaitView
    {
        return new CorporateAccountApplicationWaitView(
            '@ibexadesign/customer_portal/registration/registration_wait.html.twig'
        );
    }
}
