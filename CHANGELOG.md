# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD
### Fixed
- Docker build error related to `zlib1g` Linux package.
- Lockfile not having dependencies at required version.
- Wrong path to languages JSON file (#4).

### Added
- The `wordpress` Docker service now has WP-CLI.
- Plugin header `Network`, which prevents the plugin from being activated on site level (#6).

## [0.1.0-alpha2] - 2019-12-27
### Fixed
- Wrong path to languages JSON file (#4).

## [0.1.0-alpha1] - 2019-06-03
Initial release.
