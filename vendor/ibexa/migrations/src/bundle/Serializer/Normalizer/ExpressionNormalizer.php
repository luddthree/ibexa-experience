<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Template\ExpressionLanguageContext;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ExpressionNormalizer implements
    NormalizerInterface,
    NormalizerAwareInterface,
    DenormalizerInterface,
    DenormalizerAwareInterface,
    ContextAwareDenormalizerInterface,
    ContextAwareNormalizerInterface,
    LoggerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use LoggerAwareTrait;

    public const PERFORM_REPLACE_KEY = 'perform_replace';
    private const ESCAPED_EXPRESSIONS = 'ibexa.escaped_expressions';

    public const TEMPLATE_CONTEXT_KEY = 'ibexa.template_context';

    private ExpressionLanguage $expressionLanguage;

    private ExpressionLanguageContext $defaultContext;

    public function __construct(
        ExpressionLanguage $expressionLanguage,
        ExpressionLanguageContext $defaultContext
    ) {
        $this->expressionLanguage = $expressionLanguage;
        $this->defaultContext = $defaultContext;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface $object
     * @param array<mixed> $context
     *
     * @return array<mixed>
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $result = $this->normalizer->normalize($object, $format, $context + [
            self::ESCAPED_EXPRESSIONS => true,
        ]);

        Assert::isArray($result);

        array_walk_recursive($result, static function (&$value): void {
            if (!is_string($value)) {
                return;
            }

            // Check if expression encompasses the whole string. If yes, set value to expression result to prevent in
            // from being converted to string.
            $value = preg_replace(
                '~^(?<delimiter>###)(\s+)(?<expression>.*?)(\s+)\1$~s',
                '\\#\\#\\#$2$3$4\\#\\#\\#',
                $value,
            );

            Assert::string($value, preg_last_error_msg());

            $replacement = static function (array $data): string {
                return sprintf(
                    '\\#\\#\\#%s%s%s%s%s\\#\\#\\#',
                    $data['identifier'],
                    $data['first_separator'],
                    $data['expression'],
                    $data['second_separator'],
                    $data['identifier'],
                );
            };

            $value = preg_replace_callback(
                '~(?<delimiter>###)(?<identifier>\w+)(?<first_separator>\s+)(?<expression>.*?)(?<second_separator>\s+)\2\1~s',
                $replacement,
                $value,
            );
        });

        return $result;
    }

    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (!$data instanceof StepInterface) {
            return false;
        }

        return ($context[self::ESCAPED_EXPRESSIONS] ?? false) === false;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $templateContext = $context[self::TEMPLATE_CONTEXT_KEY] ?? [];
        $templateContext['context'] = array_merge(
            $templateContext['context'] ?? [],
            $this->defaultContext->all(),
        );

        array_walk_recursive($data, function (&$value) use ($templateContext): void {
            if (!is_string($value)) {
                return;
            }

            $replacement = function (array $data) use ($templateContext) {
                try {
                    return $this->expressionLanguage->evaluate(
                        $data['expression'],
                        $templateContext,
                    );
                } catch (SyntaxError $e) {
                    throw new NotNormalizableValueException(sprintf(
                        'Invalid expression encountered: "%s". Error evaluating expression: "%s". Available context keys: "%s"',
                        $data['expression'],
                        $e->getMessage(),
                        implode('", "', array_keys($templateContext['context'])),
                    ), $e->getCode(), $e);
                }
            };

            // Check if expression encompasses the whole string. If yes, set value to expression result to prevent in
            // from being converted to string.
            if (preg_match('~^(?<delimiter>###)(?<identifier>\w*)\s+(?<expression>.*?)\s+\2\1$~s', $value, $matches) === 1) {
                $value = $replacement($matches);
            } else {
                $value = preg_replace_callback('~(?<delimiter>###)(?<identifier>\w+)\s+(?<expression>.*?)\s+\2\1~s', $replacement, $value);
            }

            if (is_string($value)) {
                // Revert escaping
                $value = $this->revertEscapeDelimiters($value);
            }
        });

        $context[self::PERFORM_REPLACE_KEY] = false;

        return $this->denormalizer->denormalize(
            $data,
            $type,
            $format,
            $context
        );
    }

    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        if (!is_subclass_of($type, StepInterface::class)) {
            return false;
        }

        return ($context[self::PERFORM_REPLACE_KEY] ?? true) === true;
    }

    private function revertEscapeDelimiters(string $value): string
    {
        // Check if expression encompasses the whole string. If yes, set value to expression result to prevent in
        // from being converted to string.
        $value = preg_replace(
            '~^(?<delimiter>\\\\#\\\\#\\\\#)(\s+)(?<expression>.*?)(\s+)\1$~s',
            '###$2$3$4###',
            $value,
        );

        Assert::string($value, preg_last_error_msg());

        $replacement = static function (array $data): string {
            return sprintf(
                '###%s%s%s%s%s###',
                $data['identifier'],
                $data['first_separator'],
                $data['expression'],
                $data['second_separator'],
                $data['identifier'],
            );
        };

        $value = preg_replace_callback(
            '~(?<delimiter>\\\\#\\\\#\\\\#)(?<identifier>\w+)(?<first_separator>\s+)(?<expression>.*?)(?<second_separator>\s+)\2\1~s',
            $replacement,
            $value,
        );

        Assert::string($value, preg_last_error_msg());

        return $value;
    }
}
