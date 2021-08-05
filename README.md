### core-library

[![Latest Stable Version](http://poser.pugx.org/ryzen/core-library/v)](https://packagist.org/packages/ryzen/core-library) 
[![Total Downloads](http://poser.pugx.org/ryzen/core-library/downloads)](https://packagist.org/packages/ryzen/core-library) 
[![Latest Unstable Version](http://poser.pugx.org/ryzen/core-library/v/unstable)](https://packagist.org/packages/ryzen/core-library) 
[![License](http://poser.pugx.org/ryzen/core-library/license)](https://packagist.org/packages/ryzen/core-library)

##### Super powerful, Highly optimized and Fully Secured Core PHP Library For Your PHP Web Application.

### Install

Run the below command in your terminal
```
$ composer require ryzen/core-library
```

Finally, Import the autoloader and create new object with configuration
```php
<?php

include 'vendor/autoload.php';

$config = array(

    'mysql_database_config' =>
        [
            'db_host'       => "YOUR_DATABASE_HOST",
            'db_name'       => "YOUR_DATABASE_NAME",
            'db_user'       => "YOUR_DATABASE_USER",
            'db_pass'       => "YOUR_DATABASE_PASS",
            'db_driver'     => 'mysql',
            'db_charset'    => 'utf8',
            'db_collation'  => 'utf8_general_ci',
            'db_prefix'     => ''
        ],
    'encryption_method' =>
        [
            'encryptionMethod'  =>'AES-128-CBC',
            'password'          =>'YOUR_SECRET_ENCRYPTION_KEY'
        ],
    'default_tables' =>
        [
            'users'             =>'users',
            'users_sessions'    =>'users_sessions',
        ],
    'auth' =>
        [
            'password_hashing_method' => 'bcrypt',
            'rehash' => false,
        ],
        
);

$site_url   = 'http://localhost/YOUR_APPLICATION_ROOT_PATH';

$corelib    =  new Ryzen\CoreLibrary\Ry_Zen($site_url,$config);
```

##### Readme only gives you an idea on how to use, refer to doc for entire list of functions.<a href="https://docs.8beez.com/corelib">Core-Library Documentation</a>

### View
```php
# Loading View

View::load('fileName');

# To cache the view pass true as second parameter ('fileName', true);
# Default filename extension is .php;

# Clearing View Cache

View::cleanCache('fileName');
```
##### View Customization
```php
# Changing View Extension 

Ry_Zen::$main->viewExtension = 'html' OR 'php' OR 'phtml';

# Changing Default UI path

Ry_Zen::$main->theme_url = 'Your path'; # Default is './resources/view/';
```
### File System
```php
# Creates new directory if not exists

FileSystem::checkCreateDir('uploads');
```
### Lazy
#### Lazy Backup
```php
# Backup all your project files and folder into zip

Lazy::backMeUp();
```
#### Lazy Migrate
```php
# Automatically creates users and user session table for you

Lazy::migrateDefaults();

# If your table uses prefixes pass it through parameter else leave empty
# Example -> Lazy::migrateDefaults('ry'); [ OUTPUT (ry_users),(ry_users_sessions) ]
```
### Cache
```php
# Opens up cache directory

Cache::openCacheDirectory();
```
### Functions
```php
# Example

Functions::safe_string();
```
### Auth
```php
# Example

Auth::check();
```

### Cookie
```php
# Example

Cookie::get();
```

### Session
```php
# Example

Session::put();
```

### Hashing
```php
# Example

Hash::make('SomethingStrongPassword');
```

### Generate
```php
# Example

# Generates Random Password ( accepts length as Parameter Default is 8 )
Generate::password();

# Generates Random Key ( accepts length as parameter Default is 32 )
Generate::key();

# Generates Random Token ( accepts length as parameter Default is 22)
Generate::token();
```

### Database Builder
```php
# Inbuilt
$corelib->dbbuilder->table('users')->getAll();

# Example For Using DatabaseQuery PDO

$statement  = $corelib->dbbuilder->pdo->prepare('SELECT * FROM users');
$statement->execute();
```

### Documentation
<a href="https://docs.8beez.com/corelib">Core-Library Docs</a>

### Support Center
<a href="https://docs.8beez.com/support">Ry-Zen official Support</a>

### Contributors
<a href="https://rajuchoudhary.com.np/"> Raju Choudhary </a> 😎 - Creator and Maintainer
