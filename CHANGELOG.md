# Release Notes for Rentman for Craft

## 2.1.0 - 2026-10-27
### Added
* Utilities → Rentman Sync: starts the sync of categories and products from the control panel and shows "Running…" (also when coming back to the page) until the sync is done. Requires the "Rentman Sync" utility permission and `shell_exec()` on the server; the PHP CLI binary can be set with the `PHP_BINARY` environment variable
### Changed
* The `rentman-for-craft/rentman/update-all`, `update-products` and `update-categories` console commands now refuse to start while another sync is running (exit code 75), tracked in `storage/logs/rentman-sync.pid`

## 2.0.1 - 2026-07-03
### Fixed
* Fixed a `nl2br(): Argument #1 ($string) must be of type string, stdClass given` error that broke the Products element index and edit pages in the control panel when a product had custom values ([Product::getFieldLayout()] now decodes the `custom` JSON to an associative array)
* Imported values that decode to an object are now normalized to an array before rendering, as an extra safeguard
* The imported-value template now renders array values with their keys and no longer breaks on nested arrays

## 2.0.0 - 2026-06-30
* Compatible with Craft cms version 5

## 1.0.7 - 2023-12-13
### Added
* Backend translations
* Patch for Rentman days calculation
* Custom factor for sales products

### Fixed
* Backend submenu highlight fixes
* Privileges for projects and categories

## 1.0.6 - 2023-08-11
### Added
* Example templates for product / category / project pages
* JavaScript API documentation and examples

## 1.0.5 - 2023-08-10
New screenshots

## 1.0.4 - 2023-08-10
Bug fixes

## 1.0.3 - 2023-08-10
Updated documentation and minimum craft version

## 1.0.2 - 2023-08-08
Updated readme

## 1.0.1 - 2023-08-08
Typos in documentation

## 1.0.0 - 2023-08-07
Initial release
