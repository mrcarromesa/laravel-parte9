<h1>Laravel parte 5</h1>

<strong>Referências:</strong>

- [Migration](https://laravel.com/docs/6.x/migrations)
- [Model](https://laravel.com/docs/6.x/eloquent#defining-models)

<strong>Importante: antes de continuar verifique se as tabelas estão com a engine definida como InnoDB, caso contrário altere. E certifique-se de que o conteúdo das tabelas devs e posts estejam vazias.</strong>

- Criar Nova Migration Para alteração da tabela ```posts```:

```bash
php artisan make:migration add_dev_id_to_posts_table --table=posts
```

- Isso irá criar mais um arquivo dentro de ```database/migrations```

- Abra e edite esse arquivo, vamos alterar a função ```up()``` desse arquivo:

```php
Schema::table('posts', function (Blueprint $table) {
    $table->unsignedBigInteger('dev_id')->after('id');

    //1 - nome da coluna; EX.: dev_id
    //2 - nome do relacionamento (Opcional); EX.: devs_id_posts
    //3 - o campo de referencia na tabela Pai; EX.: id
    //4 - o nome da tabela; Ex.: devs
    $table->foreign('dev_id', 'devs_id_posts')->references('id')->on('devs');
});
```

- Vamos alterar o conteudo da function ```down()``` para o caso de um eventual rollback:

````php
Schema::table('posts', function (Blueprint $table) {
    // remove relacionamento
    $table->dropForeign('devs_id_posts');
    // remove coluna
    $table->dropColumn('dev_id');
});
````

- Execute a migration:

```bash
php artisan migrate
```

- Será alterada a tabela ```posts``` na base de dados, adicionando o campo ```dev_id```

---

<h2>Alterando o Model Devs</h2>

- No Model ```Devs``` adicione a seguinte function:

```php
public function posts()
{
    // 1 - Path\Model\Tabela_Filha
    // 2 - Nome do campo de referencia na tabela filha
    // 3 - Nome do campo de referencia na tabela pai
    return $this->hasMany('App\Http\Models\Posts', 'dev_id', 'id');
}
```

- Isso indicará que a tabela devs poderá possuir 1 ou mais registro da tabela posts

---

<h2>Alterando o Model Posts</h2>

- No Model ```Posts``` adicione a seguinte function:

```php
public function devs()
{
    // 1 - Path\Model\Tabela_Pai
    // 2 - Nome do campo de referencia na tabela atual
    return $this->belongsTo('App\Http\Models\Devs', 'dev_id');
}
```

- Isso indicará que a tabela posts poderá pertencer a 1 ou mais registros da tabela devs.

---

<h2>Ajustando o controller PostController</h2>

- Na function ```update()``` altere o seguinte:

- De:

```php
$json = $request->only(['titulo', 'descricao']);
```

- Para:

```php
$json = $request->only(['titulo', 'descricao', 'dev_id']);
```

---

<h2>Ajustando a validação ValidarPost</h2>

- altere o conteudo da function ```rules()``` para o seguinte:

```php
// Para criação de um novo registro todos os campos devem ser validados
if ($this->isMethod('POST')) {
    return [
        'titulo' => 'required|max:191',
        'descricao' => 'required',
        'dev_id' => 'required|integer|exists:devs,id' // o exists valida se a referencia exite na tabela devs campo id
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

if ($this->has('dev_id')) {
    $validacao = ['dev_id' => 'required|integer|exists:devs,id'];
}

return $validacao;
```

- Alterar o conteudo da function ```messages()``` para o seguinte:

```php
return [
    'titulo.required' => 'Campo obrigatório',
    'titulo.max' => 'O campo deverá conter no máximo :max',
    'descricao.required' => 'Campo obrigatório',
    'dev_id.required' => 'Campo obrigatório',
    'dev_id.integer' => 'O valor deve ser numerico',
    'dev_id.exists' => 'Referência não encontrada',
];
```

- A regra ```exists``` valida se a referencia exite na tabela devs campo id!!

- Agora no Postman, na rota ```http://localhost:8000/post``` no metodo ```POST``` o campo ```dev_id``` deve ser informado e a referência deverá existir na tabela devs campo ```ìd```

---

<h2>Alterando no POSTMAN</h2>

- primeiro insira um dev na tabela ```devs```, pode utilizar a rota que foi criado para isso no postman.


- No Postman alterar a rota

- Metodo: POST;

- Url: http://localhost:8000/post

- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.

- No campo de texto adiconar:

```js
{
    "titulo": "Meu titulo1",
    "descricao": "minha descricao",
    "dev_id": "1"
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir o registro.

<strong>Importante o valor do campo dev_id, deve existir na tabela devs campo id</strong>

---

<h2>Criar Post juntamente com Devs</h2>

- Vamos criar um controller para realizar isso, execute o seguinte comando:

```bash
php artisan make:controller DevPostController --resource
```

- Será criado o arquivo ```DevPostController```

- Adicione a ```use```:

```php
use App\Http\Models\Devs;
```

- Lista de devs com o seus posts:

- Na function ```index()``` adicione o seguinte:

```php
// dentro do with estamos utilizando o nome da função que foi criada no Model Devs, a qual retorna os posts relacionados a cada dev que será consultado
$dev = Devs::select()->with(['posts'])->get();
return $dev;
```

- Na function ```store()``` adicione o seguinte:

```php
// Separa o que irá compor o valor do dev
$json = $request->only(['github_username', 'nome']);
// Separa o que irá compor o valor do post
$json_post = $request->only(['post']);

// insere registro na tabela dev
$dev = Devs::create($json);

// insere registro na tabela post com o dev_id criado pelo comando acima.
$dev->posts()->create($json_post['post']);

$dev->save();

return $dev;
```

- Na function ```update()``` adicione o seguinte:

```php
$json = $request->only(['nome', 'github_username']);
$json_post = $request->only(['post']);

$dev = Devs::find($id);

if (!$dev) {
    return response()->json(['error' => 'Not Found'], 404);
}

$dev->posts()->create($json_post['post']);

$dev->update($json);
$dev->save();

return $dev;
```

- Na function ```destroy()``` adicione o seguinte:

```php
$dev = Devs::find($id);

if (!$dev) {
    return response()->json(['error' => 'Not Found'], 404);
}

$dev->posts()->delete();

$dev->delete();

return response()->json(['ok' => 'Registro removido']);
```

---
<h2>Criar Rota</h2>

- No arquivo ```routes/web.php``` criar a rota:

```php
Route::resource('dev-post', 'DevPostController')->parameters([
    'post' => 'id'
]);
```
---

<h2>Testando</h2>

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/dev-post
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "nome": "Meu Nome",
    "github_username": "meu_github_username1",
    "post": {"titulo": "teste", "descricao": "teste descricao"}
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir o registro.

---

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: GET;
- Url: http://localhost:8000/dev-post

- Clicar no botão SEND.

- E retornará os registros.

---

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: PUT;
- Url: http://localhost:8000/dev-post/1
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "nome": "Meu Nome",
    "github_username": "meu_github_username1",
    "post": {"titulo": "teste", "descricao": "teste descricao"}
}
```

- Clicar no botão SEND.

- E na base de dados deverá alterar o registro.

- e inserir os posts

---

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: DELETE;
- Url: http://localhost:8000/dev-post/1

- Clicar no botão SEND.

- E na base de dados deverá remover os registros de devs e posts.

---
