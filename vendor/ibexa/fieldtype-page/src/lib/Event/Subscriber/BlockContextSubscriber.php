<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\Bundle\FieldTypePage\Controller\BlockController;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\FieldTypePage\Event\BlockContextEvent;
use Ibexa\FieldTypePage\Event\BlockContextEvents;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\ContentViewBlockContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class BlockContextSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface */
    private $controllerResolver;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Type */
    private $type;

    /**
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $controllerResolver
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Type $type
     */
    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        ControllerResolverInterface $controllerResolver,
        Type $type
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->controllerResolver = $controllerResolver;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockContextEvents::CREATE => ['onBlockContextCreate', 0],
        ];
    }

    /**
     * Creates ContentViewBlockContext from Request.
     *
     * @param \Ibexa\FieldTypePage\Event\BlockContextEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function onBlockContextCreate(BlockContextEvent $event): void
    {
        $request = $event->getRequest();

        $controller = $this->controllerResolver->getController($request);

        if (!\is_array($controller) || !$controller[0] instanceof BlockController || $controller[1] !== 'renderAction') {
            return;
        }

        $location = $request->get('location');
        $locationId = (int)$request->get('locationId');
        if (null === $location && !empty($locationId)) {
            try {
                $location = $this->locationService->loadLocation($locationId);
            } catch (UnauthorizedException $e) {
                return;
            } catch (NotFoundException $e) {
                //Content is not yet published (i.e. send for review), location do not exist.
            }
        }

        $contentId = (int) (null !== $location ? $location->contentId : $request->get('contentId'));

        if (!$contentId) {
            return;
        }

        $contentInfo = null !== $location ? $location->getContentInfo() : $this->contentService->loadContent($contentId)->contentInfo;

        $versionNo = (int) $request->get('versionNo', $contentInfo->currentVersionNo);
        $languageCode = (string) $request->get('languageCode', $contentInfo->mainLanguageCode);
        $content = $this->contentService->loadContent($contentId, [$languageCode], $versionNo);
        $pageField = $this->getPageField($content);
        $page = $pageField->value->getPage();

        $event->setBlockContext(new ContentViewBlockContext($location, $content, $versionNo, $languageCode, $page));
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    private function getPageField(Content $content): Field
    {
        foreach ($content->getFields() as $field) {
            if ($field->fieldTypeIdentifier === $this->type->getFieldTypeIdentifier()) {
                return $field;
            }
        }

        throw new InvalidArgumentException('$content', 'The Content item does not contain a Page Field.');
    }
}

class_alias(BlockContextSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\BlockContextSubscriber');
