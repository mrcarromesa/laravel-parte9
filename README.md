<h1>Laravel parte 8</h1>

<strong>Referências:</strong>

- [Observers](https://laravel.com/docs/6.x/eloquent#observers)
- [Observer BelongsToMany pivot table](https://github.com/laravel/nova-issues/issues/944)


---

<h2>Observers para Model normal</h2>

- Criar observer:

```bash
php artisan make:observer DevObserver --model=Http\\Models\\Devs
```

- Será criada um arquivo `app/Observers/DevObserver.php`, adicione a function `created()`:

```php
Log::alert('created ' . $devs->toJson());
```

- Adicione a `use` nesse arquivo:

```php
use Illuminate\Support\Facades\Log;
```

- Para utilizar o observer altere no arquivo `app/Providers/AppServiceProvider`:

```php
use App\Http\Models\Devs;
use App\Observers\DevObserver;
```

- Na function `boot()` adicione:

```php
Devs::observe(DevObserver::class);
```

- Tudo pronto agora quando realizar um create de um novo dev, irá gerar um log no arquivo `laravel.log`

---

<h2>Com tabela Pivot é um pouco diferente</h2>

- Crie o Model: `app/Http/Models/Pivot/DevTechs`:

```bash
php artisan make:model Http\\Models\\Pivot\\DevTechs  
```

- Será criado o arquivo `app/Http/Models/Pivot/DevTechs`, altere o conteúdo para:

```php
namespace App\Http\Models\Pivot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DevTechs extends Pivot
{
    public function create()
    {
        event('eloquent.creating: ' . __CLASS__, $this);

        parent::create();

        event('eloquent.created: ' . __CLASS__, $this);
    }

    public function update(array $attributes = [], array $options = [])
    {
        event('eloquent.updating: ' . __CLASS__, $this);

        parent::update();

        event('eloquent.updated: ' . __CLASS__, $this);
    }

    public function delete()
    {
        event('eloquent.deleting: ' . __CLASS__, $this);

        parent::delete();

        event('eloquent.deleted: ' . __CLASS__, $this);
    }
}
```

- No model `Devs`, realize essa substitua o conteúdo da função `techs()`:

```php
return $this->belongsToMany('App\Http\Models\Techs', 'dev_techs', 'id_dev', 'id_tech')
->withPivot('status', 'id')
->using('App\Http\Models\Pivot\DevTechs');
```

- A diferença aqui é a adição da function `->using('App\Http\Models\Pivot\DevTechs')`

- Criar o observer para o pivot:

```bash
php artisan make:observer DevTechsObserver --model=Http\\Models\\Pivot\\DevTechs
```

- Será criado o arquivo `app/Observers/DevTechsObserver`, altere a function `created()`:

```php
Log::alert('pivot ok => ' . $devTechs->toJson());
```

- Altere a function `updated()` para:

```php
Log::alert('pivot ok UPd => ' . $devTechs->toJson());
```

- Não esqueça de adicionar a `use`:

```php
use App\Http\Models\Pivot\DevTechs;
use Illuminate\Support\Facades\Log;
```

- Quase pronto, agora no arquivo `Providers/AppServiceProvider` adicione a `use`:

```php
use App\Http\Models\Pivot\DevTechs;
use App\Observers\DevTechsObserver;
```

- Na function `boot()` adicione o seguinte:

```php
DevTechs::observe(DevTechsObserver::class);
```

- Pronto agora qualquer criação/alteração feita na tabela dev_techs, através do devs será gerado log.
