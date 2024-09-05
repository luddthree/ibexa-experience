<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Language;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Symfony\Component\HttpFoundation\RequestStack;

final class PreviewLanguageResolver implements PreviewLanguageResolverInterface
{
    private const LANGUAGE_QUERY_PARAM = 'language';

    private RequestStack $requestStack;

    private LanguageService $languageService;

    public function __construct(
        RequestStack $requestStack,
        LanguageService $languageService
    ) {
        $this->requestStack = $requestStack;
        $this->languageService = $languageService;
    }

    public function resolve(): ?Language
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if ($currentRequest === null) {
            return null;
        }

        $languageCode = $currentRequest->get(self::LANGUAGE_QUERY_PARAM);
        try {
            return $languageCode === null ? null : $this->languageService->loadLanguage($languageCode);
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
