### core-library

[![Latest Stable Version](http://poser.pugx.org/ryzen/core-library/v)](https://packagist.org/packages/ryzen/core-library) 
[![Total Downloads](http://poser.pugx.org/ryzen/core-library/downloads)](https://packagist.org/packages/ryzen/core-library) 
[![Latest Unstable Version](http://poser.pugx.org/ryzen/core-library/v/unstable)](https://packagist.org/packages/ryzen/core-library) 
[![License](http://poser.pugx.org/ryzen/core-library/license)](https://packagist.org/packages/ryzen/core-library)

##### Super powerful, Highly optimized and Fully Secured Core PHP Library For Your PHP Web Application.

### Install

Include following block of code to your compose.json file

```json
{
    "require": {
        "ryzen/core-library": "^1.0.3"
    }
}
```

Now, run the following command in your Terminal
```
$ composer update
```

Finally, Import the auto loader and create new object with configuration
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
        
);

$site_url   = 'http://localhost/YOUR_APPLICATION_ROOT_PATH';

$corelib    =  new Ryzen\CoreLibrary\Ry_Zen($site_url,$config);
```

### Example
```php
# Example For Using Functions

$ip_address = Functions::Ry_Get_Ip_Address();

# Example For Using Auth

$user_data  = Auth::Ry_User_Data(1);

# Example For Using Session

$session    = Session::put('name','raju');

# Example For Using DatabaseQuery

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
