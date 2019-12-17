# Changelog
All notable changes to this project will be documented in this file.

# [1.1.9] - 2019-12-17

### Fixed
- Fixed one issue for manage the database flag variables for import status.

## [1.1.8] - 2019-11-15

### Fixed
- Fixed one issue for the old plugin users to make the demo content flag false if API key is exists.

## [1.1.7] - 2019-11-15

### Added
- Added a feature to import sample content for the channel and categories if the user have not their own API key.
- Added new webhook to update all the channels by page count when the request is comes form an API Routes or Cron.

## [1.1.6] - 2019-11-07

### Fixed
- Fixed an issue with the category update.

## [1.1.5] - 2019-10-18

### Added
- Added functionality to add new hash-key while importing the category so the import process can come to know if the category is unpublished in next import process run. Also Fixed minor bugs.

## [1.1.4] - 2019-9-30

### Added
- Added functionality to add new hash-key while importing the channels so the import process can come to know if the channel is unpublished in next import process run

## [1.1.2] - 2019-8-8

### Added
- Added functionality to delete transients for content related to channels/categories when ingesting webhooks

## [1.1.1] - 2019-4-2

### Fixed
- Fixed an issue with the webhook receiver for channels that was causing channel logos to be saved incorrectly.

## [1.1.0] - 2019-3-18

### Fixed
- Fixed an issue with the channel update where the REST API schema didn't have an `items` array

### Updated
- Various minor updates to functionality
