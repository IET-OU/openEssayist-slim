
[![Build status — Travis-CI][travis-icon]][travis]
[![changelog][release-icon]][changelog]
[![js-semistandard-style][semi-icon]][semi]
[![Accessibility testing][pa11y-icon]][pa11y-ci]

# openEssayist-slim

Web frontend to the essay analyser and summariser — [pyEssayAnalyser][py].

Part of the [SafeSEA][] project.
(Grants: _[Open University][ou-grant], [Oxford University][ox-grant]_.)
(_Research [papers on ORO][oro-ss]. [YouTube demo][yt-demo]_)

Built on [Slim PHP framework][slim].

* GitHub: [SAFeSEA/openEssayist-slim][gh]
* GitHub: [IET-OU/openEssayist-slim][gh-iet]
* GitHub: [SAFeSEA/pyEssayAnalyser][gh-py]

## Authentication

Newer OpenEssayist servers can optionally use [OU-SAMS][] authentication,
with a pre-defined list of [admin OUCUs][admin].

## Release notes

* See: [CHANGELOG][]

## Redhat preparation

```sh
composer redhat-check
composer redhat-install   # yum install php php-fpm php-mcrypt php-zip httpd mod_fcgid
# cp -n app/_data/php.fastcgi /var/www/cgi-bin/
#   ...
```

## Install .. Test

```sh
composer prepare    # Edit: 'app/config.php'
                    # OU only - edit: '.env'
composer install
composer npm-install
composer copy-nginx  # Only on Redhat 7.
composer chown       # Only on Redhat 7.

composer build
composer test
```

## Database

To create and seed the [MySQL database tables][db], start typing:

```sh
composer cli-help
app/cli.php --create-tables
```

## Auto-assign student to group

Edit the [groups][] PHP `object` (`array`) in the `app/config.php` configuration file, then run `app/cli.php`:

```sh
sudo vi app/config.php +78
sudo app/cli.php --seed-groups
```

The student then uses a link of this form, to add themselves to the group on their first visit:

```
https://my-openessayist.open.ac.uk/samslogin?group={NAME}
```

---

> To check:

* https://github.com/silentworks/sharemyideas
* https://github.com/Tieno/SlimPackage
* https://github.com/codeguy/Slim-Extras
* [Using phpactiverecord with slim..](http://silentworks.co.uk/blog/development/using-phpactiverecord-with-slim-framework.html)

---

* Original developer: [vanch3d][].

## GDPR

Details of GDPR / privacy fixes can be found in [Bug #1][] (Was [Bug #38][]).

---
[©][c] 2013-2019 [The Open University][ou]. ([Institute of Educational Technology][iet])

[c]: https://www.open.ac.uk/copyright "Copyright © 2013-2019 The Open University (IET). All rights reserved."
[ou]: https://www.open.ac.uk/
[iet]: https://iet.open.ac.uk/

[vanch3d]: https://github.com/vanch3d "Original developer: Nicolas Van Labeke (vanch3d)"
[changelog]: https://github.com/IET-OU/openEssayist-slim/blob/3.x/CHANGELOG.md

[py]: https://github.com/SAFeSEA/pyEssayAnalyser
[gh]: https://github.com/SAFeSEA/openEssayist-slim "Original"
[gh-iet]: https://github.com/IET-OU/openEssayist-slim "Fork"
[gh-py]: https://github.com/SAFeSEA/pyEssayAnalyser "Python"
[travis]: https://travis-ci.org/IET-OU/openEssayist-slim "IET-OU / openEsasyist-slim"
[travis-icon]: https://travis-ci.org/IET-OU/openEssayist-slim.svg
[travis-ss]:  https://travis-ci.org/SAFeSEA/openEssayist-slim "SafeSEA / openEssayist-slim"
[travis-ss-icon]: https://api.travis-ci.org/SAFeSEA/openEssayist-slim.svg
    "Build status – Travis-CI (PHP)"
[semi]: https://github.com/Flet/semistandard
[semi-icon]: https://img.shields.io/badge/code_style-semistandard-brightgreen.svg?_style=flat-square
    "Javascript coding style — 'semistandard'"
[pa11y-ci]: https://github.com/pa11y/pa11y-ci
    "Automated accessibility testing - via 'pa11y-ci' (work-in-progress)"
[pa11y-icon]: https://img.shields.io/badge/accessibility-pa11y--ci-blue.svg
[release-icon]: https://img.shields.io/github/release/IET-OU/openEssayist-slim.svg "Changelog: latest GitHub release"
[slim]: https://docs.slimframework.com/ "Slim PHP Framework v2"
[db]: https://github.com/IET-OU/openEssayist-slim/blob/3.x/app/_data/openessayist-schema.sql#L24 "SQL database schema"
[ou-sams]: https://github.com/IET-OU/openEssayist-slim/blob/3.x/composer.json#L45 "Composer: use the 'iet-ou/sams-c-auth' library"
[admin]: https://github.com/IET-OU/openEssayist-slim/blob/3.x/config/app.DIST.php#L62 "Configuration: 'admin_oucu_list' => [ .. ]"
[groups]: https://github.com/IET-OU/openEssayist-slim/blob/3.x/config/app.DIST.php#L82 "Configuration: 'groups' => [ .. ]"

[safesea]: https://www.open.ac.uk/researchprojects/safesea/
  "Supportive Automated Feedback for Short Essay Answers (SAFeSEA)."
[yt-demo]: https://youtu.be/7a3ATQPjpiM# "openEssayist Software Tool Demonstration, @ietou on YouTube"
[yt-intro]: https://youtu.be/a9l0ts1tgK4# "Introduction to openEssayist - Professor Denise Whitelock, @ietou on YouTube"
[oro-ss]: https://oro.open.ac.uk/cgi/search/archive/advanced?project_details_project_name=SafeSEA "'SafeSEA' on ORO (10 results)"
[oro-oe]: https://oro.open.ac.uk/cgi/search/archive/simple?meta=OpenEssayist& "'OpenEssayist' on ORO (8 results)"
[ou-grant]: http://gow.epsrc.ac.uk/NGBOViewGrant.aspx?GrantRef=EP/J005959/1
  "Supportive Automated Feedback for Short Essay Answers (SAFeSEA) (Open University, 2012-2014) [EPSRC grant: EP/J005959/1]"
[ox-grant]: http://gow.epsrc.ac.uk/NGBOViewGrant.aspx?GrantRef=EP/J005231/1
  "Supportive Automated Feedback for Short Essay Answers (SAFeSEA) (Oxford University, 2012-2014) [EPSRC grant: EP/J005231/1]"

[Bug #38]: https://github.com/SAFeSEA/openEssayist-slim/issues/38 "GDPR/privacy (SafeSEA)"
[bug #1]: https://github.com/IET-OU/openEssayist-slim/issues/1 "GDPR/privacy"

[End]: //.
