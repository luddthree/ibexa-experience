<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeCreateType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeDefinitionChoiceType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Create;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\ProductType\ContentTypeFactoryInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateController extends Controller
{
    private ContentTypeService $contentTypeService;

    private ContentTypeFactoryInterface $contentTypeFactory;

    private ConfigResolverInterface $configResolver;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private Repository $repository;

    private ConfigProviderInterface $configProvider;

    private SubmitHandler $submitHandler;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentTypeFactoryInterface $contentTypeFactory,
        ConfigResolverInterface $configResolver,
        TranslatableNotificationHandlerInterface $notificationHandler,
        Repository $repository,
        ConfigProviderInterface $configProvider,
        SubmitHandler $submitHandler
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeFactory = $contentTypeFactory;
        $this->configResolver = $configResolver;
        $this->notificationHandler = $notificationHandler;
        $this->repository = $repository;
        $this->configProvider = $configProvider;
        $this->submitHandler = $submitHandler;
    }

    public function renderAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Create());

        $form = $this->createProductTypeCreateForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductTypeCreateData $data): Response {
                    $isVirtual = $data->getType() === ProductTypeDefinitionChoiceType::VIRTUAL;
                    $languageCode = $this->getMainLanguageCode();

                    return $this->getResponse(
                        $this->createContentTypeCreateStruct($languageCode, $isVirtual),
                        $languageCode
                    );
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.product_type.list');
    }

    private function createProductTypeCreateForm(): FormInterface
    {
        return $this->createForm(
            ProductTypeCreateType::class,
            null,
            [
                'method' => Request::METHOD_POST,
                'action' => $this->generateUrl('ibexa.product_catalog.product_type.create'),
            ]
        );
    }

    private function getMainLanguageCode(): string
    {
        $languages = $this->configResolver->getParameter('languages');

        return reset($languages);
    }

    private function createContentTypeCreateStruct(
        string $mainLanguageCode,
        bool $isVirtual
    ): ContentTypeCreateStruct {
        return $this->contentTypeFactory->createContentTypeCreateStruct(
            '__new__' . md5((string)microtime(true)),
            $mainLanguageCode,
            [],
            $isVirtual
        );
    }

    private function getResponse(ContentTypeCreateStruct $createStruct, string $languageCode): Response
    {
        $group = $this->contentTypeService->loadContentTypeGroupByIdentifier(
            $this->configProvider->getEngineOption('product_type_group_identifier')
        );

        try {
            /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft */
            $contentTypeDraft = $this->repository->sudo(
                fn () => $this->contentTypeService->createContentType($createStruct, [$group])
            );
        } catch (NotFoundException $e) {
            $this->notificationHandler->error(
                /** @Desc("Cannot create Product Type. Could not find language with identifier '%language_code%'") */
                'product_type.add.missing_language',
                ['%language_code%' => $languageCode],
                'ibexa_product_catalog'
            );

            return $this->redirectToRoute('ibexa.product_catalog.product_type.list');
        }

        return $this->redirectToRoute(
            'ibexa.content_type.update',
            [
                'contentTypeId' => $contentTypeDraft->id,
                'contentTypeGroupId' => $group->id,
                'toLanguageCode' => $languageCode,
                'fromLanguageCode' => $languageCode,
            ]
        );
    }
}
