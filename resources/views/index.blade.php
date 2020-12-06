<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nutriclock</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ URL::asset('css/styles.css') }}">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap4.min.css">
        <style>
            html, body {
                background-image:url({{url('images/background.jpg')}});
                color: #FFF;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                width:100vw;
                margin: 0;
            }

            #app{
                height: 100%;
                width:100%;
            }
        </style>
    </head>
    <body>
        <div id="app">
            <div v-show="this.$store.state.user">
                <topbar></topbar>
                <sidebar></sidebar>
            </div>
            <router-view></router-view>
        </div>
        <script src="js/app.js"></script>
    </body>
</html>
