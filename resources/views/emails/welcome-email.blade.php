@extends('layouts.email')

@section('subject', 'Powitanie')

@section('header')
    Dzień dobry
@endsection

@section('content')
    <p>Dzień dobry <strong>{{ $user->getUserFullName() }}</strong>,</p>

    <p>Miło mi gościć Cię na mojej stronie. Poniżej kilka przydatnych informacji na początek: </p>

    <p>Jeżeli chcesz umówić się na wizytę, klinknij w przycisk <strong>UMÓW WIZYTĘ</strong> w aplikacji, a następnie wybierz interesujący Cię wolny termin w znajdującym się tam kalendarzu. </p>

    <p>Aktualnie przyjmuję w gabinetach przy ulicy Obornickiej, Legnickiej i Otmuchowskiej (więcej informacji w sekcji <strong>KONTAKT</strong>). </p>

    <p>Po zarezerwowaniu terminu, potwierdzę go, a Ty otrzymasz powiadomienie na ten adres email. </p>

    <p>Jeżeli masz jakieś pytania, możesz również skontaktować się ze mną telefonicznie. </p>

@endsection

@section('greetings')
    <p>Pozdrawiam, <br> Katarzyna Trzeciakiewicz</p>
@endsection
