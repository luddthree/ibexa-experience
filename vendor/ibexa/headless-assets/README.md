# ibexa/headless-assets

This package is part of [Ibexa DXP](https://ibexa.co).

To use this package, [install Ibexa DXP](https://doc.ibexa.co/en/latest/install/).

## Preparing tag for release

To prepare a tag for release you have to run the following command from the root directory of the bundle:

```
sh bin/prepare_release.sh -v 1.0.0 -b main
```

Options:

1. -v : tag to be released
1. -b : branch used to create the tag

If you are tagging for Ibexa DXP 4.6.x LTS you should use the 4.6 branch.

In order to create tag you need access to the [QNTM](https://github.com/qntmgroup) organization and you must add the `@qntmgroup` NPM registry (which is private). To do so, you must add the following lines to your NPM configuration (~/.npmrc):

```
@qntmgroup:registry=https://npm.pkg.github.com/
//npm.pkg.github.com/:_authToken=GITHUB_AUTH_TOKEN
```

## COPYRIGHT

Copyright (C) 1999-2024 Ibexa AS (formerly eZ Systems AS). All rights reserved.

## LICENSE

This source code is available separately under the following licenses:

A - Ibexa Business Use License Agreement (Ibexa BUL),
version 2.4 or later versions (as license terms may be updated from time to time)
Ibexa BUL is granted by having a valid Ibexa DXP (formerly eZ Platform Enterprise) subscription,
as described at: https://www.ibexa.co/product
For the full Ibexa BUL license text, please see:

-   LICENSE-bul file placed in the root of this source code, or
-   https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)

AND

B - Ibexa Trial and Test License Agreement (Ibexa TTL),
version 2.2 or later versions (as license terms may be updated from time to time)
Trial can be granted by Ibexa, reach out to Ibexa AS for evaluation access: https://www.ibexa.co/about-ibexa/contact-us
For the full Ibexa TTL license text, please see:

-   LICENSE file placed in the root of this source code, or
-   https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)
