# Notice administration for Silverstripe websites

Simple, unopinionated, module to store notices for use by a project.

## Options

A notice contains:

+ A title
+ Some content (not HTML)
+ A link
+ A dismissible option, with auto dismiss value
+ A site-wide option (one allowed)
+ An active option (enabled)

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

+ [dpcdigital@NSWDPC:~$](https://dpc.nsw.gov.au)

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
