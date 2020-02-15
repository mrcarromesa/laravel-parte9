<h1>Laravel parte 2</h1>

<strong>Referências:</strong>

- [Controllers](https://laravel.com/docs/6.x/controllers)

- Criar Controller, executar o seguinte comando:

```bash
php artisan make:controller DevController --resource
```

- Isso irá criar o arquivo ```app/Http/Controllers/DevController```

---

<h2>Metodos e funções</h2>

| Metodo  |  Função  | Quando usar |
| ------------------- | ------------------- |------------------- |
|  GET |  ```index()```, ```create()```, ```show()```, ```edit()``` | ```index()```:  Listar/ Consultar registros, ```create()```: Obter formulário para inserir registro, ```show()```: mostrar unico registro, ```edit()```: Obter formulário para editar registro |
|  POST |  ```store()``` | enviar dados para inserir novo registro |
|  PUT |  ```update()``` | enviar dados e id para alterar registro |
|  DELETE |  ```destroy()``` | enviar id para remover registro |

---

<h2>Preenchendo metodos</h2>

- Antes de iniciar adicionar a ```use```:

```php
use App\Http\Models\Devs;
```

- No ```index()``` inserir o seguinte: 

```php
    // irá retornar todos os registros da base de dados.
    $devs = Devs::all();
    return $devs;
```

- No ```store()``` inserir o seguinte: 

```php
    // obtem os dados enviados via post
    $json = $request->json()->all();

    // insere o registro na base
    $devs = Devs::create($json);
    $devs->save();

    // retorna o registro
    return $devs;
```

- No ```update()``` inserir o seguinte:

```php
    // obtendo os dados enviados via metodo PUT
    $json = $request->only(['nome', 'github_username']);

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
```

- No ```delete()``` inserir o seguinte:

```php
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
```

- Os demais metodos não serão implemetados;

- No arquivo ```routes/web.php``` adicionar a seguinte rota:

```php
/*
o primeiro parametro de resource 'dev' precisa ser o mesmo
que o primeiro parametro de 'parameters()'
e o segundo parametro de parameters, preferenciamente poderá ser 'id'
*/

Route::resource('dev', 'DevController')->parameters([
    'dev' => 'id'
]);

```

- No postman alterar em todas as rotas de ```devs```, para ```dev```

- Ex.: ao invés de ```http://localhost:8000/devs```, alterar para ```http://localhost:8000/dev```

- E testar tudo deverá está funcionando normalmente.
