<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', "What's On, Watson?") }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
<div id="app" class="app-container">
    <!-- call the chat component, include the google map API key  -->
    <chat-component :googleMapAPIKey="'{{ config('app.googleMapApiKey', '')}}'"></chat-component>
</div>

<!-- include JS files -->
<script src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
<script src="https://unpkg.com/vue-clock/dist/vue-clock.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/skycons.js') }}"></script>

</body>
</html>
