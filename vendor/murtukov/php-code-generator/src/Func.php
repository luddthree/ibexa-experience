<?php

declare(strict_types=1);

namespace Murtukov\PHPCodeGenerator;

class Func extends AbstractFunction implements BlockInterface
{
    use ScopedContentTrait;
    use DocBlockTrait;

    final public function __construct(string $name, string $returnType = '')
    {
        $this->signature = new Signature($name, Modifier::NONE, $returnType);
        $this->dependencyAwareChildren = [$this->signature];
    }

    public static function new(string $name, string $returnType = ''): self
    {
        return new static($name, $returnType);
    }

    public function generate(): string
    {
        $content = $this->generateWrappedContent("\n", '');

        return <<<CODE
        {$this->buildDocBlock()}{$this->signature->generate(false)}
        {{$content}
        }
        CODE;
    }
}
