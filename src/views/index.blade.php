@extends('layout\layout')

@section('content')
    <div class="content">

        <form action="/weather" method="POST">
            @csrf
            <label for="ip">Enter your Ip address below</label>
            <input type="text" name="ip" value="{{ $clientIp }}">
            </select>
            <input type="submit" value="Submit">
        </form>

    </div>
@endsection
