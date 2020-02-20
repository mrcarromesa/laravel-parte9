<h1>Laravel parte 9</h1>

<strong>Referências:</strong>

- [How to send mail using queue in Laravel 5.7?](https://www.itsolutionstuff.com/post/how-to-send-mail-using-queue-in-laravel-57example.html)

---

- Criar Class para envio de e-mail:

```bash
php artisan make:mail SendEmailDevs
```

- Será criado o arquivo `Mail\SendEmailDevs`

- Altere o conteúdo da class para o seguinte:

```php
use Queueable, SerializesModels;
private $devs = [];

/**
 * Create a new message instance.
 *
 * @return void
 */
public function __construct($devs)
{
    $this->devs = $devs;
}

/**
 * Build the message.
 *
 * @return $this
 */
public function build()
{
    return $this->view('emails.parts.content', ['dev' => $this->devs]);
}
```

- crie a view `resources/views/emails/devs.blade.php`

- E adicione o seguinte conteudo:

```html
<!DOCTYPE html>
<html>

<head>
    <title>How to send mail using queue in Laravel 5.7? - ItSolutionStuff.com</title>
</head>

<body>
    @yield('content')
</body>

</html>
```

- crie a view `resources/views/emails/parts/content.blade.php` adicione o seguinte:

```html
@extends('emails.devs')
@section('content')
<center>
    <h2 style="padding: 23px;background: #b3deb8a1;border-bottom: 6px green solid;">
        Novo Dev
    </h2>
</center>

<p>Nome: {{$dev['nome']}}</p>
<p>Git: {{$dev['github_username']}}</p>

@include('emails.parts.footer')
@endsection
```

- crie a view `resources/views/emails/parts/footer.blade.php` adicione o seguinte:

```html
<i>E-mail gerado via sistema</i>
```
- Como já configuramos os dados de envio de e-mail conforme [Laravel parte 7](https://github.com/mrcarromesa/laravel-parte7) no tópico `Prepara envio de e-mail`, vc já deve ter o arquivo `.env` configurado para o envio de e-mails

---

<h2>Criação da tabela de filas</h2>

- No arquiv `.env` altere o valor da variavel `QUEUE_CONNECTION` para que as filas sejam criadas utilizando a base de dados:

```
QUEUE_CONNECTION=database
```

- Gerando tabelas, execute o comando:

```bash
php artisan queue:table
```

- Criando as tabelas, execute o comando:

```bash
php artisan migrate
```

- Criando o JOB:

- Execute o comando:

```bash
php artisan make:job SendEmailJob
```

- Será criado o arquivo `app/Jobs/SendEmailJob.php`, altere ele para:

```php

namespace App\Jobs;

use App\Mail\SendEmailDevs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $devs = [];
    private $details = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($devs, $details)
    {
        $this->devs = $devs;
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $email = new SendEmailDevs($this->devs);
        Mail::to($this->details['email'])->send($email);
    }
}

```

<h2>Ajuste no observe</h2>

- Em `App\Observers\DevObserver` adicione a `use` `use App\Jobs\SendEmailJob;`


- Em `App\Observers\DevObserver` ajuste a function: `created(Devs $devs)` adicionando o seguinte:

```php
$details['email'] = 'your_email@gmail.com';
//dd($devs->toArray());
SendEmailJob::dispatch($devs->toArray(), $details);
```

- Antes de testar vamos garantir que as informações do arquivo `.env` não estejam em cache, execute os comandos:

```bash
php artisan config:cache
```

- E também:

```bash
php artisan config:clear
```

- Agora teste cadastrando um novo dev!
