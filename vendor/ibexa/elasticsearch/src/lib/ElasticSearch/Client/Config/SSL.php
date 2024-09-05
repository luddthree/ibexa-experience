<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client\Config;

final class SSL
{
    /** @var bool|null */
    private $verification;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLCert|null */
    private $sslCert;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLKey|null */
    private $sslKey;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSLCACert|null */
    private $sslCACert;

    public function __construct(
        ?bool $verification = null,
        ?SSLCert $cert = null,
        ?SSLKey $cerLKey = null,
        ?SSLCACert $sslCACert = null
    ) {
        $this->verification = $verification;
        $this->sslCert = $cert;
        $this->sslKey = $cerLKey;
        $this->sslCACert = $sslCACert;
    }

    public function isVerificationEnabled(): ?bool
    {
        return $this->verification;
    }

    public function getSSLCert(): ?SSLCert
    {
        return $this->sslCert;
    }

    public function getSSLKey(): ?SSLKey
    {
        return $this->sslKey;
    }

    public function getSSLCACert(): ?SSLCACert
    {
        return $this->sslCACert;
    }

    public static function fromArray(array $properties): self
    {
        // CA certificate takes precedence over client signed certificate
        if (is_array($properties['ca_cert'] ?? null)) {
            $caCert = SSLCACert::fromArray($properties['ca_cert']);

            return new self($properties['verification'] ?? null, null, null, $caCert);
        }

        $cert = null;
        if (is_array($properties['cert'] ?? null)) {
            $cert = SSLCert::fromArray($properties['cert']);
        }

        $certKey = null;
        if (is_array($properties['cert_key'] ?? null)) {
            $certKey = SSLKey::fromArray($properties['cert_key']);
        }

        return new self($properties['verification'] ?? null, $cert, $certKey);
    }
}

class_alias(SSL::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\Config\SSL');
