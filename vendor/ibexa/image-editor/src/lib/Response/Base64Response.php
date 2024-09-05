<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Response;

use Ibexa\Bundle\IO\BinaryStreamResponse;
use Symfony\Component\HttpFoundation\Request;

class Base64Response extends BinaryStreamResponse
{
    public function sendContent(): void
    {
        if (!$this->isSuccessful()) {
            parent::sendContent();

            return;
        }

        if (0 === $this->maxlen) {
            return;
        }

        $out = fopen('php://output', 'w');
        $in = $this->ioService->getFileInputStream($this->file);
        stream_filter_append($in, 'convert.base64-encode');
        stream_copy_to_stream($in, $out, $this->maxlen, $this->offset);

        fclose($out);
    }

    public function prepare(Request $request): void
    {
        parent::prepare($request);

        $stream = $this->ioService->getFileInputStream($this->file);
        $this->headers->set('Content-Length', \strlen(base64_encode(stream_get_contents($stream))));
    }
}

class_alias(Base64Response::class, 'Ibexa\Platform\ImageEditor\Response\Base64Response');
