<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\OAuth2Client\Controller;

use Ibexa\Contracts\OAuth2Client\Client\ClientRegistry;
use Ibexa\Contracts\OAuth2Client\Exception\Client\DisabledClientException;
use Ibexa\Contracts\OAuth2Client\Exception\Client\UnavailableClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class OAuth2Controller extends AbstractController
{
    /** @var \Ibexa\Contracts\OAuth2Client\Client\ClientRegistry */
    private $clientRegistry;

    /** @var bool */
    private $debug;

    public function __construct(ClientRegistry $clientRegistry, bool $debug = false)
    {
        $this->clientRegistry = $clientRegistry;
        $this->debug = $debug;
    }

    public function connectAction(string $identifier): Response
    {
        try {
            return $this->clientRegistry->getClient($identifier)->redirect([], []);
        } catch (DisabledClientException | UnavailableClientException $e) {
            if ($this->debug) {
                throw $e;
            }

            throw new NotFoundHttpException();
        }
    }

    public function checkAction(string $identifier): Response
    {
        if (!$this->clientRegistry->hasClient($identifier)) {
            throw new NotFoundHttpException();
        }

        return new RedirectResponse('/');
    }
}

class_alias(OAuth2Controller::class, 'Ibexa\Platform\Bundle\OAuth2Client\Controller\OAuth2Controller');
