# Ibexa DXP Product catalog

This package is part of [Ibexa DXP](https://ibexa.co).

To use this package, [install Ibexa DXP](https://doc.ibexa.co/en/latest/install/).

This package contains the product catalog functionality for [Ibexa DXP](https://ibexa.co).

## Contributing

### Running tests

You can run tests locally by running:
```shell
composer test
```

You can test Elasticsearch and Solr integration by running:
```shell
docker-compose up --detach
# wait for services to become "healthy"
docker-compose ps

# Solr
SOLR_DSN="http://$(docker-compose port solr 8983)/solr" SEARCH_ENGINE=solr composer test

# Elasticsearch
ELASTICSEARCH_DSN=$(docker-compose port elasticsearch 9200) SEARCH_ENGINE=elasticsearch composer test
```

### Extracting translations

Please ensure that you have the following configuration on project level:

```yaml
jms_translation:
    dumper:
        add_references: false
        add_date: false
```

and then run:

```shell
php bin/console translation:extract --config=ibexa_product_catalog en
```

## COPYRIGHT

Copyright (C) 1999-2024 Ibexa AS (formerly eZ Systems AS). All rights reserved.

## LICENSE

This source code is available separately under the following licenses:

A - Ibexa Business Use License Agreement (Ibexa BUL),
version 2.3 or later versions (as license terms may be updated from time to time)
Ibexa BUL is granted by having a valid Ibexa DXP (formerly eZ Platform Enterprise) subscription,
as described at: https://www.ibexa.co/product
For the full Ibexa BUL license text, please see:
- LICENSE-bul file placed in the root of this source code, or
- https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)

AND

B - Ibexa Trial and Test License Agreement (Ibexa TTL),
version 2.2 or later versions (as license terms may be updated from time to time)
Trial can be granted by Ibexa, reach out to Ibexa AS for evaluation access: https://www.ibexa.co/about-ibexa/contact-us
For the full Ibexa TTL license text, please see:
- LICENSE file placed in the root of this source code, or
- https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)


