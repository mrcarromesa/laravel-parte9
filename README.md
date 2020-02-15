<h1>Laravel parte 4</h1>

<strong>Referências:</strong>

- [Migration](https://laravel.com/docs/6.x/migrations)
- [Model](https://laravel.com/docs/6.x/eloquent#defining-models)
- [Controllers](https://laravel.com/docs/6.x/controllers)
- [Validation](https://laravel.com/docs/6.x/validation)

- Criar Nova Migration Para criação da tabela ```posts```:

```bash
php artisan make:migration create_posts_table --create=posts
```

- Isso irá criar mais um arquivo dentro de ```database/migrations```

- Abra e edite esse arquivo, vamos alterar a função ```up()``` desse arquivo:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('titulo');
    $table->text('descricao');
    $table->timestamps();
});
```

- Execute a migration:

```bash
php artisan migrate
```

- Será criada a tabela ```posts``` na base de dados

---

<h2>Criando o Model</h2>

- Execute o comando para criar o model:

```bash
php artisan make:model Http\\Models\\Posts
```

- Irá criar um arquivo em ```app/Http/Models/Posts```, vamos edita-lo adicionando o seguinte dentro da class:

```php
protected $table = 'posts';
protected $primaryKey = 'id';
protected $guarded = [];
```

---

<h2>Criando o Controller</h2>

- Execute o comando para criar o controller:

```bash
php artisan make:controller PostController --resource
```

- Será criado o arquivo ```app/Http/Controllers/PostController```, vamos implementar algumas funções;

- Inicialmente adicione a seguinte ```use``` ao arquivo:

```php
use App\Http\Models\Posts;
```

- No metodo ```index()``` adicione o seguinte:

```php
$post = Posts::all();
return $post;
```

- No metodo ```store()``` adicione o seguinte:

```php
$json = $request->json()->all();
$post = Posts::create($json);
$post->save();
return $post;
```

- No metodo ```update()``` adicione o seguinte:

```php
$json = $request->only(['titulo', 'descricao']);

$post = Posts::find($id);

if (!$post) {
    return response()->json(['error' => 'Not found'], 404);
}

$post->update($json);
$post->save();

return $post;
```

- Finalmente no metodo ```destroy()``` adicione o seguinte:

```php
$post = Posts::find($id);

if (!$post) {
    return response()->json(['error' => 'Not Found'], 404);
}

$post->delete();

return response()->json(['ok' => 'Registro removido']);
```

---

<h2>Validação</h2>

- Executar o comando:

```bash
php artisan make:request ValidarPost
```

- Será criado o arquivo em ```app/Http/Requests/ValidarPost```

- Vamos realizar as modificações necessárias:

- Adicionar as ```use```:

```php
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
```

- Alterar o return da function ```authorize()``` para:

```php
return true;
```

- Na function ```rules()``` alterar o conteudo para o seguinte:

```php
// Para criação de um novo registro todos os campos devem ser validados
if ($this->isMethod('POST')) {
    return [
        'titulo' => 'required|max:191',
        'descricao' => 'required',
    ];
}

// Quando for atualização de um registro, validar apenas o que foi enviado
$validacao = [];

if ($this->has('descricao')) {
    $validacao = ['descricao' => 'required'];
}

if ($this->has('titulo')) {
    $validacao = ['titulo' => 'required|max:191'];
}

return $validacao;
```

- Adicionar a seguinte function para personalizar as mensagens:

```php
public function messages()
{
    return [
        'titulo.required' => 'Campo obrigatório',
        'titulo.max' => 'O campo deverá conter no máximo :max',
        'descricao.required' => 'Campo obrigatório',
    ];
}
```

- Por fim adincionar a seguinte função para retornar os erros de validação via JSON:

```php
protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(response()
        ->json($validator->errors(), 400));
}
```

---
<h2>Ajustar Controller</h2>

- No arquivo ```PostController``` add ```use```:

```php
use App\Http\Requests\ValidarPost;
```

- Ajustar function ```public function store(Request $request)``` para ```public function store(ValidarPost $request)```

- Ajustar function ```public function update(Request $request, $id)``` para ```public function update(ValidarPost $request, $id)```

---
<h2>Criar Rota</h2>

- No arquivo ```routes/web.php``` adicionar a seguinte rota:

```php
Route::resource('post', 'PostController')->parameters([
    'post' => 'id'
]);
```
---

<h2>Criar testes com o Postman</h2>

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/post
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "titulo": "Meu titulo1",
    "descricao": "minha descricao"
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir o registro.

---

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: GET;
- Url: http://localhost:8000/post

- Clicar no botão SEND.

- E será retornado os registros da tabela

---

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: PUT;
- Url: http://localhost:8000/post/1
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "titulo": "Meu titulo1 alterado",
    "descricao": "minha descricao alterado"
}
```

- Clicar no botão SEND.

- E na base de dados deverá alterar o registro.

---

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: DELETE;
- Url: http://localhost:8000/post/1

- Clicar no botão SEND.

- E na base de dados deverá reover o registro.

---
