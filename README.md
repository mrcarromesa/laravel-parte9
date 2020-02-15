<h1>Laravel parte 1</h1>

<strong>Referências:</strong>

- [Route](https://laravel.com/docs/6.x/routing)
- [Migration](https://laravel.com/docs/6.x/migrations)
- [Model](https://laravel.com/docs/6.x/eloquent#defining-models)

- Criar projeto laravel, executar o comando:

```bash
laravel new devs
```

- Criar base de dados
- Alterar arquivo ```.env``` configurando os dados de acesso a base de dados.

** Tenha certeza que os comandos estão sendo executados na pasta do projeto, onde tem o arquivo ```artisan```

- Criar migration:

```bash
php artisan make:migration create_devs_table --create=devs
```

- Abrir arquivo criado em ```database/migrations/```

- Na function ```up()``` alterar o conteudo para:

```php
    Schema::create('devs', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('nome');
        $table->string('github_username');
        $table->timestamps();
    });
```

** Talvez seja necessário realizar um ajuste no arquivo ```app/Providers/AppServiceProvider.php``` para evitar problema com a quantidade de carateres gerados pela função string, na base MySQL, 5.8 ou menor. **

- Nesse caso no arquivo ```app/Providers/AppServiceProvider.php``` adicionar a use:

```php
use Illuminate\Support\Facades\Schema;
```

- Na função ```boot()``` desse mesmo arquivo adicionar o seguinte:

```php
    Schema::defaultStringLength(191);
```

- Agora sim podemos executar a migration utilizando o comando:

```bash
php artisan migrate
```

- A tabela deverá ser criada no banco de dados.

---

<h2>Model</h2>

<p>Passos para criar o Model</p>

- Executar o comando:

```bash
php artisan make:model Http\\Models\\Devs 
```

- Será criado o arquivo ```app/Http/Models/Devs.php```

- Adicionar o conteúdo dentro da class Devs:

```php
protected $table = 'devs'; //Nome da tabela na base

protected $primaryKey = 'id'; //Campo primary key da tabela

protected $guarded = []; // Permitir que todos os campos sejam preenchidos
```

- No arquivo ```routes/web.php``` adicionar a use:

```php
use App\Http\Models\Devs;
```

- Adicionar ainda no arquivo routes:

```php
Route::post('devs', function () {
    $json = request()->json()->all();
    $devs = Devs::create($json);
    $devs->save();

    return $devs;
});
```

- No arquivo ```app/Http/Middleware/VerifyCsrfToken.php``` alterar:

```php
    protected $except = [
        //
    ];
```

- para:

```php
    protected $except = [
        '*',
    ];
```


- Executar o comando:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/devs
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "nome": "Meu Nome",
    "github_username": "meu_github_username"
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir o registro.

---

<h2>Um pouco mais sobre migrations</h2>

- alterar tabela, adicionar campo, executar comando:

```bash
php artisan make:migration add_column_to_devs_table --table=devs
```

- Mais detalhes em [Creating Columns](https://laravel.com/docs/6.x/migrations#creating-columns)

- Como executar rollback da migration:
[Rolling Back Migrations](https://laravel.com/docs/6.x/migrations#rolling-back-migrations)

---

<h2>Obter todos os registro da tabela</h2>

- No arquivo ```routes/web.php``` adicionar o seguinte:

```php
Route::get('devs', function () {
    $devs = Devs::all();
    return $devs;
});
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: GET;
- Url: http://localhost:8000/devs

- Clicar em SEND, deverá exibir todos os devs cadastrados.

---

<h2>Definir campos que devem ser ocultos</h2>

- Declarar direto no Model: [Hiding Attributes From JSON](https://laravel.com/docs/6.x/eloquent-serialization#hiding-attributes-from-json)

- Ex.: 

```php
protected $hidden = ['coluna'];
```

- Ou antes de retornar os dados: [makeHidden](https://laravel.com/docs/6.x/eloquent-collections#method-makeHidden)

- Ex.:

```php
$user->makeHidden('attribute');
```

---
<h2>Definir campos que devem ser visiveis</h2>

- Declarar direto no Model: [Hiding Attributes From JSON](https://laravel.com/docs/6.x/eloquent-serialization#hiding-attributes-from-json)

- Ex.:

```php
protected $visible = ['coluna1', 'coluna2'];
``` 
- Ou antes de retornar os dados: [makeVisible](https://laravel.com/docs/6.x/eloquent-collections#method-makeVisible)

- Ex.: 

```php
$user->makeVisible('attribute');
```
---

<h2>Update</h2>

- No arquivo ```routes/web.php``` criar a rota:

```php
Route::put('devs/{id}', function ($id) {

    // obtendo os dados enviados via metodo PUT
    $json = request()->only(['nome', 'github_username']);

    // o only obtem campos especificos de uma requisição

    // buscar os dados na tabela pelo id enviado
    $devs = Devs::find($id);

    // verifica se o registro com o id informado acima existe
    if (!$devs) {
        // caso não exista RETORNA um erro 404
        return response()->json(['error' => 'Not found'], 404);
    }
    // caso exista...

    // realiza o update do registro
    $devs->update($json);
    $devs->save();

    // retorna o registro alterado
    return response()->json($devs);
    //return $devs;
});
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: PUT;
- Url: http://localhost:8000/devs/1
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "nome": "Meu Nome alterado",
    "github_username": "meu_github_username_alterado"
}
```

- Clicar no botão SEND.

- E na base de dados deverá alterar o registro.

---

<h2>Delete</h2>

- No arquivo ```routes/web.php``` adicionar o seguinte:

```php
Route::delete('devs/{id}', function ($id) {

    // buscar os dados na tabela pelo id enviado
    $devs = Devs::find($id);

    // verifica se o registro com o id informado acima existe
    if (!$devs) {
        // caso não exista RETORNA um erro 404
        return response()->json(['error' => 'Not found'], 404);
    }
    // caso exista...

    // realiza o delete
    $devs->delete();

    // Retorna uma mensagem
    return response()->json(['ok' => 'Registro removido']);
});
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: DELETE;
- Url: http://localhost:8000/devs/1
- clicar no botão SEND;
- O registro deverá ser removido da tabela
