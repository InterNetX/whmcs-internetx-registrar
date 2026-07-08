# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repo is

A WHMCS Registrar Module for InterNetX's DomainRobot/AutoDNS JSON API. The actual module lives entirely in `src/`; `dist/` is **generated output**, never edit it directly (see "Build process" below).

`src/` contains three WHMCS module types that ship together:
- `src/install/modules/registrars/InterNetXJson/` — the registrar module (domain register/transfer/renew/DNS/contacts/etc.), the core of this project.
- `src/install/modules/addons/InterNetXJson/` — a companion addon module for selling premium domains.
- `src/install/modules/servers/InterNetX_SSL/` — an SSL product module.
- `src/install/lang/`, `src/install/resources/`, `src/install/templates/` — WHMCS-side language overrides, TLD legal-document resources, and template overrides that need to be copied into a WHMCS installation alongside the modules (see `src/INSTALL.md`).

The placeholder name `InterNetXJson` (and lowercase `internetxjson`) is intentional — it's what `makeModule.sh` rewrites into each white-labeled registrar name (`InterNetX`, `DomainRobot`, `SchlundTech`) when producing `dist/`.

## Build process (src → dist)

`dist/` is committed but is **build output**, produced by CI, not hand-edited. Always make source changes in `src/`.

- `./makeModule.sh <RegistrarName> <RegistrarUrl> <RegistrarNS>` — builds one white-labeled copy of `src/` into `dist/<RegistrarName>`, renaming the `InterNetXJson` placeholder throughout, swapping the nameserver domain and version string, stripping tests/dev files, and zipping the result. Requires `gnu-sed` (`brew install gnu-sed`) on macOS.
- `./makeAllModules.sh` — runs `makeModule.sh` for all three registrar brands (`InterNetX`/www.internetx.com/ns14.net, `DomainRobot`/www.domain-robot.org/ns14.net, `SchlundTech`/www.schlundtech.com/schlundtech.de).
- CI (`.github/workflows/main.yml`) runs `makeAllModules.sh` on every push to `main` and commits the regenerated `dist/` back to the repo automatically — don't hand-edit `dist/` since CI will overwrite it, and don't manually commit to `dist/` in normal PRs.
- The module version is a single line in `VERSION` at the repo root (currently `1.1.0-preview`), substituted into `Config.php`'s `DEVELOPMENT-VERSION` placeholder during the build.

## Working in the registrar module

Path: `src/install/modules/registrars/InterNetXJson/`

- `InterNetXJson.php` — the WHMCS entry point. Every WHMCS-called function (`InterNetXJson_RegisterDomain`, `InterNetXJson_Sync`, etc.) is a thin wrapper delegating to `App\RegistrarModule::call($functionName, $params)`. Add new WHMCS hook functions here as one-line delegations, never inline logic.
- `app/RegistrarModule.php` — the actual implementation of every registrar action, implementing `App\Contracts\RegistrarModuleInterface`. `RegistrarModule::call()` is the single entry point that all WHMCS functions funnel through; it centralizes exception handling (`RegistrarModuleException` and `DomainrobotException` are caught and converted to WHMCS-style `['error' => ...]` responses) and optional request/response logging (gated by the `debug` / `debugallcalls` params and `LOGTHESEFUNCTIONCALLS`/`LOGGERREPLACEVARS` constants, which redact credential fields from logs).
- `app/Helpers/WhmcsBridge.php` — pure translation functions between WHMCS's flat `$params` array format and the DomainRobot SDK's model objects (contacts, nameservers, zones). This is the most heavily unit-tested file and the best place to look for expected `$params` shapes.
- `app/Helpers/DomainRobotExtension.php` — additional DomainRobot-API-specific helpers/extensions beyond the raw SDK.
- `app/Helpers/Config.php` — static module config (module name, nameserver root, version, user agent). `registrarNameserver()` honors the `DNS_NS_ROOT` env var for dev/testing.
- `app/Providers/WhmcsModuleLogProvider.php` — writes to WHMCS's module log.
- `app/Providers/WhoisProxyProvider.php` — whois lookups.
- `app/templates/zone.json` (and `zone_standalone.json`, `zone.example.json`) — DNS zone templates applied to newly created domains; supports `{{config_ip}}`, `{{config_mx}}`, `{{domain}}`, `{{nameservers}}` placeholders (documented in `src/UPGRADE.md`).
- `vendor/internetx/php-domainrobot-sdk` — the vendored DomainRobot API SDK; `RegistrarModule.php` is a consumer of this SDK's `Domainrobot`, `Model\*`, and `Lib\*` classes.

Dev-mode env vars (see root `README.md`):
- `DOMAINROBOT_INSECURE=true` — use insecure curl / ignore invalid SSL certs.
- `DNS_NS_ROOT=demo.autodns2.de` — override the nameserver root used to detect if a domain uses "our" nameservers vs. foreign ones.

## Tests

Tests live under `src/install/modules/registrars/InterNetXJson/tests/` (`unit/` and `functional/`) and are stripped out of `dist/` builds by `makeModule.sh`.

```bash
cd src/install/modules/registrars/InterNetXJson
composer install         # installs phpunit and the SDK dependency
./test.sh                # runs ./vendor/bin/phpunit --testdox tests
./vendor/bin/phpunit --testdox tests/unit/app/Helpers/WhmcsBridgeTest.php   # single file
./vendor/bin/phpunit --filter testParseDomainReturnCorrectValue            # single test
```

- `tests/unit/` — pure PHP unit tests (e.g. `WhmcsBridgeTest.php`, `DomainRobotExtensionTest.php`) that don't hit the network; safe to run anytime.
- `tests/functional/` — call `App\RegistrarModule::call(...)` end-to-end with WHMCS-shaped `$params` arrays, using `sandboxMode` and hardcoded sandbox credentials (`sandboxServerUsername`/`sandboxServerPassword`) against InterNetX's live demo/sandbox API (`demo.autodns2.de`). These require network access to InterNetX's sandbox and are slower/less deterministic than unit tests — prefer unit tests for logic changes, and treat functional test failures as possibly environmental (sandbox state, network) rather than always a code regression.
- No `phpunit.xml` config file exists; discovery relies on pointing phpunit directly at the `tests` directory.

## Notes

- WHMCS module function names are case-sensitive and must match exactly what WHMCS calls (e.g. `RegisterDomain`, `GetTldPricing`); `RegistrarModuleInterface` documents the required set, with optional hooks (`AdminCustomButtonArray`, `TransferOutAck`/`TransferOutNack`, `ClientAreaCustomButtonArray`, `DomainForwarding`, `ChangeOwner`) listed in a comment below it.
- Credential-like param keys are centralized in `RegistrarModule::LOGGERREPLACEVARS` — if you add a new sensitive param, add it there so it gets redacted from debug logs.
- See `src/INSTALL.md` for what gets copied where in a real WHMCS installation, and `src/UPGRADE.md` for migration notes/breaking changes between module versions (useful context when changing zone templates, MX handling, or transfer sync behavior).
