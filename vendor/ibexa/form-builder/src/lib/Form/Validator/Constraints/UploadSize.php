<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UploadSize extends Constraint
{
    public const UPLOAD_SIZE_EXCEEDED_ERROR = '15fa60f8-5bc5-445b-a348-5c003c62f72a';

    /** @var int */
    public $uploadSize;

    public $uploadSizeMessage = 'File is to big.';

    /**
     * @param int $uploadSize
     * @param string|null $message
     */
    public function __construct(int $uploadSize, ?string $message = null)
    {
        $options = ['uploadSize' => $uploadSize];

        if ($message !== null) {
            $options['uploadSizeMessage'] = $message;
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return UploadSizeValidator::class;
    }
}

class_alias(UploadSize::class, 'EzSystems\EzPlatformFormBuilder\Form\Validator\Constraints\UploadSize');
