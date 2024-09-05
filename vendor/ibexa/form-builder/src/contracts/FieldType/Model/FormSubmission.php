<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FieldType\Model;

use DateTimeImmutable;
use DateTimeInterface;

class FormSubmission
{
    /** @var int */
    private $id;

    /** @var int */
    private $userId;

    /** @var string */
    private $languageCode;

    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue[] */
    private $values;

    /** @var \DateTimeInterface */
    private $created;

    /**
     * @param int $id
     * @param int $userId
     * @param string $languageCode
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue[] $values
     * @param \DateTimeInterface|null $created
     *
     * @throws \Exception
     */
    public function __construct(int $id, int $userId, string $languageCode, array $values, DateTimeInterface $created = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->languageCode = $languageCode;
        $this->values = $values;
        $this->created = $created ?? new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @param int $id
     * @param int $userId
     * @param string $languageCode
     * @param array $data
     * @param \DateTimeInterface|null $created
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission
     *
     * @throws \Exception
     */
    public static function fromRawData(int $id, int $userId, string $languageCode, array $data, DateTimeInterface $created = null): self
    {
        $values = [];
        foreach ($data as $value) {
            $values[] = new FieldValue(
                $value['identifier'],
                $value['name'],
                $value['value']
            );
        }

        return new self($id, $userId, $languageCode, $values, $created);
    }
}

class_alias(FormSubmission::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Model\FormSubmission');
