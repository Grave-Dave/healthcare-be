@extends('layouts.email')

@section('subject', 'Zmiana hasła')

@section('header')
    Zweryfikuj adres email
@endsection

@section('content')
    <p>Dzień dobry <strong>{{ $user->getUserFullName() }}</strong>,</p>

    <p>Dziękuję za rejestrację na mojej stronie. Proszę, kliknij na przycisk poniżej, aby zweryfikować Twój adres email:</p>

    @isset($verificationUrl)
        <p>
            <a href="{{ $verificationUrl }}" class="btn">Zweryfikuj email</a>
        </p>
    @endisset

    <p>Jeżeli to nie Ty utworzyłeś konto, zignoruj tą wiadomość.</p>
@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Katarzyna Trzeciakiewicz</p>
@endsection
