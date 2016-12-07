<!doctype html>
<html ng-app="app">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic'>
    {{--<link rel="stylesheet" href="{{ asset('fonts.googleapis/SourceSansPro.css') }}">--}}
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    {{--<link rel="stylesheet" href="{{ asset('font-awesome/font-awesome.min.css') }}">--}}
    {{-- Ionicons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    {{--<link rel="stylesheet" href="{{ asset('ionicons/ionicons.min.css') }}">--}}
    {{-- datepicker --}}
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,500' rel='stylesheet' type='text/css'>
    {{--<link rel="stylesheet" href="{{ asset('fonts.googleapis/Roboto.css') }}">--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/8.9.1/styles/github.min.css" rel="stylesheet">
    {{--<link rel="stylesheet" href="{{ asset('highlight/github.min.css') }}">--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/angular-material/1.0.0-rc3/angular-material.min.css" rel="stylesheet" type="text/css"/>



    <title>SEEG Sms</title>
    {{-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries --}}
    {{-- WARNING: Respond.js doesn't work if you view the page via file:// --}}
    {{--[if lt IE 9]--}}
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    {{--[endif]--}}

    {{--wizard--}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-nav-wizard.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/angular-wizard/dist/angular-wizard.css') }}">
    {{--wizard-end--}}

    {{--datepicker--}}
    <link rel="stylesheet" href="{{ asset('bower_components/angular-material-datetimepicker/css/material-datetimepicker.css') }}">
    {{--<script src="plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>--}}
    {{--<script type="text/javascript" src={{ asset('/../node_modules/moment/moment.js') }}></script>--}}
    {{--<script type="text/javascript" src={{ asset('/../node_modules/angular/angular.js') }}></script>--}}
    {{--<script type="text/javascript" src={{ asset('/../node_modules/angular-date-time-input/src/js/dateTimeInput.js') }}></script>--}}
    {{--datepicker-end--}}

    {{--dropzone--}}
    <link rel="stylesheet" href="{{ asset('bower_components/dropzone/downloads/css/dropzone.css') }}">
</head>
<body route-bodyclass>
    <div class="wrapper">
        <div ui-view="layout"></div>
        <script src="{{ asset('js/vendor.js') }}"></script>
        <script src="{{ asset('js/partials.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        {{--material design--}}
        <script src="{{ asset('bower_components/angular-aria/angular-aria.js') }}"></script>
        <script src="{{ asset('bower_components/angular-animate/angular-animate.js') }}"></script>
        <script src="{{ asset('bower_components/angular-material/angular-material.min.js') }}"></script>
        <script src="{{ asset('bower_components/angular-messages/angular-messages.min.js') }}"></script>

        {{--datepicker--}}
        <script type="text/javascript" src="{{ asset('js/moment-with-locales.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/highlight.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('bower_components/angular-material-datetimepicker/beautifier.js') }}"></script>
        <script type="text/javascript" src="{{ asset('bower_components/angular-material-datetimepicker/js/angular-material-datetimepicker.js') }}"></script>
        {{--datepicker-end--}}

        <script type="text/javascript" src="{{ asset('bower_components/angular-wizard/dist/angular-wizard.js') }}"></script>

        {{--dropzone--}}
        <script type="text/javascript" src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('bower_components/dropzone/downloads/dropzone.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('bower_components/angular-dropzone/lib/angular-dropzone.js')}}"></script>

        {{--upload file--}}
        <script src="{{asset('bower_components/ng-file-upload/ng-file-upload-shim.min.js')}}"></script>
        <script src="{{asset('bower_components/ng-file-upload/ng-file-upload.min.js')}}"></script>

        {{--Accordion--}}
        {{--<script type="text/javascript" src="{{asset('IntegralUI_Studio_Web_v3.1.5/js/angular.integralui.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('IntegralUI_Studio_Web_v3.1.5/js/jquery.integralui.accordion.min.js')}}"></script>--}}

        <!-- For simple styling and transitions, include "angular-accordion.css". You can edit styles to meed your look and feel -->
        <link rel="stylesheet" type="text/css" href="{{asset('bower_components/ang-accordion/css/ang-accordion.css')}}">

        <!-- Then include "angular.js" and "angular-accordion.js" to your page -->
        {{--<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.15/angular.min.js"></script>--}}
        <script type="text/javascript" src="{{asset('bower_components/ang-accordion/js/ang-accordion.js')}}"></script>

        {{--DataTables Responsive--}}


        <div class="control-sidebar-bg"></div>
    </div>

    <script src="dist/js/app.js"></script>

</body>
</html>
