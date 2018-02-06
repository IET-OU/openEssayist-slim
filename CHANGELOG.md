[![Build status — Travis-CI][travis-icon]][travis]

# OpenEssayist changelog

## [Version 3.0.0](https://github.com/IET-OU/openEssayist-slim/releases/tag/3.0.0-beta)

 * Date:  ~6 February 2018 _(Development: 10 Jan - 6 Feb.)_
 * Tag:   _3.0.0-beta_  (Describe: `v2.6-108-g2ab1c9a`)
 * Notes: [Google Docs][]
 * Built on:  PHP 7.0 / Slim framework v 2.2.0 / MySQL

Below are the __headline__ items. More details are in [Google Docs][].

### PHP

 * Added _OU-SAMS_-based authentication and account creation — via separate library (Bug #27)
 * Fix PHP dependencies - upgrade from `dev-master` to released versions, in `composer.json` and `composer.lock` (Bug #20)
 * Configuration — rename `app/config.php` to `app/config.DIST.php`,
 * Configuration — make `rd_save_path`, `analyser_url` etc. configurable (Bug #23)
 * Added [Travis-CI][] continuous integration, with PHP linting [QA] (maintainability) (Bug #20)
 * Moved core _EssayAnalyser_ wrapper code to separate class (maintainability) (Bug #32)
 * Moved a lot of `require` and `Twig` configuration from front controller (`index.php`) to separate PHP files (maintainability) (Bug #31)
 * Moved database connection code from `app/config.php` to separate class (Bug #31)
 * Broke model classes into separate files (find-ability),
 * Added missing logging; improved error-handling (debugging aid, analytics),
 * Added _status_ / _uptime_ controller — shows status of backend analyser service (as well as front),
 * Eased and documented installation and setup, via [README][], Composer `scripts`.

### User-interface

 * Added configurable `maxlength` character limit to the draft-essay submission form — prevent analyser errors; server-load (Bug #30)
 * Made error messages to user more user-friendly — for example, prefixed them with "_Sorry. ..._".

### Database

 * Database schema fixes and edits - add dates/times, reduce column lengths (Bug #25)
 * Add analysis start/end time columns (Bug #25)
 * Prevent Web application from automatically creating database, tables, seeding test/example data (Bug #24)
 * New `app/cli.php` to create tables, seed with minimal/ configurable user, group and task data,
 * Configuration/ connection fixes to ensure data is sent/stored as `UTF-8` in database (Bug #26).

### Javascript

 * Javascript moved from `base.html.twig` and `site.twig` templates to separate JS files (maintainability, performance),
 * Javascript moved from some `drafts/view.*.twig` templates to separate JS files,
 * Added `semistandard` linting to [Travis-CI][] for 'openessayist' Javascripts — lots of fixes [QA]
 * Third-party Javascript/CSS versions documented in [`package.json`][pkg],
 * Transferred more key 3rd-party Javascript libraries to CDN (performance),
 * Centralised versioning of 3rd-party Javascript — via `VersionedAsset` PHP class (Bug #33)
 * Upgraded jQuery from 1.9.1 to 2.2.4 _(can't upgrade to 3.x - Bootstrap bug?)_
 * Added Google Analytics.

### Miscellaneous

 * Add missing Open University copyright statements — in PHP, Javascript, user-interface, `composer.json` (Bug #20)
 * Added _Nicolas Van Labeke_ (`vanch3d`) to `composer.json`
 * Removed legacy `composer.phar` binary from Git; removed `./openessayist.sql.zip` (2.6 MB)
 * Moved unused Javascript from Twig templates to separate JS file(s),
 * Added test data — sourced from [Project Gutenberg][pg] and [ORO][].


---
## [Version 2.7](https://github.com/IET-OU/openEssayist-slim/releases/tag/v2.7)

 * Date:   24 February 2014.
 * Tag:    _v2.7_ ("_v2.7 beta_")
 * Commit: [IET-OU/openEssayist-slim `7a3d3861`](https://github.com/IET-OU/openEssayist-slim/commit/7a3d3861e7cdb834962e82902c4d2f4e3f0d50b9)

> version 2.7 - ready for Phase II Evaluation

## [Version 2.6](https://github.com/IET-OU/openEssayist-slim/releases/tag/v2.6)

 * Date:   27 September 2013.
 * Tag:    _v2.6_
 * Commit: [IET-OU/openEssayist-slim `e6f00deb`](https://github.com/IET-OU/openEssayist-slim/commit/e6f00debfaf948d966f443cf8b0a24947e6c6f81)

> version 2.6 - ready for September evaluation

## Previous versions

* v2.4, v2.3, v2.2, v2_user_testing — 15 May to 16 August 2013.
* See:  [releases][]
* Original developer:  [vanch3d][].

← [README][]

---
© 2013-2018 [The Open University][ou]. ([Institute of Educational Technology][iet])

[ou]: http://www.open.ac.uk/
[iet]: https://iet.open.ac.uk/

[releases]: https://github.com/IET-OU/openEssayist-slim/releases
[readme]: https://github.com/IET-OU/openEssayist-slim#readme
[pkg]: https://github.com/IET-OU/openEssayist-slim/blob/3.x/package.json#L12-L38 "package.json"
[travis-ci]: https://travis-ci.org/IET-OU/openEssayist-slim "Travis-CI continuous integration"
[Google Docs]: https://docs.google.com/document/d/1n6T2zJ1FMifHGEniYyU_V2d0F8WOHWb_JPRqEWCyR2Y/# "Release notes"
[vanch3d]: https://github.com/vanch3d "Original developer: Nicolas Van Labeke (vanch3d)"
[oro]: http://oro.open.ac.uk/cgi/search/simple?meta=OpenEssayist&
[pg]: https://www.gutenberg.org/

[travis]: https://travis-ci.org/IET-OU/openEssayist-slim "IET-OU / openEsasyist-slim"
[travis-icon]: https://travis-ci.org/IET-OU/openEssayist-slim.svg
