@extends('layouts.email')

@section('subject', 'Wizyta została zarezerwowana')

@section('header')
    Wizyta została zarezerwowana
@endsection

@section('content')
    <p>Dzień dobry <strong>{{ $userName }}</strong>,</p>

    <p>Następująca wizyta:</p>

    <p>Gabinet: <strong>{{ $location }}</strong></p>

    <p>Data: <strong>{{ $date }}</strong></p>

    <p>Godzina: <strong>{{ $time }}:00</strong></p>

    <p>została zarezerwowana i czeka na zatwierdzenie. Możesz podejrzeć jej status tutaj:</p>

    @isset($url)
        <p>
            <a href="{{ $url }}" class="btn">Moje wizyty</a>
        </p>
    @endisset

    <p>Potwierdzenie wizyty otrzymasz w osobnej wiadomości.</p>
@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Katarzyna Trzeciakiewicz</p>
@endsection
