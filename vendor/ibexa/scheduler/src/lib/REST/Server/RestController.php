<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\REST\Server;

use Ibexa\Rest\Input\Dispatcher as InputDispatcher;
use Ibexa\Rest\Message;
use Symfony\Component\HttpFoundation\Request;

/**
 * REST Controller.
 */
abstract class RestController
{
    /**
     * @var \Ibexa\Rest\Input\Dispatcher
     */
    protected $inputDispatcher;

    /**
     * Sets input dispatcher.
     *
     * @param \Ibexa\Rest\Input\Dispatcher $inputDispatcher
     */
    public function setInputDispatcher(InputDispatcher $inputDispatcher)
    {
        $this->inputDispatcher = $inputDispatcher;
    }

    /**
     * Parse request ContentType.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    protected function parseRequestContentType(Request $request)
    {
        return $this->inputDispatcher->parse(
            new Message(
                [
                    'Content-Type' => $request->headers->get('Content-Type'),
                ],
                $request->getContent()
            )
        );
    }
}

class_alias(RestController::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\RestController');
