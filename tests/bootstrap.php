<?php

use Cake\Core\Plugin;

require_once 'vendor/autoload.php';

require 'vendor/cakephp/cakephp/tests/bootstrap.php';

Plugin::load('Bugsnag', [
    'path' => dirname(dirname(__FILE__)) . DS
]);
