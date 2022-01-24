# Bugsnag Plugin

Provides as custom log engine for Bugsnag.

## Requirements

* CakePHP 4.x
* PHP 8.x

## Installation

_[Using [Composer](http://getcomposer.org/)]_

```
composer require maartenvr98/cakephp-bugsnag:dev-master
```

### Enable plugin

Load the plugin in your app's `src/Application` file:

```
$this->addPlugin('Bugsnag');
```

### Configuration

Configure the API-Key for Bugsnag in your app's `config/app.php` file:

```
'Bugsnag' => [
    'apiKey' => 'YOUR_API_KEY'
]
```

If you want to modify the Bugsnag notification before you send it to the their API, you can do this easily with an event listener. You only need to listen to the following event:

```
Log.Bugsnag.beforeNotify
```

How you can listen to events is detailed described in [Cake's documentation](http://book.cakephp.org/3.0/en/core-libraries/events.html#registering-listeners). This plugin comes with a ready to use Listener to provide you the best Bugsnag experience. I suggest to load this listener even if you write your own. You can configure the built in listener in your app's `config/bootstrap.php` as follows:

```
use Bugsnag\Listener\BugsnagListener;

EventManager::instance()->on(new BugsnagListener());
```
