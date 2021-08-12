## Zenegal - Notification Logs ##

**Zenegal Notification Logs** is a package to easily log and monitor email notifications sent by the platform.

### Installation ###

Install via [composer](http://getcomposer.org) in the Laravel application.

    composer require zenegal/notification-logs

### Usage ###

The package comes with a command to prune mailables from old sent notification logs.

- `notification-logs:prune`

The command defaults to 72 hours which you can modify.

- `notification-logs:prune --hours=24`
