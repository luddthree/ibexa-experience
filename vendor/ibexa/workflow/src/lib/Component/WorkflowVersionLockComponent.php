<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Component;

use Ibexa\AdminUi\Component\TwigComponent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Twig\Environment;

final class WorkflowVersionLockComponent extends TwigComponent
{
    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        Environment $twig,
        WorkflowServiceInterface $workflowService,
        PermissionResolver $permissionResolver,
        array $parameters = []
    ) {
        parent::__construct(
            $twig,
            '@ibexadesign/ibexa_workflow/admin/component/workflow_version_lock.html.twig',
            $parameters
        );
        $this->workflowService = $workflowService;
        $this->permissionResolver = $permissionResolver;
    }

    public function render(array $parameters = []): string
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $parameters['content'];

        $isVersionLocked = $this->workflowService->isVersionLocked(
            $content->versionInfo,
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );

        if (!$isVersionLocked) {
            return '';
        }

        return parent::render($parameters);
    }
}
