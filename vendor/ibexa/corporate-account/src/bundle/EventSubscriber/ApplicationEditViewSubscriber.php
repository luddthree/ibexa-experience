<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\ApplicationEditView;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ApplicationEditViewSubscriber extends AbstractViewSubscriber
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($siteAccessService);

        $this->urlGenerator = $urlGenerator;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ApplicationEditView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\ApplicationEditView $view
     */
    protected function configureView(View $view): void
    {
        $application = $view->getApplication();

        $applicationContentInfo = $application->getContent()->getVersionInfo()->getContentInfo();
        $location = $applicationContentInfo->getMainLocation();
        if ($location === null) {
            throw new RuntimeException('Missing Location for edited application');
        }
        $language = $applicationContentInfo->getMainLanguage();
        $parentLocation = $location->getParentLocation();
        $contentType = $application->getContent()->getContentType();

        $view->addParameters([
            'form' => $view->getApplicationForm()->createView(),
            'language' => $language,
            'location' => $location,
            'parent_location' => $parentLocation,
            'content' => $application->getContent(),
            'is_published' => true,
            'content_type' => $contentType,
            'close_href' => $this->urlGenerator->generate('ibexa.corporate_account.application.details', [
                'applicationId' => $application->getId(),
            ]),
        ]);
    }
}
