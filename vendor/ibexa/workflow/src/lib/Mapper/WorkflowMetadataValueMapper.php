<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Mapper;

use Ibexa\Contracts\Core\Persistence\ValueObject as PersistenceValueObject;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as ApiNotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface;
use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Ibexa\Workflow\Value\WorkflowMarkingCollection;
use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\Value\WorkflowTransitionCollection;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\VirtualProxyInterface;

class WorkflowMetadataValueMapper implements ValueMapper
{
    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $workflowHandler;

    /** @var \Symfony\Component\Workflow\Registry */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Workflow\Mapper\MarkingMetadataValueMapper */
    private $markingMetadataValueMapper;

    /** @var \Ibexa\Workflow\Mapper\TransitionMetadataValueMapper */
    private $transitionMetadataValueMapper;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface */
    private $proxyGenerator;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface */
    private $proxyDomainMapper;

    public function __construct(
        HandlerInterface $workflowHandler,
        WorkflowRegistryInterface $workflowRegistry,
        ContentService $contentService,
        UserService $userService,
        MarkingMetadataValueMapper $markingMetadataValueMapper,
        TransitionMetadataValueMapper $transitionMetadataValueMapper,
        ProxyGeneratorInterface $proxyGenerator,
        ProxyDomainMapperInterface $proxyDomainMapper
    ) {
        $this->workflowHandler = $workflowHandler;
        $this->workflowRegistry = $workflowRegistry;
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->markingMetadataValueMapper = $markingMetadataValueMapper;
        $this->transitionMetadataValueMapper = $transitionMetadataValueMapper;
        $this->proxyGenerator = $proxyGenerator;
        $this->proxyDomainMapper = $proxyDomainMapper;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function fromPersistenceValue(PersistenceValueObject $object): ValueObject
    {
        $user = null;
        try {
            $user = $this->userService->loadUser((int)$object->initialOwnerId);
        } catch (ApiNotFoundException $ex) {
        }

        $workflowId = (int)$object->id;
        $contentId = $object->contentId;

        $contentInfo = $this->contentService->loadContentInfo($contentId);
        $content = $this->proxyDomainMapper->createContentProxy(
            $contentId,
            Language::ALL,
            true,
            $object->versionNo
        );
        $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $object->versionNo);

        $workflow = $this->workflowRegistry->getWorkflow($object->name);
        $transitions = $this->createTransitionCollectionProxy($workflowId);
        $markings = $this->createMarkingCollectionProxy($workflowId);

        return new WorkflowMetadata(
            [
                'id' => $object->id,
                'name' => $object->name,
                'content' => $content,
                'versionInfo' => $versionInfo,
                'workflow' => $workflow,
                'transitions' => $transitions,
                'markings' => $markings,
                'initialOwner' => $user,
            ]
        );
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowTransitionCollection|\ProxyManager\Proxy\VirtualProxyInterface
     */
    private function createTransitionCollectionProxy(int $workflowId): VirtualProxyInterface
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($workflowId): bool {
            $initializer = null;

            $transitions = $this->workflowHandler->loadTransitionMetadataByWorkflowId($workflowId);
            $transitions = array_map(
                [$this->transitionMetadataValueMapper, 'fromPersistenceValue'],
                $transitions
            );

            $wrappedObject = new WorkflowTransitionCollection($transitions);

            return true;
        };

        return $this->proxyGenerator->createProxy(WorkflowTransitionCollection::class, $initializer);
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowTransitionCollection|\ProxyManager\Proxy\VirtualProxyInterface
     */
    private function createMarkingCollectionProxy(int $workflowId): VirtualProxyInterface
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($workflowId): bool {
            $initializer = null;

            $markings = $this->workflowHandler->loadMarkingMetadataByWorkflowId($workflowId);
            $markings = array_map(
                [$this->markingMetadataValueMapper, 'fromPersistenceValue'],
                $markings
            );

            $wrappedObject = new WorkflowMarkingCollection($markings);

            return true;
        };

        return $this->proxyGenerator->createProxy(WorkflowMarkingCollection::class, $initializer);
    }
}

class_alias(WorkflowMetadataValueMapper::class, 'EzSystems\EzPlatformWorkflow\Mapper\WorkflowMetadataValueMapper');
