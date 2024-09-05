<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\HTTP\Kernel;

use function is_callable;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver
{
    /** @var \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface */
    private $controllerResolver;

    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $controllerResolver
     */
    public function __construct(
        ControllerResolverInterface $controllerResolver
    ) {
        $this->controllerResolver = $controllerResolver;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerReference $controllerReference
     *
     * @return callable
     *
     * @throws \RuntimeException
     */
    public function getCallableFromControllerReference(ControllerReference $controllerReference): callable
    {
        $mockRequest = new Request();
        $mockRequest->attributes->set('_controller', $controllerReference->controller);

        $callable = $this->controllerResolver->getController($mockRequest);

        if (!is_callable($callable)) {
            throw new RuntimeException('Cannot resolve the callable from ControllerReference');
        }

        return $callable;
    }
}

class_alias(ControllerResolver::class, 'EzSystems\EzPlatformPageBuilder\HTTP\Kernel\ControllerResolver');
