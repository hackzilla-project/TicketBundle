# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.3.0](https://github.com/hackzilla/TicketBundle/compare/3.2.0...3.3.0) - 2020-07-01

 * minor Deprecate `TicketManager::getTicketList()` (backported from PR #127) (phansys)
 * minor #155 Allow to install "knplabs/knp-paginator-bundle:^5.0" (phansys)
 * bugfix #156 Explicitly require "twig/twig" since it is used by the Twig ticket extensions (phansys)
 * minor #154 Update CI configuration (phansys)
 * minor #153 Allow "doctrine/doctrine-bundle:^2.0" (phansys)
 * bugfix #152 Update constraint for "phpunit/phpunit" (phansys)
 * patch #148 Update some calls to deprecated test methods (phansys)
 * patch #147 Add tests for templates load (phansys)
 * patch #145 Use Twig namespaced paths (phansys)
 * bugfix #146 Replace "twig/twig" requirement with "symfony/twig-bundle" (phansys)
 * bugfix #137 Fix template attributes forbidden (, masonen)
 * minor #144 Add explicit dependency against "twig/twig" (phansys)
 * bugfix #142 Remove "syntaxCheck" config for PHPUnit (phansys)
 * minor #139 Add support for "knplabs/knp-paginator-bundle:^4.0" (phansys)
 * minor #140 Exclude test files from "autoload" section (phansys)
 * minor #132 Allow "knplabs/knp-paginator-bundle:^3.0" (phansys)
 * minor #131 Remove some deprecations (phansys)
 * merge #130 Update dependency constraint for "symfony/framework-bundle" (phansys)
 * bugfix #126 [dependencies] Provide "translation" service via "symfony/translation" instead of "sensio/framework-extra-bundle" (phansys)
