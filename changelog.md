# Changelog
All notable changes to this project will be documented in this file.

## [1.2.3] - 2021-05-10

### Updated
- Update video play route and also added live_stream information on channels

## [1.2.2] - 2020-09-21

### Added
- Added homepage API

## [1.2.1] - 2020-09-15

### Added
- Added bypass_channel_lock flag for the video when we import the content or update the content using REST API.

## [1.2.0] - 2020-09-08

### Added
- Added request signature verificatons to webhook receiver class

## [1.1.14] - 2020-09-03

### Added
- Added new function on request

## [1.1.13] - 2020-09-03

### Added
- Added a new channel wallpaper

## [1.1.12] - 2020-07-21

### Fixed
- Fixed issue related to the wordpress timezone while creating the channel or categories

## [1.1.11] - 2020-07-14

### Added
- Added Youbora analytics functionality as well as a call to get company analytics info from the Spotlight API

## [1.1.10] - 2020-07-13

### Added
- Added a new field called custom fields and display title for category section when we import the content or update the content using REST API.

## [1.1.9] - 2019-12-17

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
