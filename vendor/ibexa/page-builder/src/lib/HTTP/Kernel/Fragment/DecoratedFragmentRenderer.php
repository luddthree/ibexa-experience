<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\HTTP\Kernel\Fragment;

use Ibexa\Bundle\FieldTypePage\Controller\BlockController;
use Ibexa\PageBuilder\HTTP\Kernel\ControllerResolver;
use Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator;
use Ibexa\PageBuilder\Security\EditorialMode\TokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\AbstractSurrogateFragmentRenderer;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Injects original siteaccess as an attribute to Block Render fragments.
 *
 * @internal
 *
 * @todo Remove in next major. Use { @see \Ibexa\FieldTypePage\Event\PageEvents } PRE_RENDER event instead.
 */
class DecoratedFragmentRenderer extends AbstractSurrogateFragmentRenderer implements FragmentRendererInterface
{
    /** @var \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface */
    private $innerRenderer;

    /** @var \Ibexa\PageBuilder\HTTP\Kernel\ControllerResolver */
    private $controllerResolver;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Symfony\Component\HttpKernel\HttpCache\SurrogateInterface */
    private $surrogate;

    /** @var \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface */
    private $inlineStrategy;

    /** @var \Symfony\Component\HttpKernel\UriSigner */
    private $signer;

    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator */
    private $authenticator;

    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenManager */
    private $jwtTokenManager;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface */
    private $kernel;

    /**
     * @param \Symfony\Component\HttpKernel\HttpCache\SurrogateInterface $surrogate
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface $inlineStrategy
     * @param \Symfony\Component\HttpKernel\UriSigner $signer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface $innerRenderer
     * @param \Ibexa\PageBuilder\HTTP\Kernel\ControllerResolver $controllerResolver
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator $authenticator
     * @param \Ibexa\PageBuilder\Security\EditorialMode\TokenManager $jwtTokenManager
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     */
    public function __construct(
        SurrogateInterface $surrogate = null,
        FragmentRendererInterface $inlineStrategy,
        UriSigner $signer = null,
        FragmentRendererInterface $innerRenderer,
        ControllerResolver $controllerResolver,
        RequestStack $requestStack,
        ?TokenAuthenticator $authenticator,
        ?TokenManager $jwtTokenManager,
        ?TokenStorageInterface $tokenStorage,
        HttpKernelInterface $kernel
    ) {
        parent::__construct($surrogate, $inlineStrategy, $signer);

        $this->innerRenderer = $innerRenderer;
        $this->controllerResolver = $controllerResolver;
        $this->requestStack = $requestStack;
        $this->surrogate = $surrogate;
        $this->inlineStrategy = $inlineStrategy;
        $this->signer = $signer;
        $this->authenticator = $authenticator;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->tokenStorage = $tokenStorage;
        $this->kernel = $kernel;
    }

    /**
     * Renders a URI and returns the Response content.
     *
     * @param string|\Symfony\Component\HttpKernel\Controller\ControllerReference $uri
     * @param \Symfony\Component\HttpFoundation\Request $request A Request instance
     * @param array $options An array of options
     *
     * @return \Symfony\Component\HttpFoundation\Response A Response instance
     */
    public function render($uri, Request $request, array $options = [])
    {
        $parentRequest = $this->requestStack->getParentRequest();

        if (
            null !== $parentRequest
            && $uri instanceof ControllerReference
            && $this->doesReferenceBlockRender($uri)
        ) {
            $this->decorateWithOriginalSiteAccess($uri);
            $this->decorateWithEditorialModeBearer($uri);
        }

        if (
            $this->doesReferenceBlockRender($uri)
            && $this->surrogate
            && $this->surrogate->hasSurrogateCapability($request)
            && !$request->isMethodCacheable()
        ) {
            return $this->inlineStrategy->render($uri, $request, $options);
        }

        return $this->innerRenderer->render($uri, $request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->innerRenderer->getName();
    }

    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerReference $controllerReference
     *
     * @return bool
     */
    private function doesReferenceBlockRender(ControllerReference $controllerReference): bool
    {
        $controllerCallable = $this->controllerResolver->getCallableFromControllerReference($controllerReference);

        return \is_array($controllerCallable) && $controllerCallable[0] instanceof BlockController && 'renderAction' === $controllerCallable[1];
    }

    /**
     * Injects original siteaccess as an attribute to Block Render fragments.
     *
     * @param \Symfony\Component\HttpKernel\Controller\ControllerReference $uri
     */
    private function decorateWithOriginalSiteAccess(ControllerReference $uri): void
    {
        $parentRequest = $this->requestStack->getParentRequest();

        if (!$parentRequest->attributes->has('_forwarded')) {
            return;
        }

        /** @var \Symfony\Component\HttpFoundation\ParameterBag $forwardedAttributes */
        $forwardedAttributes = $parentRequest->attributes->get('_forwarded');
        /** @var \Ibexa\Core\MVC\Symfony\SiteAccess $originalSiteaccess */
        $originalSiteaccess = $forwardedAttributes->get('siteaccess');

        $uri->attributes['original_siteaccess'] = $originalSiteaccess->name;
    }

    /**
     * Injects authentication token as a query parameter to Block Render fragments.
     *
     * @param \Symfony\Component\HttpKernel\Controller\ControllerReference $uri
     */
    private function decorateWithEditorialModeBearer(ControllerReference $uri): void
    {
        if ($this->authenticator === null || !$this->authenticator->isEnabled()) {
            return;
        }

        if ($this->kernel instanceof HttpCache) {
            // Skip decoration if Symfony Reverse Proxy is enabled
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof UserInterface) {
            // Skip decoration if user is .anon
            return;
        }

        $uri->query[$this->authenticator->getTokenQueryParamName()] = $this->jwtTokenManager->create(
            $token->getUser()
        );
    }
}

class_alias(DecoratedFragmentRenderer::class, 'EzSystems\EzPlatformPageBuilder\HTTP\Kernel\Fragment\DecoratedFragmentRenderer');
