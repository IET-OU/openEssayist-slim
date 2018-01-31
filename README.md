
[![Build status — Travis-CI][travis-icon]][travis]

# openEssayist-slim

Web frontend to the essay analyser and summariser — [pyEssayAnalyser][py].

Part of the [SafeSEA][] project.
Grants: [Open University][ou-grant], [Oxford University][ox-grant].

Built on [Slim][].

* GitHub: [SAFeSEA/openEssayist-slim][gh]
* GitHub: [IET-OU/openEssayist-slim][gh-iet]
* GitHub: [SAFeSEA/pyEssayAnalyser][gh-py]

## Redhat preparation

```sh
composer redhat-check
composer redhat-install
```

## Install .. Test

```sh
composer copy-dotenv # OU only (edit .env)

composer install
composer copy-conf   # app/config.php
composer copy-nginx  # Only on Redhat
composer mkdir
composer chown       # Only on Redhat 7.
composer version.json

npm run link
composer test
```

To check:

* https://github.com/silentworks/sharemyideas
* https://github.com/Tieno/SlimPackage
* https://github.com/codeguy/Slim-Extras
* http://silentworks.co.uk/blog/development/using-phpactiverecord-with-slim-framework.html


---
© 2013-2018 [The Open University][ou]. ([Institute of Educational Technology][iet])

[ou]: http://www.open.ac.uk/
[iet]: https://iet.open.ac.uk/

[py]: https://github.com/SAFeSEA/pyEssayAnalyser
[gh]: https://github.com/SAFeSEA/openEssayist-slim "Original"
[gh-iet]: https://github.com/IET-OU/openEssayist-slim "Fork"
[gh-py]: https://github.com/SAFeSEA/pyEssayAnalyser "Python"
[travis]: https://travis-ci.org/IET-OU/openEssayist-slim "IET-OU / openEsasyist-slim"
[travis-icon]: https://travis-ci.org/IET-OU/openEssayist-slim.svg
[travis-ss]:  https://travis-ci.org/SAFeSEA/openEssayist-slim "SafeSEA / openEssayist-slim"
[travis-ss-icon]: https://api.travis-ci.org/SAFeSEA/openEssayist-slim.svg
    "Build status – Travis-CI (PHP)"
[slim]: https://docs.slimframework.com/ "Slim Framework v2 (PHP)"

[safesea]: http://www.open.ac.uk/researchprojects/safesea/
  "Supportive Automated Feedback for Short Essay Answers (SAFeSEA)."
[ou-grant]: http://gow.epsrc.ac.uk/NGBOViewGrant.aspx?GrantRef=EP/J005959/1
  "Supportive Automated Feedback for Short Essay Answers (SAFeSEA) (Open University, 2012-2014) [EPSRC grant: EP/J005959/1]"
[ox-grant]: http://gow.epsrc.ac.uk/NGBOViewGrant.aspx?GrantRef=EP/J005231/1
  "Supportive Automated Feedback for Short Essay Answers (SAFeSEA) (Oxford University, 2012-2014) [EPSRC grant: EP/J005231/1]"

[End]: //.
