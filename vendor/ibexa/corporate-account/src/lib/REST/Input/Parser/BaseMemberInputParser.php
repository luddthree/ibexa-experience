<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @phpstan-import-type RESTResourceReferenceInputArray from BaseContentParser
 */
abstract class BaseMemberInputParser extends BaseContentParser
{
    public const EMAIL_KEY = 'email';
    public const PASSWORD_KEY = 'password';
    public const ROLE_KEY = 'Role';
    public const ENABLED_KEY = 'enabled';
    public const MAX_LOGIN_KEY = 'maxLogin';

    private RoleService $roleService;

    public function __construct(
        RequestParser $requestParser,
        FieldTypeParser $fieldTypeParser,
        RoleService $roleService,
        ValidatorInterface $validator
    ) {
        parent::__construct($requestParser, $fieldTypeParser, $validator);

        $this->roleService = $roleService;
    }

    /**
     * @phpstan-param RESTResourceReferenceInputArray $roleData
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    final protected function loadRole(array $roleData): Role
    {
        return $this->roleService->loadRole(
            (int)$this->requestParser->parseHref(
                $roleData['_href'],
                'roleId'
            )
        );
    }
}
