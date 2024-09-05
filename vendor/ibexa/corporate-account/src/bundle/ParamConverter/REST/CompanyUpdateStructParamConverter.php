<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\ParamConverter\REST;

use Ibexa\CorporateAccount\REST\Value\CompanyUpdateStruct;
use Ibexa\Rest\Input\Dispatcher as InputDispatcher;
use Ibexa\Rest\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class CompanyUpdateStructParamConverter implements ParamConverterInterface
{
    public const PARAMETER_COMPANY_UPDATE_STRUCT = 'companyUpdateStruct';

    private InputDispatcher $inputDispatcher;

    public function __construct(InputDispatcher $inputDispatcher)
    {
        $this->inputDispatcher = $inputDispatcher;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        /** @var \Ibexa\CorporateAccount\REST\Value\CompanyUpdateStruct $companyUpdateStruct */
        $companyUpdateStruct = $this->inputDispatcher->parse(
            new Message(
                [
                    'Content-Type' => $request->headers->get('Content-Type'),
                    'Url' => $request->getPathInfo(),
                ],
                $request->getContent()
            )
        );

        $request->attributes->set($configuration->getName(), $companyUpdateStruct);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return CompanyUpdateStruct::class === $configuration->getClass()
            && self::PARAMETER_COMPANY_UPDATE_STRUCT === $configuration->getName();
    }
}
