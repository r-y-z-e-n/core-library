### core-library

[![Latest Stable Version](http://poser.pugx.org/ryzen/core-library/v)](https://packagist.org/packages/ryzen/core-library) 
[![Total Downloads](http://poser.pugx.org/ryzen/core-library/downloads)](https://packagist.org/packages/ryzen/core-library) 
[![Latest Unstable Version](http://poser.pugx.org/ryzen/core-library/v/unstable)](https://packagist.org/packages/ryzen/core-library) 
[![License](http://poser.pugx.org/ryzen/core-library/license)](https://packagist.org/packages/ryzen/core-library)

##### Super powerful, Highly optimized and Fully Secured Core PHP Library For Your PHP Web Application.

### Install

Using Core Library now Requires .env File as its configuration listing

Quick Install  <a href="https://github.com/r-y-z-e-n/lazyphp">Lazy PHP Documentation</a>

```
$ composer create-project ryzen/lazyphp my-project
```

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

### Validation
```php
# structure

Validator::make([ 'INPUT_FIELD_NAME' => 'RULES' ]);

# Making new Validation
Validator::make([
    'first_name'    =>  'required|min|5|max|10',
    'user_email'    =>  'required|unique|np_users',
])

# Getting Validation Error
Validator::error(); // Returns Set of errors in array;
Validator::error('first_name'); // returns error of input firs_name;

# Checking Validation Passed or Failed
Validator::passed();
Validator::failed();

# Changing default validation message
# {key} -> Defines input field name
# {val} -> Defines values of test case in rule max|15, here 15 is val
# {matching.key} -> Defines input field to match with

ValidationMessage::$required = '{key} YOUR TEXT';
```

#### Validation RULES
```php
-- required
-- max|15   # 15 is the value assigned as maximum
-- min|10   # 10 is the value assigned as minimum
-- date
-- matches|c_password # Matches value with c_password input field
-- unique|np_users # check unique value in table np_users [column check is its key you assign rule with]
```

### Database Builder
```php
# Inbuilt
$corelib->dbbuilder->table('users')->getAll();

# Example For Using DatabaseQuery PDO

$statement  = $corelib->dbbuilder->pdo->prepare('SELECT * FROM users');
$statement->execute();
```

### Oauth
```php
$user_data = $corelib->oauth->init(); // Stores user data if success or error message.
```

### Documentation
<a href="https://docs.8beez.com/corelib">Core-Library Docs</a>

### Support Center
<a href="https://docs.8beez.com/support">Ry-Zen official Support</a>

### Contributors
<a href="https://rajuchoudhary.com.np/"> Raju Choudhary </a> 😎 - Creator and Maintainer
