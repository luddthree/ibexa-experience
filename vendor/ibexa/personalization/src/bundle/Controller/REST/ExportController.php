<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller\REST;

use Ibexa\Personalization\Authentication\AuthenticationInterface;
use Ibexa\Personalization\File\FileManagerInterface;
use Ibexa\Personalization\Value\Item\Action;
use Ibexa\Rest\Server\Controller as RestController;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExportController extends RestController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private AuthenticationInterface $authentication;

    private FileManagerInterface $fileManager;

    public function __construct(
        AuthenticationInterface $authentication,
        FileManagerInterface $fileManager,
        ?LoggerInterface $logger = null
    ) {
        $this->authentication = $authentication;
        $this->fileManager = $fileManager;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException
     */
    public function downloadAction(string $filePath, Request $request): Response
    {
        $response = new Response();

        if (!$this->authentication->authenticate($request, Action::ACTION_EXPORT)) {
            return $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        $content = $this->fileManager->load($filePath);

        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filePath);
        $fileSize = filesize($this->fileManager->getDir() . $filePath);

        if (is_int($fileSize)) {
            $response->headers->set('Content-Length', (string)$fileSize);
        }

        $response->setContent($content);

        return $response;
    }
}

class_alias(ExportController::class, 'EzSystems\EzRecommendationClientBundle\Controller\ExportController');
