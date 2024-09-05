<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation;

use Ibexa\Bundle\Core\Routing\DefaultRouter;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Test\IbexaKernelTestTrait;
use Ibexa\Core\MVC\Symfony\Security\UserWrapped;
use JsonSchema\Validator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractWebTestCase extends WebTestCase
{
    use IbexaKernelTestTrait;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        self::getContainer()->set(
            DefaultRouter::class,
            $this->createMock(DefaultRouter::class)
        );

        $user = $this->getUser();
        $this->client->loginUser($user);

        $oermissionResolver = self::getServiceByClassName(PermissionResolver::class);
        $oermissionResolver->setCurrentUserReference($user->getAPIUserReference());
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, string> $parameters
     */
    final public static function assertClientJsonRequest(
        KernelBrowser $client,
        string $method,
        string $uri,
        array $headers,
        ?string $body = null,
        array $parameters = []
    ): void {
        $client->request($method, $uri, $parameters, [], $headers, $body);

        self::assertResponseIsSuccessful();

        $resourceType = static::getResourceType();
        if ($resourceType !== null) {
            self::assertJsonResponseIsValid(
                (string)$client->getResponse()->getContent(),
                $resourceType
            );
        }
    }

    abstract protected static function getResourceType(): ?string;

    final protected static function assertResponseMatchesXmlSnapshot(string $content, ?string $file = null): void
    {
        self::assertStringMatchesSnapshot($content, 'xml', $file);
    }

    final protected static function assertResponseMatchesJsonSnapshot(string $content, ?string $file = null): void
    {
        self::assertStringMatchesSnapshot($content, 'json', $file);
    }

    /**
     * @phpstan-param "xml"|"json"|null $type
     */
    final protected static function assertStringMatchesSnapshot(
        string $content,
        ?string $type = null,
        ?string $file = null
    ): void {
        if ($file === null) {
            $classInfo = new \ReflectionClass(static::class);
            $class = substr(static::class, strrpos(static::class, '\\') + 1);
            $classFilename = $classInfo->getFileName();
            self::assertNotFalse($classFilename);
            $file = dirname($classFilename) . '/' . $class . '.' . ($type ?? 'log');
        }
        if (!file_exists($file)) {
            file_put_contents($file, rtrim($content, "\n") . "\n");
        }

        if ($type === 'xml') {
            self::assertXmlStringEqualsXmlFile($file, $content);
        } elseif ($type === 'json') {
            self::assertJsonStringEqualsJsonFile($file, $content);
        } else {
            self::assertStringEqualsFile($file, $content);
        }
    }

    /**
     * @throws \JsonException
     */
    protected static function assertJsonResponseIsValid(string $response, string $resourceType): void
    {
        self::assertJson($response);
        self::assertStringContainsString($resourceType, $response);
        self::assertResponseHeaderSame(
            'Content-Type',
            'application/vnd.ibexa.api.' . $resourceType . '+json'
        );

        self::validateAgainstJSONSchema($response);
    }

    final protected function getUser(): UserWrapped
    {
        $userService = self::getServiceByClassName(UserService::class);
        $apiUser = $userService->loadUserByLogin('admin');
        $symfonyUser = $this->createMock(UserInterface::class);
        $symfonyUser->method('getRoles')->willReturn(['ROLE_USER']);

        return new UserWrapped($symfonyUser, $apiUser);
    }

    /**
     * @throws \JsonException
     */
    final protected static function validateAgainstJSONSchema(string $data): void
    {
        $validator = new Validator();
        $decodedData = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
        $schemaReference = [
            '$ref' => 'file://' . self::getSchemaFileLocation(),
        ];

        $validator->validate($decodedData, $schemaReference);

        self::assertTrue($validator->isValid(), self::convertErrorsToString($validator));
    }

    private static function convertErrorsToString(Validator $validator): string
    {
        $errorMessage = '';
        foreach ($validator->getErrors() as $error) {
            $errorMessage .= sprintf(
                "property: [%s], constraint: %s, error: %s\n",
                $error['property'],
                $error['constraint'],
                $error['message']
            );
        }

        return $errorMessage;
    }

    private static function getSchemaFileLocation(): string
    {
        return __DIR__ . '/JsonSchema/' . static::getResourceType() . '.json';
    }
}
