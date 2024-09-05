<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\FieldTypePage\Exception\InvalidBlockAttributeException;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\PageBuilder\PageBuilder\Timeline\Collector;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentCreateContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentEditContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentTranslateContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentViewContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextFactory;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageContextInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageCreateContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageEditContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageTranslateContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageViewContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TimelineController extends Controller
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Type */
    private $pageFieldType;

    /** @var \JMS\Serializer\SerializerInterface */
    private $serializer;

    /** @var \Ibexa\PageBuilder\PageBuilder\Timeline\Collector */
    private $timelineEventsCollector;

    /** @var \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextFactory */
    private $contextFactory;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Type $pageFieldType
     * @param \JMS\Serializer\SerializerInterface $serializer
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Collector $timelineEventsCollector
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextFactory $contextFactory
     */
    public function __construct(
        Type $pageFieldType,
        SerializerInterface $serializer,
        Collector $timelineEventsCollector,
        ContextFactory $contextFactory
    ) {
        $this->pageFieldType = $pageFieldType;
        $this->serializer = $serializer;
        $this->timelineEventsCollector = $timelineEventsCollector;
        $this->contextFactory = $contextFactory;
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
    public function getEventsAction(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('The request is not an AJAX request');
        }

        $pageHash = $request->request->get('page');
        $intent = $request->request->get('intent');
        $parameters = $request->request->get('parameters');
        $intentParameters = $parameters['intentParameters'];

        $context = $this->contextFactory->build($intent, $intentParameters);

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
        $context = $this->wrapContext($context, $page);

        $timelineEvents = $this->timelineEventsCollector->collect($context);

        $serializedTimelineEvents = $this->serializer->serialize(['events' => $timelineEvents], 'json');

        return new JsonResponse($serializedTimelineEvents, Response::HTTP_OK, [], true);
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface $context
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     *
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageContextInterface
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function wrapContext(ContextInterface $context, Page $page): PageContextInterface
    {
        if ($context instanceof ContentEditContext) {
            return new PageEditContext(
                $context->getLocation(),
                $context->getContent(),
                $context->getVersionInfo(),
                $context->getLanguageCode(),
                $page
            );
        }

        if ($context instanceof ContentViewContext) {
            return new PageViewContext(
                $context->getLocation(),
                $context->getContent(),
                $context->getVersionInfo(),
                $context->getLanguageCode(),
                $page
            );
        }

        if ($context instanceof ContentCreateContext) {
            return new PageCreateContext(
                $context->getContentType(),
                $context->getParentLocation(),
                $context->getLanguageCode(),
                $page
            );
        }

        if ($context instanceof ContentTranslateContext) {
            return new PageTranslateContext(
                $context->getLocation(),
                $context->getContent(),
                $context->getLanguageCode(),
                $context->getBaseLanguageCode(),
                $page
            );
        }

        throw new InvalidArgumentException('$context', 'Cannot wrap the passed context object.');
    }
}

class_alias(TimelineController::class, 'EzSystems\EzPlatformPageBuilderBundle\Controller\TimelineController');
