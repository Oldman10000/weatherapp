@extends('layout\layout')

@section('content')
    <div class="content">

        <h2>Your 5 day weather forecast</h2>

        @dump($weatherData)

    </div>
@endsection
