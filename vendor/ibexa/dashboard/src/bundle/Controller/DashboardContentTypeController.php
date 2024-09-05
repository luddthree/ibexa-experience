<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DashboardContentTypeController extends Controller
{
    private ContentTypeService $contentTypeService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        ContentTypeService $contentTypeService,
        ConfigResolverInterface $configResolver
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->configResolver = $configResolver;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function dashboardContentTypeAction(Request $request): Response
    {
        $contentTypeIdentifier = $this->configResolver->getParameter(
            IsDashboardContentType::CONTENT_TYPE_IDENTIFIER_PARAM_NAME
        );
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentTypeGroups = $contentType->getContentTypeGroups();
        $contentTypeGroup = reset($contentTypeGroups);
        if ($contentTypeGroup === false) {
            throw new InvalidArgumentException(
                'contentTypeGroup',
                'ContentTypeGroup is not defined'
            );
        }
        $attributes = array_merge(
            $request->attributes->all(),
            [
                'contentTypeGroupId' => $contentTypeGroup->id,
                'contentTypeId' => $contentType->id,
                'view_template' => '@ibexadesign/dashboard/dashboard_type.html.twig',
            ]
        );

        return $this->forward(
            'Ibexa\Bundle\AdminUi\Controller\ContentTypeController::viewAction',
            $attributes
        );
    }
}
