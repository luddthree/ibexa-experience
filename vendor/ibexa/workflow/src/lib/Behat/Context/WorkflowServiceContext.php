<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Behat\Context;

use Behat\Behat\Context\Context;
use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Behat\Core\Behat\ArgumentParser;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Behat\Facade\WorkflowFacade;
use PHPUnit\Framework\Assert;

class WorkflowServiceContext implements Context
{
    /**
     * @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface
     */
    private $workflowService;

    /**
     * @var \Ibexa\Contracts\Core\Repository\UserService
     */
    private $userService;

    /**
     * @var \Ibexa\Workflow\Behat\Facade\WorkflowFacade
     */
    private $workflowFacade;

    /**
     * @var \Ibexa\Behat\Core\Behat\ArgumentParser
     */
    private $argumentParser;

    /**
     * @var \Ibexa\Behat\API\Facade\ContentFacade
     */
    private $contentFacade;

    public function __construct(
        WorkflowServiceInterface $workflowService,
        UserService $userService,
        WorkflowFacade $workflowFacade,
        ArgumentParser $argumentParser,
        ContentFacade $contentFacade
    ) {
        $this->workflowService = $workflowService;
        $this->userService = $userService;
        $this->workflowFacade = $workflowFacade;
        $this->argumentParser = $argumentParser;
        $this->contentFacade = $contentFacade;
    }

    /**
     * @Then Workflow data does not contain Trashed items
     */
    public function workflowDataDoesNotContainTrashedItems(): void
    {
        $workflowsMetadata = $this->workflowService->loadOngoingWorkflowMetadata();

        $this->assertThatContentItemsAreNotInTrash($workflowsMetadata);
    }

    /**
     * @Then Workflow data originated by user :username does not contain Trashed items
     */
    public function workflowDataOriginatedByUserDoesNotContainTrashedItems($username): void
    {
        $user = $this->userService->loadUserByLogin($username);
        $workflowsMetadata = $this->workflowService->loadOngoingWorkflowMetadataOriginatedByUser($user);

        $this->assertThatContentItemsAreNotInTrash($workflowsMetadata);
    }

    private function assertThatContentItemsAreNotInTrash(array $workflowsMetadata): void
    {
        Assert::assertNotEmpty(
            $workflowsMetadata,
            'Error: Workflow does not include any Content.'
        );

        foreach ($workflowsMetadata as $workflowMetadata) {
            $contentInfo = $workflowMetadata->content->getVersionInfo()->getContentInfo();

            Assert::assertFalse(
                $contentInfo->isTrashed(),
                sprintf('Error: Content %s from Trash returned.', $contentInfo->name)
            );
        }
    }

    /**
     * @Given  I transition :locationURL through :transitionName
     */
    public function iTransitionThrough($locationURL, $transitionName): void
    {
        $locationURL = $this->argumentParser->parseUrl($locationURL);
        $content = $this->contentFacade->getContentByLocationURL($locationURL);

        $this->workflowFacade->transition($content, $transitionName, 'MOVED');
    }
}
