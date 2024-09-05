<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser;

use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

abstract class AbstractParser extends BaseParser
{
    abstract protected function getName(): string;

    /** @return array<string> */
    protected function getMandatoryKeys(): array
    {
        return [];
    }

    /**
     * Any keys are allowed if this method returns null.
     *
     * @return array<string>|null
     */
    protected function getOptionalKeys(): ?array
    {
        return null;
    }

    /**
     * @param array<mixed> $data
     *
     * @return mixed
     */
    abstract protected function innerParse(array $data, ParsingDispatcher $parsingDispatcher);

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    protected function normalize(array $data): array
    {
        return $data;
    }

    /**
     * @param array<mixed> $data
     *
     * @return mixed
     */
    final public function parse(array $data, ParsingDispatcher $parsingDispatcher)
    {
        $name = $this->getName();

        $data = $this->normalize($data);
        // TODO: Validate using Symfony Validator
        $this->validateMandatoryKeys($data, $name);
        $this->validateRedundantKeys($data, $name);

        return $this->innerParse($data, $parsingDispatcher);
    }

    /**
     * @param array<mixed> $data
     */
    final protected function validateRedundantKeys(array $data, string $name): void
    {
        $optionalKeys = $this->getOptionalKeys();
        if ($optionalKeys === null) {
            return;
        }

        $inputKeys = array_keys($data);
        $keys = array_merge($this->getMandatoryKeys(), $optionalKeys);
        $redundantKeys = array_diff($inputKeys, $keys);

        if (count($redundantKeys) > 0) {
            throw new Parser(
                sprintf(
                    'The following properties for %s are redundant: %s. [%s]',
                    $name,
                    implode(', ', $redundantKeys),
                    static::class,
                )
            );
        }
    }

    /**
     * @param array<mixed> $data
     */
    final protected function validateMandatoryKeys(array $data, string $name): void
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff($this->getMandatoryKeys(), $inputKeys);

        if (count($missingKeys) > 0) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for %s. [%s]',
                    implode(', ', $missingKeys),
                    $name,
                    static::class,
                )
            );
        }
    }
}
