# Agentic-backend

This project is a backend application also ready to work with 3rd party, Whole API can be call and connected via HTTP Method and use Authorization Header for security prevention.

## 1. Project Brief

Agentic-backend is a robust Laravel-based backend application designed to provide a secure and scalable API infrastructure. The project aims to facilitate seamless integration with third-party services and client applications through HTTP methods, implementing OAuth2 authentication for enhanced security.

### Objectives:

-   Create a flexible and extensible API backend
-   Implement secure authentication and authorization mechanisms
-   Support integration with various third-party services
-   Provide a foundation for scalable web applications

### Expected Results:

-   A fully functional RESTful API
-   Secure user authentication and registration system
-   Customizable API scopes for granular access control
-   Easy integration with external services (e.g., Line Notify, PDF generation)
-   Comprehensive documentation for developers

## 2. Dependency Issues

### Development Environment:

-   PHP 8.1 or higher
-   Composer
-   Laravel Framework 10.x
-   MySQL 5.7 or higher (or compatible database)
-   Node.js and NPM (for frontend assets, if applicable)

### Deployment Environment:

-   Web server (Apache or Nginx)
-   PHP 8.1 or higher
-   MySQL 5.7 or higher (or compatible database)
-   Composer
-   SSL certificate (recommended for production)

### Key Dependencies:

-   Laravel Passport for OAuth2 authentication
-   GuzzleHTTP for HTTP requests
-   Other dependencies as listed in `composer.json`

## 3. Manual for Customization and Implementation

### Installation

1. Clone the repository:

```
   git clone [repository-url]
   cd agentic-backend
```

2. Install PHP dependencies:

```
composer install
```

3. Create and configure the `.env` file:

```
cp .env.example .env
```

> On Windows:

```
copy .env.example .env
```

4. Generate application key:

```
php artisan key:generate
```

5. Set up the database:

```
php artisan migrate
```

> Or for a fresh start:

```
php artisan migrate:fresh
```

6. Install Passport:

```
php artisan passport:install
```

> Or for password grant clients:

```
php artisan passport:client --password
```

### Configuration

1. Configure your `.env` file with appropriate values:

```
APP_NAME=
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=



MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=@sometime.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=@sometime.com
MAIL_FROM_NAME="${APP_NAME}"

PASSPORT_PASSWORD_CLIENT_ID=
PASSPORT_PASSWORD_SECRET=

API_SCOPES=
```

2. Define API Scopes: Protecting Resource APIs with API Scopes

-   Add desired scopes in the `API_SCOPES` variable in `.env`

    In this following `.env` file, We need to put or define naming of scope to corresponding objective.

```
.
.
API_SCOPES=line-notify,create-register-form-pdf,scope1,scope2,scope3
.
.
```

-   Update `app/Providers/AuthServiceProvider.php`:

```php
 public function boot(): void
 {
     Passport::tokensCan([
        'line-notify' => 'action for line notify.',
        'create-register-form-pdf' => 'action for create pdf',

         'scope1' => 'Description for scope1',
         'scope2' => 'Description for scope2',
         'scope3' => 'Description for scope3',
     ]);
 }
```

3. Set up API routes in `routes/api.php`:

Create or edit our routes in routes/api.php.

```php
//routes/api.php

use App\Http\Controllers\LineController; // Example controller with implement to do someting.
use App\Http\Controllers\PDFController; // Example controller

Route::group(['middleware' => 'auth:api'], function(){
.
.
  // use middleware to validate the access token scope values against the security definition and enforce the scope of access authorized
    Route::middleware('scope:line-notify')->post('/line-notify', [LineController::class, 'nameOfMethod1']);
    Route::middleware('scope:create-register-form-pdf')->post('/generate-pdf', [PDFController::class, 'nameOfMethod2']);

    Route::middleware('scope:scope1')->post('/endpoint1', [Controller1::class, 'method1']);
    Route::middleware('scope:scope2')->get('/endpoint2', [Controller2::class, 'method2']);
.
.
});

```

4. Implement controller methods as needed.

Create by following command.

```
php artisan make:controller LineController
php artisan make:controller PDFController
```

Sample implementation should be there.

