<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AssetCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AssetCreateType;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\AssetsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateAssetController extends Controller
{
    private Repository $repository;

    private LocalAssetServiceInterface $assetService;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        Repository $repository,
        LocalAssetServiceInterface $assetService,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->repository = $repository;
        $this->assetService = $assetService;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    public function executeAction(Request $request, ProductInterface $product): ?Response
    {
        $form = $this->createForm(AssetCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (AssetCreateData $data) use ($product): ?Response {
                    $this->handleFormSubmission($product, $data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToProductView($product, AssetsTab::URI_FRAGMENT);
    }

    private function handleFormSubmission(ProductInterface $product, AssetCreateData $data): void
    {
        $this->repository->beginTransaction();
        try {
            foreach ($data->getUris() as $uri) {
                $createStruct = $this->assetService->newAssetCreateStruct();
                $createStruct->setUri($uri);
                $createStruct->setTags($data->getTags() ?? []);

                $this->assetService->createAsset($product, $createStruct);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $this->notificationHandler->success(
            /** @Desc("Assets created.") */
            'product.asset.create.success',
            [],
            'ibexa_product_catalog'
        );
    }
}
