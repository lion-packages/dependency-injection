# 🦁 Lion-DependencyInjection

<p align="center">
  <a href="https://dev.lion-packages.com/docs/library/content" target="_blank">
    <img 
        src="https://github.com/lion-packages/framework/assets/56183278/60871c9f-1c93-4481-8c1e-d70282b33254"
        width="450" 
        alt="Lion-Packages Logo"
    >
  </a>
</p>

<p align="center">
  <a href="https://packagist.org/packages/lion/dependency-injection">
    <img src="https://poser.pugx.org/lion/dependency-injection/v" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/lion/dependency-injection">
    <img src="https://poser.pugx.org/lion/dependency-injection/downloads" alt="Total Downloads">
  </a>
  <a href="https://github.com/lion-packages/dependency-injection/blob/main/LICENSE">
    <img src="https://poser.pugx.org/lion/dependency-injection/license" alt="License">
  </a>
  <a href="https://www.php.net/">
    <img src="https://poser.pugx.org/lion/dependency-injection/require/php" alt="PHP Version Require">
  </a>
</p>

🚀 **Lion-DependencyInjection** Container for dependency injection with DI-PHP. 

---

## 📖 Features

✔️ Resolves a class or dependency from the container.   
✔️ Calls a method on an object with automatic dependency injection.   
✔️ Executes a callback with automatic dependency injection.   

---

## 📦 Installation

Install the dependency-injection using **Composer**:

```bash
composer require lion/dependency-injection
```

## Usage Example #1

```php
<?php 
declare(strict_types=1);

require_once('./vendor/autoload.php');

use App\Http\Controllers\UsersController;
use Lion\Dependency\Injection\Container;

$container = new Container();

/** @var UsersController $usersController */
$usersController = $container->resolve(UsersController::class);

$response = $container->callMethod($usersController, 'createUsers');

var_dump($response);
```

## Usage Example #2

```php
<?php

declare(strict_types=1);

require_once('./vendor/autoload.php');

use App\Http\Controllers\UsersController;
use Lion\Dependency\Injection\Container;

$response = (new Container())
    ->callCallback(function (UsersController $usersController) {
        return $usersController->createUsers();
    });

var_dump($response);
```
## 📝 License

The <strong>dependency-injection</strong> is open-sourced software licensed under the [MIT License](https://github.com/lion-packages/dependency-injection/blob/main/LICENSE).
