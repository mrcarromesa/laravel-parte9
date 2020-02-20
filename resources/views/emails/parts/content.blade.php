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