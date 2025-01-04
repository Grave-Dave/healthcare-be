@extends('layouts.email')

@section('subject', 'Zmiana hasła')

@section('header')
    Zmiana hasła
@endsection

@section('content')
    <p>Dzień dobry <strong>{{ $user->getUserFullName() }}</strong>,</p>

    <p>Otrzymaliśmy prośbę o zmianę Twojego hasła. Aby zresetować hasło, kliknij na poniższy przycisk:</p>

    @isset($resetUrl)
        <p>
            <a href="{{ $resetUrl }}" class="btn">Zresetuj hasło</a>
        </p>
    @endisset

    <p>Jeżeli to nie Ty chesz zmienić hasło, zignoruj tą wiadomość.</p>
@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Katarzyna Trzeciakiewicz</p>
@endsection
