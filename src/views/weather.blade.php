@extends('layout\layout')

@section('content')
    <div class="content">
        <h2>Results shown in local time</h2>
        <div class="weather__data">
            @foreach ($weatherData->DailyForecasts as $forecast)
                <div class="weather__data__day">
                    <div class="weather__data__day__date">{{ date('j F, Y', strtotime($forecast->Date)) }}</div>
                    <img src="/img/icons/{{ $forecast->Day->Icon }}.svg" alt="{{ $forecast->Day->IconPhrase }}" height="100"
                        width="100">
                    <div class="weather__data__day__phrase">{{ $forecast->Day->IconPhrase }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
