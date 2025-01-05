@extends('layouts.email')

@section('subject', 'Umówiono nową wizytę')

@section('header')
    Umówiono nową wizytę
@endsection

@section('content')
    <p>Bubeczku :*,</p>

    <p>Użytkownik <strong>{{ $patientName }}</strong> zarezerwował następującą wizytę:</p>

    <p>Gabinet: <strong>{{ $location }}</strong></p>

    <p>Data: <strong>{{ $date }}</strong></p>

    <p>Godzina: <strong>{{ $time }}:00</strong></p>

    <p>Wizyta czeka na zatwierdzenie:</p>

    @isset($url)
        <p>
            <a href="{{ $url }}" class="btn">Wizyty</a>
        </p>
    @endisset

    <p>Możesz skontaktować się z pacjentem pod numerem: <strong>{{ $patientPhone }}</strong></p>
@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Dawid :*</p>
@endsection
