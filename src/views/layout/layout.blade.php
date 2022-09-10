<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Weather App</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="/css/main.css" rel="stylesheet">
</head>

<body>
    <main>
        @yield('content')
    </main>
    <footer>
        <p>&copy; Patrick Oldman {{ date('Y') }}</p>
    </footer>
</body>

</html>
