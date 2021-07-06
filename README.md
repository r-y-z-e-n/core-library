## core-library
Develop a powerful, fast and secured Core PHP Web Application With This Library packed with features.

## Installing Library Using Composer
`$ composer require ryzen/core-library`

## Setting Things UP
The very first thing to do is loading the autoload

`include 'vendor/autoload.php';`

Creating New Configuration File

```
$config = array(
    'mysql_database_config' =>
        [
            'db_host'=>"YOUR DATABASE_HOST",
            'db_name'=>"YOUR DATABASE_NAME",
            'db_user'=>"YOUR DATABASE_USER",
            'db_pass'=>"YOUR DATABASE_PASS"
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

$site_url = 'http://localhost/YOUR_APPLICATION_ROOT_PATH';
```

## The Final Step
```
$myApp  = new Ryzen\CoreLibrary\Ry_Zen($site_url,$config);
```
