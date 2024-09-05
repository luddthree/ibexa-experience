<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\ParamConverter\REST;

use Ibexa\CorporateAccount\REST\Value\MemberUpdateStruct;
use Ibexa\Rest\Input\Dispatcher as InputDispatcher;
use Ibexa\Rest\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class MemberUpdateStructParamConverter implements ParamConverterInterface
{
    public const PARAMETER_MEMBER_UPDATE_STRUCT = 'memberUpdateStruct';

    private InputDispatcher $inputDispatcher;

    public function __construct(InputDispatcher $inputDispatcher)
    {
        $this->inputDispatcher = $inputDispatcher;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        /** @var \Ibexa\CorporateAccount\REST\Value\MemberUpdateStruct $memberUpdateStruct */
        $memberUpdateStruct = $this->inputDispatcher->parse(
            new Message(
                [
                    'Content-Type' => $request->headers->get('Content-Type'),
                    'Url' => $request->getPathInfo(),
                ],
                $request->getContent()
            )
        );

        $request->attributes->set($configuration->getName(), $memberUpdateStruct);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return MemberUpdateStruct::class === $configuration->getClass()
            && self::PARAMETER_MEMBER_UPDATE_STRUCT === $configuration->getName();
    }
}
