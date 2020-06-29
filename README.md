# auto-template
A PHP library to automagically make simple PHP scripts look like real websites

```
<?php
require_once __DIR__ . '/vendor/autoload.php';
use AutoTemplate\AutoTemplate;

new AutoTemplate('My Website', [
    'Home' => '/',
    'Page 1' => 'page1.php',
    'Google' => 'http://google.com'
]);

echo "here is an <b>HTML</b> fragment";
```
![page templated with auth-template](https://i.ibb.co/V3jNdqB/i.png)

## Installing with Composer
* run
`composer require 9072997/auto-template`.
* If you don't have `require_once __DIR__ . '/vendor/autoload.php';` at the top of your scripts already, add it.

## Installing without Composer
Download [AutoTemplate.php](AutoTemplate.php) and `require` it in your script
