# Release Checklist for WordPress.org Repository

Use this checklist to make sure your plugin is ready for submission to the WordPress.org plugin repository.

## Required Files

- [ ] `readme.txt` - Properly formatted according to the [WordPress standards](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/)
- [ ] `readme.txt` contains all required sections and is in English
- [ ] Main plugin file (easy-restaurant-menu.php) has all required headers
- [ ] License file (LICENSE.txt) is included and is GPLv2 or later
- [ ] Screenshot files are included in the `/assets/screenshots/` directory

## Assets

- [ ] Plugin banner (772x250 px) is created and placed in `/assets/banner-772x250.png`
- [ ] High-resolution banner (1544x500 px) is created and placed in `/assets/banner-1544x500.png`
- [ ] Plugin icon (128x128 px) is created and placed in `/assets/icon-128x128.png` 
- [ ] SVG icon is created and placed in `/assets/icon.svg` (optional but recommended)

## Internationalization

- [ ] All user-facing strings are properly internationalized
- [ ] Text domain matches the plugin slug (easy-restaurant-menu)
- [ ] POT file is up-to-date with all the strings
- [ ] Italian translation files (it_IT.po and it_IT.mo) are included

## Code Quality

- [ ] Code complies with [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/)
- [ ] All PHP files have appropriate file headers with PHPDoc
- [ ] All functions have appropriate PHPDoc comments
- [ ] No PHP errors, warnings, or notices are generated
- [ ] No JavaScript errors in the console
- [ ] Code has been tested with WordPress debug mode enabled (WP_DEBUG)

## Security

- [ ] All database queries use properly prepared statements
- [ ] All forms have nonce verification
- [ ] All user inputs are properly sanitized
- [ ] All outputs are properly escaped
- [ ] Proper capability checks are in place for all admin actions
- [ ] No sensitive data is exposed in the front-end

## Functionality

- [ ] Plugin activates without errors
- [ ] Plugin deactivates without errors
- [ ] All features work as expected
- [ ] Plugin works with the latest version of WordPress
- [ ] Plugin works with the minimum required WordPress version (5.9)
- [ ] Plugin works with PHP 7.0 or higher

## Privacy

- [ ] Privacy policy suggestions are provided if the plugin collects data
- [ ] Personal data export functionality is implemented if applicable
- [ ] Personal data erasure functionality is implemented if applicable

## Repository Guidelines Compliance

- [ ] No "powered by" links are displayed on the front-end without explicit user permission
- [ ] No tracking is implemented without user consent
- [ ] No obfuscated code is used
- [ ] No external resources are loaded without user permission
- [ ] Plugin doesn't make updates from external sources
- [ ] Plugin uses WordPress default libraries (not custom versions)

## Performance

- [ ] Assets are properly enqueued (not inline)
- [ ] CSS and JS files are minified for production
- [ ] Plugin doesn't load resources on pages where they're not needed
- [ ] Caching is implemented for database-intensive operations

## Final Testing

- [ ] Plugin has been tested on a clean WordPress installation
- [ ] Plugin has been tested with popular plugins to check for conflicts
- [ ] Plugin has been tested with different themes
- [ ] Plugin has been tested on both desktop and mobile devices
- [ ] Plugin has been tested with different user roles

## Submission

- [ ] Create a ZIP file of the plugin directory
- [ ] Make sure the ZIP file name is `easy-restaurant-menu.zip`
- [ ] Create a WordPress.org account if you don't have one
- [ ] Submit the plugin at [https://wordpress.org/plugins/developers/add/](https://wordpress.org/plugins/developers/add/) 