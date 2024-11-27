# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.8.1](https://github.com/hackzilla/TicketBundle/compare/3.8.0...3.8.1) - 2022-01-04

* allow PHP8
* minor #151 Vich TicketMessageWithAttachment.xml renamed to Entity.TicketMessageWithAttachment.xml

## [3.8.0](https://github.com/hackzilla/TicketBundle/compare/3.7.0...3.8.0) - 2021-02-25

 * minor #203 Added XML routing (phansys)
 * pedantic #202 Use configuration from PHP instead of YAML (phansys)
 * bugfix #201 Fix argument 1 passed to `UserLoad::__construct()` (phansys)
 * minor #200 Add `UserManager::findUserByUsername()` (phansys)
 * minor #199 Update URLs in status badges (phansys)
 * minor #198 Modernize routing definition (phansys)
 * minor #196 Update dependency constraints in order to allow some major versions (phansys)
 * minor #197 Stop suggesting "friendsofsymfony/user-bundle" (phansys)

## [3.7.0](https://github.com/hackzilla/TicketBundle/compare/3.6.0...3.7.0) - 2021-02-17

 * minor #193 Add support for "doctrine/persistence:^2.1" (phansys)
 * minor #194 Running tests from GitHub Actions (phansys)

## [3.6.0](https://github.com/hackzilla/TicketBundle/compare/3.5.0...3.6.0) - 2020-10-29

 * minor #178 Bump dependencies (phansys)
 * tests #182 Remove `serialize()` and `unserialize()` methods from `TestKernel` (phansys)
 * minor #180 Deprecate return other type than boolean from `TicketFeatures::hasFeature()` (phansys)
 * minor #181 Use `::class` pseudo constants in `Configuration::getConfigTreeBuilder()` (phansys)
 * tests #179 Mark `FunctionalTest::testConfiguredFileUploadSubscriber()` as skipped if "vich/uploader-bundle" is not available (phansys)

## [3.5.0](https://github.com/hackzilla/TicketBundle/compare/3.4.0...3.5.0) - 2020-08-26

 * minor #175 Add configuration for Dependabot (phansys)
 * bugfix #176 Explicitly require "doctrine/common" (phansys)
 * minor #172 Update `README.md` (phansys)
 * minor #170 Update coding standard rules (phansys)
 * minor #169 Update "Version Matrix" section at `README.md` (phansys)
 * minor #168 Add status badges for GitHub actions (phansys)
 * minor #166 Add "friendsofsymfony/user-bundle" to development dependencies (phansys)
 * minor #165 Narrow API marking classes as final (phansys)
 * minor #164 Lint "*.xlf" files (phansys)
 * minor #163 [#150] add Dutch translation (bghosts)
 * minor #162 Fix some PHP CS rules (phansys)
 * minor #161 Configure SCA through GH actions (phansys)

## [3.4.0](https://github.com/hackzilla/TicketBundle/compare/3.3.1...3.4.0) - 2020-07-03

 * bugfix #160 Fix bad call at `TicketManager::getTicketList()` (phansys)
 * minor #158 Deprecate "messages" translation domain in favor of "HackzillaTicketBundle" (backport PR #123) (phansys)

## [3.3.1](https://github.com/hackzilla/TicketBundle/compare/3.3.0...3.3.1) - 2020-07-01

 * bugfix #159 Fix references to "hackzilla_ticket.templates.*" parameters (phansys)

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
