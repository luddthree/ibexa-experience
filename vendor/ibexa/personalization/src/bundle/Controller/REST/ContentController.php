<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller\REST;

use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Personalization\Authentication\AuthenticationInterface;
use Ibexa\Personalization\Exception\ItemNotFoundException;
use Ibexa\Personalization\Service\Storage\DataSourceServiceInterface;
use Ibexa\Personalization\Value\ContentData;
use Ibexa\Personalization\Value\Item\Action;
use Ibexa\Personalization\Value\Storage\ItemList;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Exceptions\AuthenticationFailedException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ContentController extends RestController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private AuthenticationInterface $authentication;

    private DataSourceServiceInterface $dataSourceService;

    public function __construct(
        AuthenticationInterface $authentication,
        DataSourceServiceInterface $dataSourceService,
        ?LoggerInterface $logger = null
    ) {
        $this->authentication = $authentication;
        $this->dataSourceService = $dataSourceService;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws \Ibexa\Rest\Server\Exceptions\AuthenticationFailedException
     */
    public function getContentByIdAction(int $contentId, Request $request): ContentData
    {
        if (!$this->authentication->authenticate($request, Action::ACTION_UPDATE)) {
            throw new AuthenticationFailedException(
                'Invalid or expired token',
                Response::HTTP_UNAUTHORIZED
            );
        }

        $requestQuery = $request->query;
        $item = $this->dataSourceService->getItem((string)$contentId, (string)$requestQuery->get('lang'));

        return $this->getContentData(new ItemList([$item]));
    }

    /**
     * @throws \Ibexa\Rest\Server\Exceptions\AuthenticationFailedException
     */
    public function getContentByRemoteIdAction(string $remoteId, Request $request): ContentData
    {
        if (!$this->authentication->authenticate($request, Action::ACTION_UPDATE)) {
            throw new AuthenticationFailedException(
                'Invalid or expired token',
                Response::HTTP_UNAUTHORIZED
            );
        }
        $requestQuery = $request->query;
        $item = $this->dataSourceService->getItem($remoteId, (string)$requestQuery->get('lang'));

        return $this->getContentData(new ItemList([$item]));
    }

    /**
     * @throws \Ibexa\Rest\Server\Exceptions\AuthenticationFailedException
     */
    public function getContentListAction(string $contentIds, Request $request): ContentData
    {
        if (!$this->authentication->authenticate($request, Action::ACTION_UPDATE)) {
            throw new AuthenticationFailedException(
                'Invalid or expired token',
                Response::HTTP_UNAUTHORIZED
            );
        }

        $requestQuery = $request->query;
        $splitContentIds = explode(',', $contentIds);

        $items = [];
        foreach ($splitContentIds as $contentId) {
            try {
                $items[] = $this->dataSourceService->getItem((string)$contentId, (string)$requestQuery->get('lang'));
            } catch (ItemNotFoundException $exception) {
                $this->logger->warning($exception->getMessage());
            }
        }

        return $this->getContentData(new ItemList($items));
    }

    private function getContentData(ItemListInterface $itemList): ContentData
    {
        return new ContentData($itemList);
    }
}
