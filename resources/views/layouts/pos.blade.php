<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, width=device-width" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" href="favicon.ico">
        <title>{{ config('app.name', 'Inventory Booking System') }}</title>

        <!-- JS Mix -->
        <script src="{{ mix('js/manifest.js') }}"></script>
        <script src="{{ mix('js/vendor.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>

        <style>
            html {
                overflow: hidden;
            }
        </style>

    </head>

    <body>
        {{ $slot }}
    </body>
</html>