<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller\REST\UserSegment;

use Ibexa\Bundle\Segmentation\REST\Input\UserSegmentAssignInput;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class UserSegmentAssignController extends RestController
{
    private UserService $userService;

    private SegmentationServiceInterface $segmentationService;

    private TransactionHandler $transactionHandler;

    public function __construct(
        UserService $userService,
        SegmentationServiceInterface $segmentationService,
        TransactionHandler $transactionHandler
    ) {
        $this->userService = $userService;
        $this->segmentationService = $segmentationService;
        $this->transactionHandler = $transactionHandler;
    }

    public function assignSegmentToUser(int $userId, Request $request): Value
    {
        $assignStruct = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                (string)$request->getContent()
            )
        );

        if (!$assignStruct instanceof UserSegmentAssignInput) {
            throw new InvalidArgumentException(
                '$assignStruct',
                'expected ' . UserSegmentAssignInput::class,
            );
        }

        $user = $this->userService->loadUser($userId);
        $this->transactionHandler->beginTransaction();
        try {
            foreach ($assignStruct->segments as $segment) {
                $this->segmentationService->assignUserToSegment($user, $segment);
            }
            $this->transactionHandler->commit();
        } catch (Throwable $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        return new NoContent();
    }
}
