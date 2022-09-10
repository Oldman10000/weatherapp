@extends('layout\layout')

@section('content')
    <div class="content">

        <h2>Get a 5 day weather forecast</h2>

        <form action="/" method="POST">
            @csrf
            <label for="ip">Your Ip Address</label>
            <input type="text" name="ip" value="{{ $clientIp }}">
            </select>
            <input type="submit" value="submit">
        </form>

    </div>
@endsection
