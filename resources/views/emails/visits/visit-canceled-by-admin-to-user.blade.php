@extends('layouts.email')

@section('subject', 'Wizyta została odwołana')

@section('header')
    Wizyta została odwołana
@endsection

@section('content')
    <p>Dzień dobry <strong>{{ $userName }}</strong>,</p>

    <p>Następująca wizyta:</p>

    <p>Gabinet: <strong>{{ $location }}</strong></p>

    <p>Data: <strong>{{ $date }}</strong></p>

    <p>Godzina: <strong>{{ $time }}:00</strong></p>

    <p>została odwołana przez terapeutę.</p>

    <p>Jeżeli chcesz umówić się na inną wizytę, możesz to zrobić tutaj:</p>

    @isset($url)
        <p>
            <a href="{{ $url }}" class="btn">Umów wizytę</a>
        </p>
    @endisset

    <p>Jeżeli masz jakieś pytania możesz skontaktować się ze mną telefonicznie.</p>
@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Katarzyna Trzeciakiewicz</p>
@endsection
