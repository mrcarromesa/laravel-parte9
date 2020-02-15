<h1>Laravel parte 3</h1>

<strong>Referências:</strong>

- [Validation](https://laravel.com/docs/6.x/validation)

- Criar Form Request Validation, executar o seguinte comando:

```bash
php artisan make:request ValidarDev
```

- Isso irá criar o arquivo ```app/Http/Requests/ValidarDev```

---

- Adicionar ```use`` no arquivo:

```php
use Illuminate\Contracts\Validation\Validator; // para implementar a function: failedValidation() que ao invés de redirecionar retornará uma resposta json
use Illuminate\Http\Exceptions\HttpResponseException; // para retornar a resposta em json que sera implementada na function faile
```

- Alterar o return da função ```authorize()``` para:

```php
/**
 * Se o método authorize retornar false, uma resposta HTTP com um código de status 403 será retornada automaticamente e o método do controlador não será executado.
 * Se você planeja ter lógica de autorização em outra parte do seu aplicativo, retorne true no método authorize:
 * return true;
 */

return true;
```

- Adicionar a function ```rules()``` :

```php
$id = $this->route('id');

return [
    'github_username' => 'required|unique:devs,github_username,' . $id . '|max:191',
    'nome' => 'required',
];
```

** As chaves do array (```github_username => ...``` e ```nome => ... ```) devem corresponder aos campos que será enviados pela requisição nos metodos POST e PUT **

** Os valores de cada chave correspondem a validação de cada campo:

| Regra | O que significa |
|-------|-----------------|
| ```required```| Campo obrigatório, deve ser informado na requisição e não pode ser vazio|
| ```unique``` | O valor do campo deve ser unico na tabela, essa regra possuí mais parametros:  <br /><b>1 -</b> Nome da tabela; <br /> <b>2 -</b> Nome da coluna na tabela do qual seu valor deverá ser unico; <br /> <b>3 -</b> Exceção, no caso quando alteramos o registro queremos que seja verificado se o valor informado é diferente de todos os outros exceto ao registro que possuí o id que estamos alterando; <br> Mais detalhes na [Documentação do Laravel](https://laravel.com/docs/6.x/validation#rule-unique) |
| ```max``` | Deve informar o valor máximo permitido para o campo ex.: ```max:191```, irá validar se o campo possuí no máximo essa quantidade de caracteres |

- Outras regras disponíveis em: [Available Validation Rules](https://laravel.com/docs/6.x/validation#available-validation-rules)


---

<h2>Personalizar as mensagens de erro</h2>

- Adicionar a seguinte função no arquivo ```ValidarDev.php```:

```php
public function messages()
{
    return [
        'github_username.unique'
        => 'username já cadastrado por outro usuário',
        'github_username.required' => 'Campo obrigatório',
        'github_username.max' => 'O campo deverá conter no máximo :max',
        'nome.required' => 'Campo obrigatório',
    ];
}
```

- Como isso funciona?:

- Ele é um array de ```chave => valor```, onde a chave é composta pelo, nome do campo que queremos validar, mais "```.```" e em seguida a regra que receberá a mensagem caso o erro ocorra, estrutura:

```php
'nome_do_meu_campo.regra' => 'Minha mensagem personalizada',
```

---

<h2>Mais um ajuste</h2>

- Por padrão o Form Request Validation irá redirecionar para rota que fez a requisição reportando os erros, porém quando trabalhamos com REST API, esse padrão não serve, precisamos alterar o padrão, para isso implementamos a função ```failedValidation``` no nosso arquivo, dessa forma:

```php
// Não esqueça de adicionar as uses:
/*
use Illuminate\Contracts\Validation\Validator; // para implementar a function: failedValidation() que ao invés de redirecionar retornará uma resposta json
use Illuminate\Http\Exceptions\HttpResponseException; // para retornar a resposta em json que sera implementada na function faile
*/

protected function failedValidation(Validator $validator)
{
    // retorna o Json com os erros de validação e resposta de status 400 (Bad request)
    throw new HttpResponseException(response()
        ->json($validator->errors(), 400));
}
```

- Ok o arquivo ```ValidarDev.php```, está pronto, agora vamos ajustar o controller.

---

<h2>Ajuste no controller DevController</h2>

- Adicionar a use:
```php
use App\Http\Requests\ValidarDev;
```

- Ajustar a função ```public function store(Request $request)``` para:
```public function store(ValidarDev $request)```

- Ajustar a função ```public function update(Request $request, $id)``` para:
```public function update(ValidarDev $request, $id)```

- Pronto, agora só testar e nossa validação estará funcionando.
