# auto-template
A PHP library to automagically make simple PHP scripts look like real websites

```
<?php
require_once __DIR__ . '/vendor/autoload.php';

startAutoTemplate('My Website', [
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
Download [auto-template.php](auto-template.php) and `require` it in your script

## Using
call `startAutoTemplate($title, $menuItems)` at the top of your script. `$title` is a string that will be displayed at the top of the page. `$menuItems` can either be an associative array of navigation link titles to links, or it can be a function that returns an array. The function option can be useful for context-aware navigation logic (like login/logout links) that needs to be evaluated after your script.

Optionally, stick the call to `startAutoTemplate()` and the associated navigation logic it it's own file and require that.
