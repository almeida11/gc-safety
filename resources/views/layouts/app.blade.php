<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="{{asset('logo.png')}}">

    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <style>
        footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            text-align: center;
        }

    </style>
    <script>
        function getInitial(name) {
            const strings = name.split(' ');
            const initials = strings
                .filter(string => string.toLowerCase() !== 'de' && string.toLowerCase() !== 'da' && string
                .toLowerCase() !== 'do' && string.toLowerCase() !== 'e')
                .map(string => string[0].toUpperCase())
                .join('');
            return initials;
        }

        function onlyNumberKey(evt) {
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }

        function getDocument(e) {
            let file = document.getElementById(e.id.slice(0, -2).concat('fl'));
            file.click();
        }

        function limitString (string = '', limit = 0) {  
            const fileExtension = string.split(".").pop();
            return string.substring(0, limit).concat('.').concat(fileExtension)
        }

        function changeName(e) {
            let modal_date_create = document.getElementById('modal_date_create');
            if (modal_date_create) {
                modal_date_create.classList.remove("hidden");
            }
            var url = e.value;
            let button = document.getElementById(e.id.slice(0, -2).concat('bt'));
            button.innerText = limitString(url.split(/(\\|\/)/g).pop(), 10);
        }

        function selecionarBox(evt, chkbox) {
            let chkBox = document.getElementById(chkbox);
            chkBox.click();
            if (chkBox.checked) {
                evt.innerText = 'NÃO EXIGIR!';
            } else {
                evt.innerText = 'EXIGIR!';
            }
        }

        function allowUpdate() {
            let modal_save_update = document.getElementById('modal_save_update');
            if (modal_save_update) {
                modal_save_update.classList.remove("hidden");
            }
        }

        function allowCreate() {
            let modal_save_create = document.getElementById('modal_save_create');
            if (modal_save_create) {
                modal_save_create.classList.remove("hidden");
            }
        }

        function fdeleteProfilePhoto() {
            let deleteProfilePhotoButton = document.getElementById('deleteProfile');
            deleteProfilePhotoButton.value = 'deleteProfilePhoto';
        }

        function orderBy(e, a) {
            let input1 = document.getElementById('order-companie');
            input1.value = e;
            let input2 = document.getElementById('method-companie');
            input2.value = a;
        }
    </script>
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

    </div>

    @stack('modals')

    <!-- <footer> <small>&copy; Copyright 2023, Gigante Sistemas</small> </footer> -->
    @livewireScripts
</body>

</html>
