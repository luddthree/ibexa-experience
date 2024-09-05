<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserLogin;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\FieldType\TextLine\Value;
use Ibexa\Migration\StepExecutor\ActionExecutor\User\Executor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\StepExecutor\UserUpdateStepExecutor;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserUpdateStep;
use Ibexa\Migration\ValueObject\User\UpdateMetadata;

/**
 * @covers \Ibexa\Migration\StepExecutor\UserUpdateStepExecutor
 */
final class UserUpdateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private const USER_ID = 14;
    private const USER_LOGIN = 'admin';

    /**
     * @dataProvider providerStep
     */
    public function testHandle(StepInterface $step): void
    {
        self::assertInstanceOf(User::class, $this->findUser());

        $executor = new UserUpdateStepExecutor(
            self::getUserService(),
            self::getContentService(),
            self::getFieldTypeService(),
            self::getServiceByClassName(Executor::class),
        );
        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('user'),
        ]);

        $executor->handle($step);

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\User $user */
        $user = $this->findUser();

        self::assertInstanceOf(User::class, $user);
        self::assertSame('admin', $user->login);
        self::assertSame('__foo_email__@ibexa.co', $user->email);
        self::assertFalse($user->enabled);

        $value = $user->getFieldValue('first_name');
        self::assertInstanceOf(Value::class, $value);
        self::assertSame('__foo_first_name__', (string) $value);
        $value = $user->getFieldValue('last_name');
        self::assertInstanceOf(Value::class, $value);
        self::assertSame('__foo_last_name__', (string) $value);
    }

    private function findUser(): ?User
    {
        try {
            return self::getUserService()->loadUserByLogin('admin');
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @return iterable<string, iterable<\Ibexa\Migration\ValueObject\Step\UserUpdateStep>>
     */
    public function providerStep(): iterable
    {
        $metadata = UpdateMetadata::createFromArray([
            'email' => '__foo_email__@ibexa.co',
            'enabled' => false,
        ]);
        $fields = [
            Field::createFromArray([
                'fieldDefIdentifier' => 'first_name',
                'languageCode' => 'eng-US',
                'value' => '__foo_first_name__',
            ]),
            Field::createFromArray([
                'fieldDefIdentifier' => 'last_name',
                'languageCode' => 'eng-US',
                'value' => '__foo_last_name__',
            ]),
        ];

        yield 'match by user_id' => [
            new UserUpdateStep(
                $metadata,
                new UserId(self::USER_ID),
                $fields
            ),
        ];

        yield 'match by user_login' => [
            new UserUpdateStep(
                $metadata,
                new UserLogin(self::USER_LOGIN),
                $fields
            ),
        ];
    }
}

class_alias(UserUpdateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\UserUpdateStepExecutorTest');
