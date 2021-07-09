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
# Every Built-In functions comes with Ry_ prefix
$ryzen->function->FunctionName();

# List of Built-In Functions
- Ry_Secure()             # [ Secures Your String Data ]
# Parameters $ryzen->function->Ry_Secure($string,$br,$strip);
# $ryzen->function->Ry_Secure('Hello World!',true,1);
Or
# $ryzen->function->Ry_Secure('Hello World!');

- Ry_Clean_String()       # [ Cleans String Data ]
# Parameters $ryzen->function->Ry_Clean_String($string);
# $ryzen->function->Ry_Clean_String('Hello World !');

- Ry_Generate_CSRF()      # [ Generates CSRF Token ]
# Parameters $ryzen->function->Ry_Generate_CSRF();
# $ryzen->function->Ry_Generate_CSRF();

- Ry_Match_CSRF()         # [ Matches The CSRF Token ]
# Parameters $ryzen->function->Ry_Match_CSRF($csrfToken);
# $ryzen->function->Ry_Match_CSRF('7asdh7cbf7ab');

- Ry_ObjectToArray()      # [ Converts Object To Array ]
# Parameters $ryzen->function->Ry_ObjectToArray($object);

- Ry_ArrayToObject()      # [ Converts Array To object ]
# Parameters $ryzen->function->Ry_ArrayToObject($array);

- Ry_Curl_Url()           # [ Curls URL ]
# Parameters $ryzen->function->Ry_Curl_Url($url);
# $ryzen->function->Ry_Curl_Url('https://google.com/curl/api/weather');

- Ry_Encrypt()            # [ Encrypts Data into Hash ]
# Parameters $ryzen->function->Ry_Encrypt($openString);
# $ryzen->function->Ry_Encrypt('encryptMe');

- Ry_Decrypt()            # [ Decrypts Hash into Data ]
# Parameters $ryzen->function->Ry_Decrypt($encryptedString);
# $ryzen->function->Ry_Decrypt('exhjd9xnd9d');

- Ry_Strip_Long_Text()    # [ Strips Long Text to Short Text ]
# Parameters $ryzen->function->Ry_Strip_Long_Text($string, $strip_length);
# $ryzen->function->Ry_Strip_Long_Text('Hello World', 3);

- Ry_Time_Completed()     # [ Gives You Time Completion Value ]
# Parameters $ryzen->function->Ry_Time_Completed($timestamp or time);
# $ryzen->function->Ry_Time_Completed(1234594);
or
# $ryzen->function->Ry_Time_Completed('2020-11-05');

- Ry_Get_Browser()        # [ Gives The Current Browser and its specific details ]
# Parameters $ryzen->function->Ry_Get_Browser();

- Ry_Get_Ip_Address()     # [ Gives The IP Address ]
# Parameters $ryzen->function->Ry_Get_Ip_Address();

- redirect()              # [ Redirects the URl ]
# Parameters $ryzen->function->redirect($url);
# $ryzen->function->redirect('home');
````
##### Using Authentication Functions
```php
$ryzen->auth->FunctionName();

#List of Built-In Authentication Function

- Ry_Is_Logged_In()             # [ True If Logged in False if Not Logged in ]
# Parameters : $ryzen->auth->Ry_Is_Logged_In();

- Ry_Get_User_From_Session_ID() # [ Gives user_id From Loggedin session ]
# Parameters : $ryzen->auth->Ry_Get_User_From_Session_ID($session_id);
# $ryzen->auth->Ry_Get_User_From_Session_ID('begd7dh392jd0sj');

- Ry_Create_Login_Session()     # [ Creates new Login Session of user ]
# Parameters : $ryzen->auth->Ry_Create_Login_Session($user_id);
# $ryzen->auth->Ry_Create_Login_Session(1);

- Ry_Is_Valid_Sign_In()         # [ Checks For Correct Username or Password ]
# Parameters : $ryzen->auth->Ry_Is_Valid_Sign_In($username or email or phonenumber, $password);
# $ryzen->auth->Ry_Is_Valid_Sign_In('ryzen', '12345678');
Or
# $ryzen->auth->Ry_Is_Valid_Sign_In('ryzen@ryzen.com', '12345678');
Or
# $ryzen->auth->Ry_Is_Valid_Sign_In(98123129292, '12345678');

- Ry_Get_User_Id()              # [ Returns User_id From Username, email or Phone ]
# Parameters : $ryzen->auth->Ry_Get_User_Id($username or email or phonenumber);
# $ryzen->auth->Ry_Get_User_Id('ryzen');
Or
# $ryzen->auth->Ry_Get_User_Id('ryzen@ryzen.com');
Or
# $ryzen->auth->Ry_Get_User_Id(98123123123);

- Ry_Create_Login()             # [ Creates New Login with username, email or phone ]
# Parameters : $ryzen->auth->Ry_Create_Login($username or email or phonenumber, $rememberLogin);
# $ryzen->auth->Ry_Create_Login('ryen', 1);
Or
# $ryzen->auth->Ry_Create_Login('ryzen');

- Ry_Login_With_Id()            # [ Creates New Login With ID ]
# Parameters : $ryzen->auth->Ry_Login_With_Id($user_id);
# $ryzen->auth->Ry_Login_With_Id(1);

- Ry_User_Data()                # [ Returns all user Data from ID ]
# Parameters : $ryzen->auth->Ry_User_Data($user_id);
# $ryzen->auth->Ry_User_Data(1);

- Ry_Sign_Out()                 # [ Signs out Current User ]
# Parameters : $ryzen->auth->Ry_Sign_Out();
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