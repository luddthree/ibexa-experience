<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\FieldType\TextLine\Value;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\User\Executor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\StepExecutor\UserCreateStepExecutor;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserCreateStep;
use Ibexa\Migration\ValueObject\User\CreateMetadata;

/**
 * @covers \Ibexa\Migration\StepExecutor\UserCreateStepExecutor
 */
final class UserCreateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private const KNOWN_USER_GROUP_REMOTE_ID = '15b256dbea2ae72418ff5facc999e8f9';
    private const KNOWN_USER_GROUP_ID = '42';

    /**
     * @dataProvider provideStep
     */
    public function testHandle(StepInterface $step): void
    {
        self::assertNull($this->findUser());

        $executor = new UserCreateStepExecutor(
            self::getUserService(),
            self::getContentService(),
            self::getContentTypeService(),
            self::getFieldTypeService(),
            self::getServiceByClassName(Executor::class),
        );
        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('user'),
        ]);

        $collector = self::getServiceByClassName(CollectorInterface::class);
        $referenceCollection = $collector->getCollection();
        self::assertFalse($referenceCollection->has('generated_user_id_reference'));

        $executor->handle($step);

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\User $user */
        $user = $this->findUser();
        self::assertInstanceOf(User::class, $user);
        self::assertSame('__foo_login__', $user->login);
        self::assertSame('__foo_email__@ibexa.co', $user->email);
        self::assertTrue($user->enabled);

        $value = $user->getFieldValue('first_name');
        self::assertInstanceOf(Value::class, $value);
        self::assertSame('__foo_first_name__', $value->text);
        $value = $user->getFieldValue('last_name');
        self::assertInstanceOf(Value::class, $value);
        self::assertSame('__foo_last_name__', $value->text);

        self::assertTrue($referenceCollection->has('generated_user_id_reference'));
    }

    private function findUser(): ?User
    {
        try {
            return self::getUserService()->loadUserByLogin('__foo_login__');
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @return iterable<string, array<\Ibexa\Migration\ValueObject\Step\UserCreateStep>>
     */
    public function provideStep(): iterable
    {
        $metadata = CreateMetadata::createFromArray([
            'login' => '__foo_login__',
            'email' => '__foo_email__@ibexa.co',
            'password' => '__foo_password__',
            'enabled' => true,
            'mainLanguage' => 'eng-US',
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
        $references = [
            new ReferenceDefinition('generated_user_id_reference', 'user_id'),
        ];
        yield 'step with groups remote id' => [
            new UserCreateStep(
                $metadata,
                [
                    self::KNOWN_USER_GROUP_REMOTE_ID,
                ],
                $fields,
                $references
            ),
        ];

        yield 'step with groups id' => [
            new UserCreateStep(
                $metadata,
                [
                    self::KNOWN_USER_GROUP_ID,
                ],
                $fields,
                $references
            ),
        ];
    }
}

class_alias(UserCreateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\UserCreateStepExecutorTest');
