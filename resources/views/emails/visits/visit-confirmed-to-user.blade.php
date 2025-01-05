@extends('layouts.email')

@section('subject', 'Wizyta została potwierdzona')

@section('header')
    Wizyta została potwierdzona
@endsection

@section('content')
    <p>Dzień dobry <strong>{{ $userName }}</strong>,</p>

    <p>Następująca wizyta:</p>

    <p>Gabinet: <strong>{{ $location }}</strong></p>

    <p>Data: <strong>{{ $date }}</strong></p>

    <p>Godzina: <strong>{{ $time }}:00</strong></p>

    <p>została potwierdzona. Możesz podejrzeć jej status tutaj:</p>

    @isset($url)
        <p>
            <a href="{{ $url }}" class="btn">Moje wizyty</a>
        </p>
    @endisset

    <p>Jeżeli masz jakieś pyatania odnośnie nadchodzącej wizyty, skontaktuj się ze mną telefonicznie.</p>
@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Katarzyna Trzeciakiewicz</p>
@endsection
