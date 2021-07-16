## core-library
Develop a powerful, fast and secured Core PHP Web Application With This Library packed with features.

### Installing Library Using Composer
`$ composer require ryzen/core-library`

### Setting Things UP
The very first thing to do is loading the autoload

`include 'vendor/autoload.php';`

Start Session To Use Auth

`session_start();`

Creating New Configuration File

```php
$config = array(
    'mysql_database_config' =>
        [
            'db_host'       => "YOUR_DATABASE_HOST",
            'db_name'       => "YOUR_DATABASE_NAME",
            'db_user'       => "YOUR_DATABASE_USER",
            'db_pass'       => "YOUR_DATABASE_PASS",
            'db_driver'	    => 'mysql',
            'db_charset'    => 'utf8',
            'db_collation'  => 'utf8_general_ci',
            'db_prefix'	    => ''
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

### The Final Step
```php
$ryzen  = new Ryzen\CoreLibrary\Ry_Zen($site_url,$config);
```
### Use Case
##### View Loading
```php
# Loads View Page From themes/default/layouts/content.phtml;
$ryzen->Ry_Load_Page('content');

# Overriding View URl;
$ryzen->theme_url = 'YOUR_UI_LOCATION';
```
##### Using In-Built Functions
```php
use Ryzen\CoreLibrary\Functions;

# Every Built-In functions comes with Ry_ prefix
Functions::FunctionName();

# List of Built-In Functions
- Ry_hmac_create()     🆕 # [ create unique csrf for diff form ]
# Parametes Functions::ry_hmac_create($string)
# Functions::ry_hmac_create('iWillSecureLoginForm');
# takes unique string to make unique token with csrf

- Ry_hmac_check()      🆕 # [ checks passed token with generated once ]
# Parametes Functions::ry_hmac_create($string, $token)
# Functions::ry_hmac_create('iWillSecureLoginForm', $TokenFromPOSTorGET);
# matches the passed token with csrf and string you passed to secure;

- Ry_Secure()             # [ Secures Your String Data ]
# Parameters Functions::Ry_Secure($string,$br,$strip);
# Functions::Ry_Secure('Hello World!',true,1);
Or
# Functions::Ry_Secure('Hello World!');

- Ry_Clean_String()       # [ Cleans String Data ]
# Parameters Functions::Ry_Clean_String($string);
# Functions::Ry_Clean_String('Hello World !');

- Ry_Generate_CSRF()      # [ Generates CSRF Token ]
# Parameters Functions::Ry_Generate_CSRF();
# Functions::Ry_Generate_CSRF();

- Ry_Match_CSRF()         # [ Matches The CSRF Token ]
# Parameters Functions::Ry_Match_CSRF($csrfToken);
# Functions::Ry_Match_CSRF('7asdh7cbf7ab');

- Ry_ObjectToArray()      # [ Converts Object To Array ]
# Parameters Functions::Ry_ObjectToArray($object);

- Ry_ArrayToObject()      # [ Converts Array To object ]
# Parameters Functions::Ry_ArrayToObject($array);

- Ry_Curl_Url()           # [ Curls URL ]
# Parameters Functions::Ry_Curl_Url($url);
# Functions::Ry_Curl_Url('https://google.com/curl/api/weather');

- Ry_Encrypt()            # [ Encrypts Data into Hash ]
# Parameters Functions::Ry_Encrypt($openString);
# Functions::Ry_Encrypt('encryptMe');

- Ry_Decrypt()            # [ Decrypts Hash into Data ]
# Parameters Functions::Ry_Decrypt($encryptedString);
# Functions::Ry_Decrypt('exhjd9xnd9d');

- Ry_Strip_Long_Text()    # [ Strips Long Text to Short Text ]
# Parameters Functions::Ry_Strip_Long_Text($string, $strip_length);
# Functions::Ry_Strip_Long_Text('Hello World', 3);

- Ry_Time_Completed()     # [ Gives You Time Completion Value ]
# Parameters Functions::Ry_Time_Completed($timestamp or time);
# Functions::Ry_Time_Completed(1234594);
or
# Functions::Ry_Time_Completed('2020-11-05');

