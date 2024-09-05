<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ConnectorQualifio\Controller;

use Ibexa\Bundle\Core\Controller;
use Ibexa\ConnectorQualifio\Service\QualifioService;
use Symfony\Component\HttpFoundation\Response;

final class QualifioController extends Controller
{
    private QualifioService $qualifioService;

    public function __construct(
        QualifioService $qualifioService
    ) {
        $this->qualifioService = $qualifioService;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function embedCampaign(
        array $params,
        string $template = '@ibexadesign/embed/qualifio/default.html.twig'
    ): Response {
        $campaignId = (int)$params['campaign'];

        if ($campaignUrl = $this->qualifioService->buildCampaignUrl($campaignId)) {
            $params['url'] = $campaignUrl;
        }

        return $this->render(
            $template,
            $params,
        );
    }

    public function promoteQualifio(): Response
    {
        return $this->render(
            '@ibexadesign/qualifio/promote.html.twig',
            [
                'is_activated' => $this->qualifioService->isConfigured(),
            ],
        );
    }
}