```php
//app/Http/Controller/LineController.php
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class LineController extends Controller
{
    protected function nameOfMethod1(Request $request){
        try
        {
            if(empty($request->json()->all())){
                return response()->json([
                    'flag' => "fail",
                    'message' => 'parameter is not empty!'], 403);
            }

            $data = (object) $request->json()->all();
            $message=$data->message;

            Log::info("Send notify : "."");
            $line_api_url=Config::get('app.line_api_url'); //need to add LINE_API_URL to .env
            $line_api_key=Config::get('app.line_api_key'); //LINE_API_KEY

            $client = new Client(['verify' => false,'http_errors'=>false]);

            $options_param = array_merge(
                [
                    'form_params' => [
                        'message' => $message,
                        'stickerPackageId'=>4,
                        'stickerId'=>607
                    ]
                ], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $line_api_key
                   ]
                ]
            );

            $request = $client->post($line_api_url, $options_param );

            $body=$request->getBody();
            $obj=json_decode(strval($body), true);

            Log::debug($obj);

            if(($obj['status']==200)&($obj['message']=="ok")){
                Log::info("Message has been sent! ");
            }

        }
        catch (GuzzleHttp\Exception\ClientException $exception){
            Log::debug($exception->getResponse());

        }
        catch (GuzzleHttp\Exception\RequestException $exception){

            if($exception->hasResponse()){
                Log::debug($exception->getResponse()->getBody());
            }else{
                Log::error($exception->getMessage());
            }
        }
    }

}
.
.
;

```

## Running the Application

Start the development server:

```
php artisan serve
```

### Testing

Use tools like Postman to test your API endpoints. Example requests:

1. Register a user:

-   POST request to
    http://localhost:8000/api/register with header and json data
    ```
    --header 'Content-Type: application/json'
    ```
    ```
    --data-raw '
    {
        "name":"", //that your wish
        "email":"", //that your wish
        "password":"", //that your wish
        "c_password":"" //that your wish
    }
    '
    ```

2. Login:

-   POST request to
    http://localhost:8000/api/login with header and json data

    ```
    --header 'Content-Type: application/json'

    ```

    ```
    --data-raw '
    {
        "email":"", //that your wish
        "password":"" //that your wish
    }
    '
    ```

3. Retrive user data

-   GET request to http://localhost:8000/api/me with header and json data

    ```
    --header 'Content-Type: application/json'
    --header 'Accept: application/json' \
    --header 'Authorization: Bearer xxxx

    ```

    output

    ```
    {
            "success": true,
            "statusCode": 200,
            "message": "Authenticated use info.",
            "data": {
                "id": 1,
                "name": "user last",
                "email": "xxxx.sometime.com",
                "email_verified_at": null,
                "created_at": "2024-12-11T03:42:02.000000Z",
                "updated_at": "2024-12-11T03:42:02.000000Z"
            }
    }
    ```

4. Send email message

-   POST request to
    http://localhost:8000/api/email-notify with header and json data

    ```
    --header 'Content-Type: application/json'
    --header 'Accept: application/json' \
    --header 'Authorization: Bearer xxxx

    ```

    ```
    --data-raw '
    {
        "to_email":"xxxx.sometime.com" //that your wish
        "mail_from_name":"Something Official Account",
        "message": "Hello"
    }
    '
    ```

    output

    ```
    {
        "result": {
            "status_message": "Email notification sent",
            "status": "OK"
        }
    }
    ```

### Customization (step by step)

1. Add new API endpoints by creating controllers and defining routes.
2. Implement additional services by creating new service classes.
3. Extend the authentication system as needed, e.g., adding social login.

## 4. Additional Information

For more detailed information on specific features or advanced configuration, please refer to the Laravel documentation or contact the project maintainers.

LDAP (future feature integate soon.)

this following `.env` file

```
LDAP_LOGGING=true
LDAP_CONNECTION=default
LDAP_HOST=
LDAP_USERNAME=""
LDAP_PASSWORD=
LDAP_PORT=389
LDAP_BASE_DN="dc=sometime,dc=local"
```

```
php artisan ldap:test
```

Ses more..
https://ldaprecord.com/docs/laravel/v2/debugging/#logging-in

## Sample Data (Optinal if you need.)

Running Seeders. Use command below.

```
php artisan db:seed
```

or

```
php artisan db:seed --class UserStaffSeeder
```
