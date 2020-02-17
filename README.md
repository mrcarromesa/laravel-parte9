<h1>Laravel parte 6</h1>

<strong>Referências:</strong>

- [Migration](https://laravel.com/docs/6.x/migrations)
- [Model](https://laravel.com/docs/6.x/eloquent#defining-models)

- Criar migrate para criar tabelas techs:

```bash
php artisan make:migration create_techs_table --create=techs
```

- Abrir o arquivo criado em ```database/migrations```

- Alterar conteudo da function ```up()``` para:

```php
Schema::create('techs', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('nome');
    $table->timestamps();
});
```
- Executar a migration:

```bash
php artisan migrate
```

- Irá criar a tabela ```techs```

---

<h2>Inserir Registros utilizando seeders</h2>

- Executar o comando:

```bash
php artisan make:seeder TechsTableSeeder
```

- Será criado um arquivo na pasta ```database/seeds/TechsTableSeeder```

- Adicionar a ```use``` no arquivo:

```php
use Illuminate\Support\Facades\DB;
```

- Altere o arquivo, adicionando o seguinte na function ```run()```:

```php
$techs = ['PHP', 'React', 'NodeJS', 'Delphi'];

foreach ($techs as $value) {
    DB::table('techs')->insert([
        'nome' => $value
    ]);
}
```

- Executar o comando:

```bash
php artisan db:seed --class=TechsTableSeeder
```

- Será inserido registros na tabela ```techs```

---

<h2>Criar o Model</h2>

- Executar o comando:

```bash
php artisan make:model Http\\Models\\Techs
```
- Será criado o Model ```Techs```, adicione o seguinte dentro da class:

```php
protected $table = 'techs';
protected $primaryKey = 'id';
protected $guarded = [];
```

---

<h2>A ideia do projeto</h2>

- Atualmente no projeto temos as 3 principais tabelas:

    - devs
    - posts
    - techs

- A tabela posts está relacionada com a tabela devs, ou seja cada dev pode ter um ou mais posts:

| Devs |  | |
|-----| -----|----|
| id  | nome | github_username |
|   1 | Nome1 | usuario1 |


| Posts | | | |
|-------| ------ | --- | --- |
| id | titulo | descricao | dev_id |
|   1 | Teste 1 | descr1...|  1 |
|   2 | Teste 2 | descr2...|  1 |


- A tabela ```techs``` foi criada. Qual a ideia?

- Relacionar a tabela ```devs``` com a tabela ```techs``` ou seja:

- Cada dev poderá dominar 1 ou mais techs.

- E

- Cada tech pode ser dominada por 1 ou mais devs

- ou seja temos uma relação de muitos para muitos.

- Vamos criar a tabela intermediaria mais a frente, antes vamos ver como ficará o relacionamento:

| Devs |  | |
|-----| -----|----|
| id  | nome | github_username |
|   1 | Nome1 | usuario1 |



| Devs Techs |  | |
|----|----|----|
| id | dev_id | tech_id |
|   1|   1 |  1|
|   2|   1 |  2|



| Techs  |      |
|--------|------|
|  id    | nome |
|    1   | NodeJS  |
|    2   | React  |


---

<h2>Agora que temos a ideia do relacionamento muitos para muitos, vamos criar a tabela intermediarai</h2>

- Executar a migration:

```bash
php artisan make:migration create_dev_techs_table --create=dev_techs
```

- Será criado mais um arquivo na pasta ```database/migrations```

- alterar a function ```up()``` desse arquivo:

```php
Schema::create('dev_techs', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->bigInteger('id_dev')->nullable()->unsigned();
    $table->bigInteger('id_tech')->nullable()->unsigned();
    $table->timestamps();

    $table->foreign('id_dev', 'fk_id_dev')->references('id')->on('devs')->onDelete('set null');

    $table->foreign('id_tech', 'fk_id_tech')->references('id')->on('techs')->onDelete('set null');
});
```

- Executar migration:

```bash
php artisan migrate
```

- Foi criado a tabela ```dev_techs```

---

<h2>Ajustar Models</h2>

- No model Techs adicionar a function:

```php
public function devs()
{

    // Parametros:
    // 1 - Classe do outro model principal, nao o intermediario e sim o principal
    // 2 - nome da tabela intermediaria
    // 3 - id correspondente na tabela intermediaria foreingkey do presente model nesse caso do Techs
    // 4 - id correspondente na tabela intermediaria foreingkey do outro model princiapl no caso do Devs

    return $this->belongsToMany('App\Http\Models\Devs', 'dev_techs', 'id_tech', 'id_dev');
}
```

- No model Devs, adicionar a seguinte function:

```php
public function techs()
{
    // Parametros:
    // 1 - Classe do outro model principal, nao o intermediario e sim o principal
    // 2 - nome da tabela intermediaria
    // 3 - id correspondente na tabela intermediaria foreingkey do presente model nesse caso do Devs
    // 4 - id correspondente na tabela intermediaria foreingkey do outro model princiapl no caso do Techs

    return $this->belongsToMany('App\Http\Models\Techs', 'dev_techs', 'id_dev', 'id_tech')->withPivot('status', 'id');
}
```

---

<h2>Criando o Controller</h2>

- Vamos criar o controller para trabalhar com os dois models:

```bash
php artisan make:controller DevTechsController --resource
```

- Será criado o controller DevTechsController

---

<h2>Implementando o controller DevTechsController</h2>

- Adicione a ```use```:

```php
use App\Http\Models\Devs;
use App\Http\Models\Techs;
```

- na function ```store()``` adicione o seguinte:

```php
$json = $request->only(['nome', 'github_username']);
$json_techs = $request->only(['techs']);

// Nao obrigatorio apenas um exemplo de consulta de techs pelo id.
$techs = Techs::whereIn('id', $json_techs['techs'])->get();
$devs = Devs::create($json);

$devs->techs()->attach($techs, ['status' => 'A']);
$devs->save();

return $devs;
```

---

<h2>Criar testes com o Postman</h2>

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: POST;
- Url: http://localhost:8000/dev-tech
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "nome": "Meu Nome",
    "github_username": "meu_github_username",
    "techs": ["1","2"]
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir os registros na tabela devs e na dev_techs.

---

<h2>Alteração</h2>

- Para melhor vermos como funciona a alteração, vamos adicionar o campo status na tabela ```dev_techs```:

```bash
php artisan make:migration add_status_to_dev_techs_table --table=dev_techs
```

- No arquivo que foi criado em ```database/migrations``` alterar o conteudo da function ```up()```

```php
Schema::table('dev_techs', function (Blueprint $table) {
    $table->string('status');
});
```

- alterar o conteudo da function ```down()```

```php
Schema::table('dev_techs', function (Blueprint $table) {
    $table->dropColumn('status');
});
```

- Executar a mirgation:

```bash
php artisan migrate 
```

- Será criado o campo ```status``` na tabela ```dev_techs```;

---

<h2>Realizar update de registro</h2>

- No controller ```DevTechsController``` adicionar a function ```update()```:

```php
//$techs = Techs::whereIn('id', [1])->get();
$json_techs = $request->only(['techs']);
//dd($json_techs['techs']);
$devs = Devs::find($id);
//$devs->techs()->syncWithoutDetaching([4 => ['status' => 'B']]);
$devs->techs()->syncWithoutDetaching($json_techs['techs']);
$devs->save();
return $devs;
```

---

<h2>Criar testes com o Postman</h2>

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: PUT;
- Url: http://localhost:8000/dev-tech/1
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "nome": "Meu Nome",
    "github_username": "meu_github_username",
    "techs": {"1": {"status": "A"},"2": {"status": "I" } }
}
```

- Clicar no botão SEND.

- E na base de dados deverá inserir os registros na tabela devs e na dev_techs.

---

<h2>Remover registros</h2>

- Adicionar a function ```destroy()```:

```php
$json_techs = request()->only(['techs']);
//$techs = Techs::whereIn('id', [1])->get();

//$devs = Devs::with(['devTechs.techs'])->get();
$devs = Devs::find($id);
$devs->techs()->detach($json_techs['techs']);
$devs->save();

return response()->json(['ok', 'Techs removidas.']);
```

---

<h2>Criar testes com o Postman</h2>

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: DELETE;
- Url: http://localhost:8000/dev-tech/1
- Na requisição na aba Body opção raw, Opção JSON ao invés de Text.
- No campo de texto adiconar:

```js
{
    "techs": ["1","2"]
}
```

- Clicar no botão SEND.

- E na base de dados deverá remover os registros na tabela dev_techs.

---

<h2>Listar registros</h2>

- Vamos prepara algumas situações antes, adicione no inicio da Class ```DevTechsController```:

```php
/**
 * Retorna os devs em que as techs dominadas vinculadas a ele
 * estejam com o status = a @param string $status
 */
private function getDevsTechsByStatus($status)
{
    // use ($status) : envia a variavel $status presente na funcao para a
    // funcao aninhada
    $devs = Devs::whereHas('techs', function ($query) use ($status) {
        return $query->where('status', $status);
    })->get();

    return $devs;
}

/**
 * Retorna os devs em que as techs dominadas vinculadas a ele
 * estejam com o status diferente a @param string $status
 */
private function getDevsTechsByNotStatus($status)
{
    $devs = Devs::whereDoesntHave('techs', function ($query) use ($status) {
        return $query->where('status', $status);
    })->get();

    return $devs;
}

/**
 * Retorna as techs dominada pelo dev informado, conforme o status tambem enviado
 * @param Devs $dev; Esperado: $dev = Devs::find(1);
 * @param string $status
 */
private function getComplexDevTechsByDev(Devs $dev, $status)
{
    return $dev->belongsToMany('App\Http\Models\Techs', 'dev_techs', 'id_dev', 'id_tech')
        ->withPivot('id', 'status')->wherePivot('status', $status)->get();
}

/**
 * Retorna as techs dominada pelo dev informado, conforme o status tambem enviado
 * @param Devs $dev; Esperado: $dev = Devs::find(1);
 * @param string $status
 */
private function getDevTechsByDev(Devs $dev, $status)
{
    return $dev->techs()->wherePivot('status', $status)->get();
}

/**
 * Retorna os devs e as techs dominada por ele
 */
private function getDevsWithTechs()
{
    $devs = Devs::with(['techs'])->get();
    return $devs;
}


/**
 * Retorna os devs e as techs dominada por ele filtrado
 */
private function getDevsWithTechsFilter($status)
{
    $devs = Devs::whereHas('techs', function ($query) use ($status) {
        return $query->where('status', $status);
    })->with(['techs' => function ($query) use ($status) {
        return $query->wherePivot('status', $status);
    }])->get();
    return $devs;
}
```

- Na function ```index()``` adicionar:

```php
$id = request('id');
//return $this->getDevsTechsByStatus('A');
//return $this->getDevsTechsByNotStatus('A');
//return $this->getComplexDevTechsByDev(Devs::find($id), 'A');
//return $this->getDevTechsByDev(Devs::find($id), 'A');
//return $this->getDevsWithTechs();
return $this->getDevsWithTechsFilter('A');
```

- Agora só descomentar e testar!!!

---

<h2>Criar testes com o Postman</h2>

- **Verifique se o servidor está rodando, do contrário execute:

```bash
php artisan serve
```

- No [Postman](https://www.postman.com/) criar a rota

- Metodo: GET;
- Url: http://localhost:8000/dev-tech?id=1

- Clicar no botão SEND.

- E derá retornar os registros.

---
