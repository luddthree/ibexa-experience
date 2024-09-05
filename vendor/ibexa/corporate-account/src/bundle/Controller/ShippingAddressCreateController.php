<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\CorporateAccount\View\ShippingAddressCreateView;
use JMS\TranslationBundle\Annotation\Desc;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class ShippingAddressCreateController extends Controller
{
    private ShippingAddressFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private ConfigResolverInterface $configResolver;

    private ContentService $contentService;

    private LanguageService $languageService;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        ShippingAddressFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        TranslatableNotificationHandlerInterface $notificationHandler,
        ConfigResolverInterface $configResolver,
        ContentService $contentService,
        LanguageService $languageService
    ) {
        parent::__construct($corporateAccount);

        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->notificationHandler = $notificationHandler;
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
        $this->languageService = $languageService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function createAction(Request $request, Company $company)
    {
        $languages = $this->configResolver->getParameter('languages');
        $language = $this->languageService->loadLanguage(reset($languages));

        $addressBookId = $company->getAddressBookId();
        $parentLocation = $this->contentService->loadContentInfo($addressBookId)->getMainLocation();

        if ($parentLocation === null) {
            throw new RuntimeException('Missing parentLocation for shipping address');
        }
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
                $createForm->getClickedButton()->getName(),
                [
                    'company' => $company,
                ]
            );

            $this->notificationHandler->success(
                /** @Desc("Address created.") */
                'shipping_address.create.success',
                [],
                'ibexa_corporate_account'
            );

            return $this->redirectToRoute('ibexa.corporate_account.company.details', [
                'companyId' => $company->getId(),
                '_fragment' => 'ibexa-tab-address_book',
            ]);
        }

        return new ShippingAddressCreateView(
            '@ibexadesign/corporate_account/shipping_address/create/create_shipping_address.html.twig',
            $createForm,
            $parentLocation,
            $language,
            $company
        );
    }
}
