# Bugsnag Plugin

[![Build Status](https://travis-ci.org/steefaan/cakephp-bugsnag.svg?branch=master)](https://travis-ci.org/steefaan/cakephp-bugsnag)
[![codecov.io](https://codecov.io/github/steefaan/cakephp-bugsnag/coverage.svg?branch=master)](https://codecov.io/github/steefaan/cakephp-bugsnag?branch=master)

Provides as custom log engine for Bugsnag.

## Requirements

* CakePHP 3.x
* PHP 5.4.16 or greater

## Installation

_Using [Composer](http://getcomposer.org/)_

```
composer require steefaan/cakephp-bugsnag:^1.0
```

### Enable plugin

Load the plugin in your app's `config/bootstrap.php` file:

```
// ...

$isCli = php_sapi_name() === 'cli';
if ($isCli) {
    $handler = new ConsoleErrorHandler(Configure::read('Error'));
} else {
    $handler = new ErrorHandler(Configure::read('Error'));
}

$bugsnagFactory = new BugsnagFactory(true, Configure::read('Bugsnag'));
$bugsnagHandler = new BugsnagErrorHandler($bugsnagFactory, $handler);
$bugsnagHandler->register();

unset($bugsnagFactory, $bugsnagHandler, $handler);

// ...

Plugin::load('Steefaan/Bugsnag');
```

### Configuration

Configure the API-Key for Bugsnag in your app's `config/app.php` file:

```
'Bugsnag' => [
    'apiKey' => '%YOUR_API_KEY%',
    'filters' => [
        'CAKEPHP', 'password', 'email', 'token' // and so on...
    ],
    'notifier' => [
        'name' => 'My project',
        'version' => '1.0',
        'url' => 'https://github.com/company/my-project',
    ],
    'releaseStage' => 'production',
],
```

### Usage

Straight forward API usage for Bugsnag and a custom log engine for CakePHP makes it surprisingly easy to implement the plugin to a new or even to an existing project. As usual and described in [CakePHP's documentation](http://book.cakephp.org/3.0/en/core-libraries/logging.html#logging-configuration) you can configure the log engine as follows:

```
'Log' => [
    'bugsnag' => [
        'className' => 'Steefaan\Bugsnag\Log\Engine\BugsnagLog',
        'releaseStage' => 'staging',
        'filters' => [
            // filters as for web
        ],
        'notifier' => [
            // notifier as for web
        ],
        'apiKey' => '%YOUR_API_KEY%',
    ],
],
```

For a complete list of all available options, please refer to [Bugsnag's documentation](https://bugsnag.com/docs/notifiers/php#additional-configuration). This plugin doesn't know any limitation, you can use all configuration settings which are listed in Bugsnag's documentation. Please keep in mind that you need to remove the `set` prefix for each Bugsnag option. `setFilters` becomes `filters`, `setReleaseStage` becomes `releaseStage` and so on.

If you want to modify the Bugsnag notification before you send it to the their API, you can do this easily with an event listener. You only need to listen to the following event `Log.Bugsnag.beforeNotify`.

How you can listen to events is detailed described in [Cake's documentation](http://book.cakephp.org/3.0/en/core-libraries/events.html#registering-listeners). This plugin comes with a ready to use Listener to provide you the best Bugsnag experience. I suggest to load this listener even if you write your own. You can configure the built in listener in your app's `config/bootstrap.php` as follows:

```
use Bugsnag\Listener\BugsnagListener;

EventManager::instance()->on('Log.Bugsnag.beforeNotify', function (Event $event) {
    $report = $event->data['report'];

    // ...
};
```
