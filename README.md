# Bugsnag Plugin

Provides as custom log engine for Bugsnag.

## Requirements

* CakePHP 4.x
* PHP 7.4 or greater

## Installation

_[Using [Composer](http://getcomposer.org/)]_

```
composer require maartenvr98/cakephp-bugsnag:dev-master
```

### Enable plugin

Load the plugin in your app's `config/bootstrap.php` file:

```
Plugin::load('Bugsnag');
```

### Configuration

Configure the API-Key for Bugsnag in your app's `config/app.php` file:

```
'Bugsnag' => [
    'apiKey' => 'YOUR_API_KEY'
]
```

### Usage

Straight forward API usage for Bugsnag and a custom log engine for CakePHP makes it surprisingly easy to implement the plugin to a new or even to an existing project. As usual and described in [CakePHP's documentation](http://book.cakephp.org/3.0/en/core-libraries/logging.html#logging-configuration) you can configure the log engine as follows:

```
'Log' => [
    'bugsnag' => [
        'className' => 'Bugsnag\Log\Engine\BugsnagLog',
        'releaseStage' => 'development',
        'filters' => [
            'password'
        ]
        ... more options
    ]
],
```

For a complete list of all available options, please refer to [Bugsnag's documentation](https://bugsnag.com/docs/notifiers/php#additional-configuration). This plugin doesn't know any limitation, you can use all configuration settings which are listed in Bugsnag's documentation. Please keep in mind that you need to remove the `set` prefix for each Bugsnag option. `setFilters` becomes `filters`, `setReleaseStage` becomes `releaseStage` and so on.

If you want to modify the Bugsnag notification before you send it to the their API, you can do this easily with an event listener. You only need to listen to the following event:

```
Log.Bugsnag.beforeNotify
```

How you can listen to events is detailed described in [Cake's documentation](http://book.cakephp.org/3.0/en/core-libraries/events.html#registering-listeners). This plugin comes with a ready to use Listener to provide you the best Bugsnag experience. I suggest to load this listener even if you write your own. You can configure the built in listener in your app's `config/bootstrap.php` as follows:

```
use Bugsnag\Listener\BugsnagListener;

EventManager::instance()->on(new BugsnagListener());
```
