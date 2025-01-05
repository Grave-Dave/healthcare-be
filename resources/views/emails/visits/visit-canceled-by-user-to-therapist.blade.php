@extends('layouts.email')

@section('subject', 'Wizyta została odwołana')

@section('header')
    Wizyta została odwołana
@endsection

@section('content')
    <p>Bubeczku :*,</p>

    <p>Użytkownik <strong>{{ $patientName }}</strong> zrezygnował z następującej wizyty:</p>

    <p>Gabinet: <strong>{{ $location }}</strong></p>

    <p>Data: <strong>{{ $date }}</strong></p>

    <p>Godzina: <strong>{{ $time }}:00</strong></p>

    <p>Ale łobuz ;(</p>

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
