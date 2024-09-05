<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Validator\Constraints;

use Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlankValidator;

final class NotBlankRichTextValidator extends NotBlankValidator implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory */
    private $domDocumentFactory;

    public function __construct(DOMDocumentFactory $domDocumentFactory, LoggerInterface $logger = null)
    {
        $this->domDocumentFactory = $domDocumentFactory;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        try {
            if (is_string($value) && $value !== '') {
                $xmlData = $this->domDocumentFactory->loadXMLString($value);
                if ($xmlData->documentElement->hasChildNodes()) {
                    return;
                }
            }

            parent::validate(null, $constraint);
        } catch (\Exception $e) {
            $message = sprintf('Invalid RichText XML string (%s). %s', $value, $e->getMessage());
            $this->logger->warning(
                $message,
                [
                    'exception' => $e,
                ]
            );
            $this->context->addViolation($message);
        }
    }
}
