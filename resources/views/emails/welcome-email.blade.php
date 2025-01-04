@extends('layouts.email')

@section('subject', 'Zmiana hasła')

@section('header')
    Dzień dobry
@endsection

@section('content')
    <p>Dzień dobry <strong>{{ $user->getUserFullName() }}</strong>,</p>

    <p>Miło mi gościć Cię na mojej stronie. Poniżej kilka przydatnych informacji na początek: </p>

    <p>Jeżeli chcesz umówić się na wizytę...... </p>

    <p>Aktualnie przyjmuję w gabinetach...... </p>

@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Katarzyna Trzeciakiewicz</p>
@endsection
