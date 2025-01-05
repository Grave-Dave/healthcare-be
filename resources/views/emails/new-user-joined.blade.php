@extends('layouts.email')

@section('subject', 'Nowy użytkownik')

@section('header')
    Nowy użytkownik
@endsection

@section('content')
    <p>Siemanko,</p>

    <p>Nowy użytkownik: <strong>{{ $newUserName }}</strong>, </p>

    <p>zarejestrował się i właśnie zweryfikował swój adres email.</p>

    <p>To super wiadomość!</p>

    <p>🎉🎉🎉</p>

@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Dawid</p>
@endsection
