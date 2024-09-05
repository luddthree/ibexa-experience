<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Search\SearchServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchController extends AbstractPersonalizationAjaxController
{
    private const PHRASE_PARAMETER = 'phrase';

    private SearchServiceInterface $searchService;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        SettingServiceInterface $settingService,
        SearchServiceInterface $searchService
    ) {
        parent::__construct($permissionChecker, $settingService);

        $this->searchService = $searchService;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     */
    public function searchAttributesAction(Request $request, int $customerId): Response
    {
        $errors = $this->performAccessCheck($request, $customerId);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_FORBIDDEN);
        }

        try {
            $query = $request->query;
            $searchHits = [];

            if ($query->has(self::PHRASE_PARAMETER)) {
                $searchHits = $this->searchService->searchAttributes(
                    $customerId,
                    (string) $query->get(self::PHRASE_PARAMETER)
                );
            }

            return $this->json(['searchHits' => $searchHits]);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                return $this->json([], Response::HTTP_SERVICE_UNAVAILABLE);
            }

            throw $exception;
        }
    }
}
