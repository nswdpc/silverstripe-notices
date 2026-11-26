# Notice administration for Silverstripe websites

Simple, unopinionated, module to store notices for use by a project.

## Options

A notice contains:

+ A title, with 'show title' option
+ Some content (not HTML)
+ A link
+ A auto dismiss value (seconds)
+ A site-wide option (one allowed)
+ An active option (enabled)

## Link migration

This version includes [staged migration of links](https://github.com/nswdpc/silverstripe-linkfield/blob/feat-link-migrator-staged/docs/en/migration.md) to the core Silverstripe Link model.

1. *Important*: Update all calls to `$Link`/`getLink()`/`Link()` in your project  to `$DerivedLink`/`getDerivedLink()`/`DerivedLink()`
2. Once the above change is available on your site, in the "Link migration" admin section any existing links will show with a  "Migrate" button
3. Hit the "Migrate" button
4. Records will migrate to the core Silverstripe Link model and the element interface will display the core Silverstripe link field once the element is republished.
5. Template usage on site frontend will pick up the new, core, link.

The next major release of this module will remove dependency of `nswdpc/silverstripe-linkfield` and legacy linkfield implementation.

## Permissions

The module adds permissions for view/add/edit/delete

## Template

The module ships a basic `meta` template, nominally for testing purposes.

Override this template in your project to provide your own notice layout based on the notice options.

## Installation

```sh
composer require nswdpc/silverstripe-notices
```

## License

[BSD-3-Clause](./LICENSE.md)

## Documentation

Once installed, a "Notices" admin link will be available.


## Configuration

None

## Maintainers

+ PD Web Team

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
