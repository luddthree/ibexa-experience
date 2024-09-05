<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone;
use Ibexa\Core\Helper\ContentPreviewHelper;
use Ibexa\FieldTypePage\Exception\InvalidBlockAttributeException;
use Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Ibexa\FieldTypePage\FieldType\Page\Service\BlockService;
use Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry;
use Ibexa\PageBuilder\Event\BlockPreviewEvents;
use Ibexa\PageBuilder\Event\BlockPreviewPageContextEvent;
use Ibexa\PageBuilder\Event\BlockPreviewResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class PreviewController extends Controller
{
    /** @var \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry */
    private $layoutDefinitionRegistry;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService */
    private $blockService;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Type */
    private $pageFieldType;

    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface */
    private $httpKernel;

    /** @var \Ibexa\Core\Helper\ContentPreviewHelper */
    private $previewHelper;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $defaultBaseTemplate;

    /**
     * @param \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry $layoutDefinitionRegistry
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     * @param \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService $blockService
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Type $pageFieldType
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $httpKernel
     * @param \Ibexa\Core\Helper\ContentPreviewHelper $previewHelper
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param string $defaultBaseTemplate
     */
    public function __construct(
        LayoutDefinitionRegistry $layoutDefinitionRegistry,
        ConfigResolverInterface $configResolver,
        BlockService $blockService,
        Type $pageFieldType,
        HttpKernelInterface $httpKernel,
        ContentPreviewHelper $previewHelper,
        EventDispatcherInterface $eventDispatcher,
        string $defaultBaseTemplate
    ) {
        $this->layoutDefinitionRegistry = $layoutDefinitionRegistry;
        $this->configResolver = $configResolver;
        $this->blockService = $blockService;
        $this->pageFieldType = $pageFieldType;
        $this->httpKernel = $httpKernel;
        $this->previewHelper = $previewHelper;
        $this->eventDispatcher = $eventDispatcher;
        $this->defaultBaseTemplate = $defaultBaseTemplate;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function blockPreviewAction(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('The request is not an AJAX request');
        }

        $blockIds = $request->request->get('blockIds', []);
        $pageHash = $request->request->get('page');
        $parameters = $request->request->get('parameters');
        $pagePreviewParameters = $parameters['pagePreview'] ?? [];
        $blockContext = $this->blockService->createBlockContextFromRequest($request);

        try {
            $value = $this->pageFieldType->acceptValue($pageHash);
        } catch (InvalidBlockAttributeException $e) {
            return new JsonResponse(
                [
                    'error' => 'Invalid Block Attribute',
                    'message' => $e->getMessage(),
                ],
                400
            );
        }

        $page = $value->getPage();
        $this->dispatchPageContextEvent($pagePreviewParameters, $blockContext, $page);

        $responseData = ['blocks' => []];
        foreach ($blockIds as $blockId) {
            $blockValue = $page->getBlockById($blockId);
            $event = $this->dispatchBlockPreviewResponseEvent(
                $blockContext,
                $pagePreviewParameters,
                $page,
                $blockValue
            );
            $responseData['blocks'][$blockValue->getId()] = $event->getResponseData();
        }

        return new JsonResponse($responseData);
    }

    /**
     * @param array $pagePreviewParameters
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    private function dispatchPageContextEvent(
        array $pagePreviewParameters,
        BlockContextInterface $blockContext,
        Page $page
    ): void {
        $pageContextEvent = new BlockPreviewPageContextEvent($blockContext, $page, $pagePreviewParameters);
        $this->eventDispatcher->dispatch($pageContextEvent, BlockPreviewEvents::PAGE_CONTEXT);
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     * @param array $pagePreviewParameters
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @return \Ibexa\PageBuilder\Event\BlockPreviewResponseEvent
     */
    private function dispatchBlockPreviewResponseEvent(
        BlockContextInterface $blockContext,
        array $pagePreviewParameters,
        Page $page,
        BlockValue $blockValue
    ): BlockPreviewResponseEvent {
        $event = new BlockPreviewResponseEvent($blockContext, $pagePreviewParameters, $page, $blockValue);
        $this->eventDispatcher->dispatch($event, BlockPreviewEvents::getBlockPreviewResponseEventName($blockValue->getType()));
        $this->eventDispatcher->dispatch($event, BlockPreviewEvents::RESPONSE);

        return $event;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $siteaccessName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function siteaccessBlockPreviewAction(Request $request, string $siteaccessName): Response
    {
        $forwardRequestParameters = [
            '_controller' => 'Ibexa\Bundle\PageBuilder\Controller\PreviewController::blockPreviewAction',
        ];

        return $this->handleProxyAction($request, $siteaccessName, $forwardRequestParameters);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $siteaccessName
     * @param array $parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    private function handleProxyAction(
        Request $request,
        string $siteaccessName,
        array $parameters
    ): Response {
        $this->previewHelper->setPreviewActive(true);

        $siteAccess = $originalSiteAccess = $this->previewHelper->getOriginalSiteAccess();

        // only switch if $siteaccessName is set and different from original
        if ($siteaccessName !== null && $siteaccessName !== $siteAccess->name) {
            $siteAccess = $this->previewHelper->changeConfigScope($siteaccessName);
        }

        $forwardRequestParameters = [
            'siteaccess' => $siteAccess,
        ];

        // keep original SiteAccess name on Master Request in case of error handling
        $request->attributes->set('originalSiteAccess', $originalSiteAccess->name);
        $subRequest = $request->duplicate(
            null,
            null,
            array_merge($forwardRequestParameters, $parameters)
        );

        $response = $this->httpKernel->handle(
            $subRequest,
            HttpKernelInterface::SUB_REQUEST,
            false
        );

        $response->headers->remove('cache-control');
        $response->headers->remove('expires');

        $this->previewHelper->restoreConfigScope();
        $this->previewHelper->setPreviewActive(false);

        return $response;
    }

    /**
     * @param string $siteaccessName
     * @param string|null $layoutId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Ibexa\Core\MVC\Exception\ParameterNotFoundException
     */
    public function layoutAction(
        string $siteaccessName,
        ?string $layoutId = null
    ): Response {
        $layoutDefinition = $this->layoutDefinitionRegistry->getLayoutDefinitionById($layoutId);
        $emptyPage = $this->createEmptyPage($layoutDefinition);

        return $this->render('@IbexaPageBuilder/page_builder/preview/layout.html.twig', [
            'base_template' => $this->getBaseTemplate($siteaccessName),
            'siteaccess' => $siteaccessName,
            'layout_definition' => $layoutDefinition,
            'page' => $emptyPage,
        ]);
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition $layoutDefinition
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    private function createEmptyPage(LayoutDefinition $layoutDefinition): Page
    {
        $zones = [];
        foreach ($layoutDefinition->getZones() as $zoneId => $zoneDefinition) {
            $zones[] = new Zone($zoneId, $zoneDefinition['name']);
        }

        return new Page($layoutDefinition->getId(), $zones);
    }

    /**
     * Return base template from config.
     *
     * @param string $siteaccessName
     *
     * @return string
     *
     * @throws \Ibexa\Core\MVC\Exception\ParameterNotFoundException
     */
    private function getBaseTemplate(string $siteaccessName): string
    {
        if ($this->configResolver->hasParameter('page_layout', null, $siteaccessName)) {
            return $this->configResolver->getParameter('page_layout', null, $siteaccessName);
        }

        if ($this->configResolver->hasParameter('pagelayout', null, $siteaccessName)) {
            return $this->configResolver->getParameter('pagelayout', null, $siteaccessName);
        }

        return $this->defaultBaseTemplate;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $siteaccessName
     * @param string|null $layoutId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function siteaccessLayoutPreviewAction(
        Request $request,
        string $siteaccessName,
        ?string $layoutId = null
    ): Response {
        $forwardRequestParameters = [
            '_controller' => 'Ibexa\Bundle\PageBuilder\Controller\PreviewController::layoutAction',
            'layoutId' => $layoutId,
            'siteaccessName' => $siteaccessName,
        ];

        return $this->handleProxyAction($request, $siteaccessName, $forwardRequestParameters);
    }
}

class_alias(PreviewController::class, 'EzSystems\EzPlatformPageBuilderBundle\Controller\PreviewController');
