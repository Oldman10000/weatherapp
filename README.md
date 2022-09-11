# Weather App

Use your IP address to retrieve a simple 5 day weather forecast using AccuWeather Api.

## How to install in your Laravel Project

Enter the below command in your project directory.

`composer require oldman10000/weather-app:^1.0.*`

This will install the package in your vendor directory.

At this point it is probably not a bad idea to clear your cache to make sure everything installs correctly.

`php artisan cache:clear`
`php artisan route:clear`
`php artisan view:clear`

It is also sensible to run the following command to make sure the migrations classes work correctly.

`composer dump-autoload`

As a failsafe, you can publish all the vendor files within your local project with the following command. This also allows you to overwrite code should you so choose.

`php artisan vendor:publish --provider="Oldman10000\WeatherApp\WeatherAppServiceProvider"`

It will be necessary anyway to publish the public folder which includes some CSS and icons.

`php artisan vendor:publish --tag=public --force`

You will need to create a database in SQL using the command.

`create database weatherapp;`

To migrate the new tables run the command:

`php artisan migrate`

## View the project

Test that the site is running correctly by starting up your development host

`php artisan serve`

Go to [localhost]/weather in your browser which will be the 'homepage' for this project. On a live site the input field will autofill with the client IP address but on a dev server there isn't much point to this as it will just show 127.0.0.1 localhost IP. You can find your IP address on [this website](https://whatismyipaddress.com/) or simply by googling it. When you enter the ip address you will be redirected to a page displaying the 5 day forecast for the location of your IP address. Results shown below for IP address 156.0.201.255 

![image](https://user-images.githubusercontent.com/73402591/189522092-5d9e0c9e-ba31-41d4-a7b7-8b52f0463656.png)

## Useful CLI commands

It is possible to add IP addresses to the database using the following command:

`php artisan create:insert-ipaddresses {ip address}`

Get weather data for your ip address directly in the CLI with

`php artisan get:weather-data {ip-address}`

## Improvements

- I noticed that the days returned by the API were sometimes incorrect, which appears to be due to the timezone. The result in the screenshot below was fetched from the API on the 11th September, however as can be seen the first date shown is the 10th September. The AccuWeather API collects weather data only a couple of times a day, once in the morning and once in the evening so it appears at the time that the API collected the data, it was still the 10th September locally (in the UK) but the 11th September in St Lucia as it is GMT+10 over there.

  ![image](https://user-images.githubusercontent.com/73402591/189522255-7acf00f8-18de-4f6c-b868-3d947dd36b96.png)
  
  This is a shortcoming of the API, there are however probably a couple of ways to fix the output here. The first thing that came to mind was to pull the 10 day forecast instead of the 5 day forecast as it currently does. Then Simply display the 5 days starting from the first date which matches 'today' locally.
