<h1>Laravel parte 7</h1>

<strong>Referências:</strong>

- [Vídeo](https://www.youtube.com/watch?v=0hGsLgnrk68)
- [Git](https://github.com/anil-sidhu/laravel-passport-poc)
- [API Authentication](https://laravel.com/docs/6.x/api-authentication)
- [Reset Password](https://medium.com/modulr/api-rest-with-laravel-5-6-passport-authentication-reset-password-part-4-50d27455dcca)

- Instalar o pacote laravel/passaport:

```bash
composer require laravel/passport
```

- Abrir o arquivo `config/app.php` e adicionar o service provider:

```php
// config/app.php
'providers' =>[
    Laravel\Passport\PassportServiceProvider::class,
],
```

- Executar as migrations:

```bash
php artisan migrate
```

- Aplicar as alterações:

```bash
php artisan passport:install
```

- Ajustar Model `app/User`:

```php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}

```

- Ajustar arquivo `app/Providers/AuthServiceProvider.php`: (Talvez não necessário):

```php
namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        //
    }
}
```

- Ajustar arquivo `config/auth.php`:

```php
/*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
            //'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];
```

---

- Criar o controller:

```bash
php artisan make:controller API\\UserController
```

- Ajustar controller `API\UserController`:

```php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success' => $success], $this->successStatus);
    }
    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}
```
---
- Ajustar o arquivo `routes/api.php`

```php
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::group(['middleware' => 'auth:api'], function(){
Route::post('details', 'API\UserController@details');
});
```
---

- No Postman em todas as rotas adicionar a opção no header:

```bash
Accept: application/json
```

- Para evitar redirecionamento no `App\Http\Middleware\Authenticate`

---

<h2>Criar Rotas no Postman</h2>

- Rota Register:

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/api/register
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
	"name": "Meu Nome",
	"email": "meuemail@dominio.com",
	"password": "12345678",
	"c_password": "12345678"
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir o registro na tabela users.

- E deverá retornar o token na requisição.

---

- Rota Login:

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/api/login
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
	"email": "meuemail@dominio.com",
	"password": "12345678",
}
```

- Clicar no botão SEND.

- E deverá retornar o token na requisição. Esse token pode ser inserido na aba 
Authorization, Type: OAuth 2.0 e no campo que aparecerá `Access Token` inserir o token gerado pelo login.

Poderá utilizar para as demais rotas a autenticação.

---

<h2>Acessando rotas autenticada</h2>

- Ajustes uma rota para utilizar a autenticação:

```php
Route::group(['middleware' => 'auth:api'], function () {
    //Route::post('details', 'API\UserController@details');
    Route::resource('dev-tech', 'DevTechsController')->parameters([
        'dev-tech' => 'id'
    ]);
});
```

- No [Postman](https://www.postman.com/) criar a rota

- Ajustes todos os metodos que acessam `dev-tech`,

- Na aba Headers adicione a chave: `Accept` com o valor: `application/json`

- Na aba 
Authorization, a opção Type deverá ser: OAuth 2.0; e no campo que aparecerá `Access Token` inserir o token gerado pelo login.

- Agora acesse normalmente essa rota.

---

<h2>Reset Password</h2>

- Criar o model PasswordReset:

```bash
php artisan make:model Http\\Models\\PasswordReset
```

- Acessar o arquivo criado.

- Adicionar ao `PasswordReset`:

```php
protected $fillable = [
    'email', 'token'
];
```

---

<h2>Criar Notifications</h2>

- Vamos criar 2 notifications:

```bash
php artisan make:notification PasswordResetRequest
```

- e

```bash
php artisan make:notification PasswordResetSuccess
```

- Dessa forma serão criados os arquivos
`app/Notifications/PasswordResetRequest.php` e `app/Notifications/PasswordResetSuccess.php`

- No arquivo `PasswordResetRequest` altere pelo seguinte:

```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/api/password/find/' . $this->token);

        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', url($url))
            ->line('If you did not request a password reset, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

```


- No arquivo `PasswordResetSuccess` altere pelo seguinte:

```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You are changed your password successful.')
            ->line('If you did change password, no further action is required.')
            ->line('If you did not change password, protect your account.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

```
---

<h2>Instalar dependencia</h2>

- Vamos instalar o pacote para trabalhar melhor com datas, que no caso é a lib [Carbon](https://carbon.nesbot.com/):

```bash
composer require nesbot/carbon
```

---

<h2>Criando Controller</h2>

- Crie o controller `PasswordResetController`:

```bash
php artisan make:controller Auth\\PasswordResetController
```

- Será criado o arquivo em `app/Http/Auth/PasswordResetController`

- Altere o conteúdo para o seguinte:

```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\User;
use App\Http\Models\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'We can`t find a user with that e-mail address.'
            ], 404);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
            ]
        );
        if ($user && $passwordReset) {
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
        }

        return response()->json([
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }
        return response()->json($passwordReset);
    }
    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        if (!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'We can`t find a user with that e-mail address.'
            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json($user);
    }
}

```
---

<h2>Rotas</h2>

- Adicione as seguintes rotas em `routes/api.php`:

```php
Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});
```

---

<strong>IMPORTANTE</strong>

- A tabela `password_resets`, deve conter os campos:
- id: big Increments
- updated_at: timestamps

*Caso não tenha crie, pode ser via migration

---

<h2>Prepara envio de e-mail</h2>

- Para efetuar testes Crie uma conta no site mailtrap.io e copie as credenciais no lugar destinado a isso no arquivo .env


---

<h2>Criar Rotas no Postman</h2>

- Rota create (Solicita nova senha):

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/api/password/create
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
	"email": "meuemail@dominio.com"
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir o registro na tabela password_resets.

- E enviara o e-mail

---

- Ao acessar o e-mail acesse a url que será algo como isso:
`http://url.../api/password/find/TOKEN`

- Acesse a url e copie o valor do token.

- Agora...

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/api/password/reset
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
	"email": "meuemail@dominio.com",
	"password": "87654321",
	"password_confirmation": "87654321",
    "token": "INSIRA AQUI O VALOR COPIADO DO TOKEN"
}
```

- Clicar no botão SEND.

- Pronto sua nova senha foi alterada

---