- Ry_Get_Browser()        # [ Gives The Current Browser and its specific details ]
# Parameters Functions::Ry_Get_Browser();

- Ry_Get_Ip_Address()     # [ Gives The IP Address ]
# Parameters Functions::Ry_Get_Ip_Address();

- redirect()              # [ Redirects the URl ]
# Parameters Functions::redirect($url);
# Functions::redirect('home');
````
##### Using Authentication Functions
```php
use Ryzen\CoreLibrary\Auth;

Auth::FunctionName();

#List of Built-In Authentication Function

- Ry_Is_Logged_In()              # [ True If Logged in False if Not Logged in ]
# Parameters : Auth::Ry_Is_Logged_In();

- Ry_Get_User_From_Session_ID() # [ Gives user_id From Loggedin session ]
# Parameters : Auth::Ry_Get_User_From_Session_ID($session_id);
# Auth::Ry_Get_User_From_Session_ID('begd7dh392jd0sj');

- Ry_Create_Login_Session()     # [ Creates new Login Session of user ]
# Parameters : Auth::Ry_Create_Login_Session($user_id);
# Auth::Ry_Create_Login_Session(1);

- Ry_Is_Valid_Sign_In()         # [ Checks For Correct Username or Password ]
# Parameters : Auth::Ry_Is_Valid_Sign_In($username or email or phonenumber, $password);
# Auth::Ry_Is_Valid_Sign_In('ryzen', '12345678');
Or
# Auth::Ry_Is_Valid_Sign_In('ryzen@ryzen.com', '12345678');
Or
# Auth::Ry_Is_Valid_Sign_In(98123129292, '12345678');

- Ry_Get_User_Id()              # [ Returns User_id From Username, email or Phone ]
# Parameters : Auth::Ry_Get_User_Id($username or email or phonenumber);
# Auth::Ry_Get_User_Id('ryzen');
Or
# Auth::Ry_Get_User_Id('ryzen@ryzen.com');
Or
# Auth::Ry_Get_User_Id(98123123123);

- Ry_Create_Login()             # [ Creates New Login with username, email or phone ]
# Parameters : Auth::Ry_Create_Login($username or email or phonenumber, $rememberLogin);
# Auth::Ry_Create_Login('ryen', 1);
Or
# Auth::Ry_Create_Login('ryzen');

- Ry_Login_With_Id()            # [ Creates New Login With ID ]
# Parameters : Auth::Ry_Login_With_Id($user_id);
# Auth::Ry_Login_With_Id(1);

- Ry_User_Data()                # [ Returns all user Data from ID ]
# Parameters : Auth::Ry_User_Data($user_id);
# Auth::Ry_User_Data(1);

- Ry_Sign_Out()                 # [ Signs out Current User ]
# Parameters : Auth::Ry_Sign_Out();

- Ry_Value_Exists()             # [ Checks Whether the value exists in database ]
# Parameters : Auth::Ry_Value_Exists($data, $table, $operator);
# Auth::Ry_Value_Exists(['name'=>'raju','id'=>'4'], 'users', 'OR');
# Operator is AND by default if not provided
# Works as if name = raju OR id = 4
```
##### Session Helper
```php
use Ryzen\CoreLibrary\Session;

Session::FunctionName();

#List of Built-In Session Function

- put()              # [ Creates New Session ]
# Parameters : Session::put($key , $value);
# Session::put('key','value'); || Session::put(['key'=>'value',]);

- get()              # [ return session value using key ]
# Parameters : Session::get($key);
# Session::get('key');

- has()              # [ Checks whether the session exists ]
# Parameters : Session::has($key);

- forget()           # [ Deletes Specific Session ]
# Parameters : Session::forget($key);

- flush()            # [ Destroys every session ]
# Parameters : Session::flush();

```
##### Using DbBuilder
```php
# Custom PDO Query
$query = $ryzen->dbBuilder->pdo->prepare('SELECT * FROM users WHERE shyam = :shyam');
$query->bindValue(':shyam','shyam');
$query->execute();

# Using Builder
$db = $ryzen->dbBuilder;
```
##### Docs For dbBuilder Use Case
https://github.com/r-y-z-e-n/db-builder/blob/main/README.md