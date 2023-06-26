<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <!-- <div>
        {{ $logo }}
    </div> -->

    <div style="position:relative;">
        <img src="{{ asset('logo.png') }}" style="max-width:250px;">
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
