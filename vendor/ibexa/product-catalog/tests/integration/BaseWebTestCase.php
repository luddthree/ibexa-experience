<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog;

use Ibexa\Contracts\Test\Rest\WebTestCase;
use JsonSchema\Validator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class BaseWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    /**
     * @param array<string, string> $headers
     * @param array<string, string> $parameters
     */
    protected function assertClientRequest(
        string $method,
        string $uri,
        array $headers,
        ?string $body = null,
        array $parameters = []
    ): void {
        $this->client->request($method, $uri, $parameters, [], $headers, $body);

        self::assertResponseIsSuccessful();

        $this->assertResponseIsValid(
            (string)$this->client->getResponse()->getContent(),
        );
    }

    abstract protected function getResourceType(): ?string;

    abstract protected function assertResponseIsValid(string $response): void;

    /**
     * @throws \JsonException
     */
    protected function validateAgainstJSONSchema(string $data): void
    {
        $validator = self::getJsonSchemaValidator();
        $decodedData = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
        $schemaReference = [
            '$ref' => 'file://' . $this->getSchemaFileLocation(),
        ];

        $validator->validate($decodedData, $schemaReference);

        self::assertTrue($validator->isValid(), $this->convertErrorsToString($validator, $data));
    }

    private function convertErrorsToString(Validator $validator, string $response): string
    {
        $errorMessage = '';
        foreach ($validator->getErrors() as $error) {
            $errorMessage .= sprintf(
                "schema: [%s], property: [%s], constraint: %s, error: %s\n\nResponse: \n\n%s",
                $this->getSchemaFileLocation(),
                $error['property'],
                $error['constraint'],
                $error['message'],
                $response,
            );
        }

        return $errorMessage;
    }

    final protected function getSchemaDirectoryLocation(): string
    {
        return __DIR__ . '/JsonSchema';
    }

    private function getSchemaFileLocation(): string
    {
        return $this->getSchemaDirectoryLocation() . '/' . $this->getResourceType() . '.json';
    }

    final protected function decodeJsonObject(string $content): object
    {
        return json_decode($content, false, 512, JSON_THROW_ON_ERROR);
    }

    final protected function loadFile(string $location): string
    {
        $contents = file_get_contents($location);
        if (empty($contents)) {
            throw new \LogicException(sprintf(
                'Unable to load file: %s',
                $location,
            ));
        }

        return $contents;
    }
}
