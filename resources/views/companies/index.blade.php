<?php use App\Models\Company; ?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Empresas
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if(Auth::user()->type == 'Cliente' || Auth::user()->type == 'Administrador' || $editor->company == null)
            <div class="block mb-8 mb-4">
                <a href="{{ route('companies.create') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Cadastrar Empresa</a>
                @if ($editor->tipo == 'Contratante')
                <a href="#" onclick="openModal()" 
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Convites</a>
                @endif
            </div>
            @endif

            <style>
                .td200 {
                    width:200px;
                }
            </style>

            <!--Search Bar-->
            <div class="relative">
                <form method="GET" action="{{ route('companies.index') }}">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 bg-gray-50" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input id="query-companie" name="query-companie" value="{{ $busca }}" type="search"
                        id="default-search"
                        class="mb-2 block w-full p-4 pl-10 text-sm text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-50 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500 text-gray-500 bg-gray-50"
                        placeholder="Pesquisar">
                    <button type="submit"
                        class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Pesquisar</button>
                </form>
            </div>
            <!--End Search Bar-->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
                        style="min-width: 100%;  nowrap;">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <thead>
                                    <tr>
                                        <form method="GET" action="{{ route('companies.index') }}">
                                            <input id="order-companie" name="order-companie" type="search"
                                                class="hidden">
                                            <input id="method-companie" name="method-companie" type="search"
                                                class="hidden">

                                            <th scope="col" width="50"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Foto
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <button type="submit"
                                                    class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                    onclick="orderBy('id', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                    ID
                                                    @if($orderby == 'id')
                                                    @if ($method == 'asc')
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M18.2929 15.2893C18.6834 14.8988 18.6834 14.2656 18.2929 13.8751L13.4007 8.98766C12.6195 8.20726 11.3537 8.20757 10.5729 8.98835L5.68257 13.8787C5.29205 14.2692 5.29205 14.9024 5.68257 15.2929C6.0731 15.6835 6.70626 15.6835 7.09679 15.2929L11.2824 11.1073C11.673 10.7168 12.3061 10.7168 12.6966 11.1073L16.8787 15.2893C17.2692 15.6798 17.9024 15.6798 18.2929 15.2893Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @else
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg" stroke="">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @endif
                                                    @endif
                                                </button>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <button type="submit"
                                                    class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                    onclick="orderBy('name', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                    Nome Fantasia
                                                    @if($orderby == 'name')
                                                    @if ($method == 'asc')
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M18.2929 15.2893C18.6834 14.8988 18.6834 14.2656 18.2929 13.8751L13.4007 8.98766C12.6195 8.20726 11.3537 8.20757 10.5729 8.98835L5.68257 13.8787C5.29205 14.2692 5.29205 14.9024 5.68257 15.2929C6.0731 15.6835 6.70626 15.6835 7.09679 15.2929L11.2824 11.1073C11.673 10.7168 12.3061 10.7168 12.6966 11.1073L16.8787 15.2893C17.2692 15.6798 17.9024 15.6798 18.2929 15.2893Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @else
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg" stroke="">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @endif
                                                    @endif
                                                </button>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <button type="submit"
                                                    class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                    onclick="orderBy('cnpj', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                    CNPJ
                                                    @if($orderby == 'cnpj')
                                                    @if ($method == 'asc')
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M18.2929 15.2893C18.6834 14.8988 18.6834 14.2656 18.2929 13.8751L13.4007 8.98766C12.6195 8.20726 11.3537 8.20757 10.5729 8.98835L5.68257 13.8787C5.29205 14.2692 5.29205 14.9024 5.68257 15.2929C6.0731 15.6835 6.70626 15.6835 7.09679 15.2929L11.2824 11.1073C11.673 10.7168 12.3061 10.7168 12.6966 11.1073L16.8787 15.2893C17.2692 15.6798 17.9024 15.6798 18.2929 15.2893Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @else
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg" stroke="">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @endif
                                                    @endif
                                                </button>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <button type="submit"
                                                    class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                    onclick="orderBy('manager', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                    Gerente
                                                    @if($orderby == 'manager')
                                                    @if ($method == 'asc')
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M18.2929 15.2893C18.6834 14.8988 18.6834 14.2656 18.2929 13.8751L13.4007 8.98766C12.6195 8.20726 11.3537 8.20757 10.5729 8.98835L5.68257 13.8787C5.29205 14.2692 5.29205 14.9024 5.68257 15.2929C6.0731 15.6835 6.70626 15.6835 7.09679 15.2929L11.2824 11.1073C11.673 10.7168 12.3061 10.7168 12.6966 11.1073L16.8787 15.2893C17.2692 15.6798 17.9024 15.6798 18.2929 15.2893Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @else
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg" stroke="">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @endif
                                                    @endif
                                                </button>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <button type="submit"
                                                    class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                    onclick="orderBy('tipo', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                    Tipo de Empresa
                                                    @if($orderby == 'tipo')
                                                    @if ($method == 'asc')
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M18.2929 15.2893C18.6834 14.8988 18.6834 14.2656 18.2929 13.8751L13.4007 8.98766C12.6195 8.20726 11.3537 8.20757 10.5729 8.98835L5.68257 13.8787C5.29205 14.2692 5.29205 14.9024 5.68257 15.2929C6.0731 15.6835 6.70626 15.6835 7.09679 15.2929L11.2824 11.1073C11.673 10.7168 12.3061 10.7168 12.6966 11.1073L16.8787 15.2893C17.2692 15.6798 17.9024 15.6798 18.2929 15.2893Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @else
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg" stroke="">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @endif
                                                    @endif
                                                </button>
                                            </th>
                                            @if ($editor->type == 'Cliente' || $editor->type == 'Administrador')
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <button type="submit"
                                                    class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                    onclick="orderBy('ativo', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                    Status
                                                    @if($orderby == 'ativo')
                                                    @if ($method == 'asc')
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M18.2929 15.2893C18.6834 14.8988 18.6834 14.2656 18.2929 13.8751L13.4007 8.98766C12.6195 8.20726 11.3537 8.20757 10.5729 8.98835L5.68257 13.8787C5.29205 14.2692 5.29205 14.9024 5.68257 15.2929C6.0731 15.6835 6.70626 15.6835 7.09679 15.2929L11.2824 11.1073C11.673 10.7168 12.3061 10.7168 12.6966 11.1073L16.8787 15.2893C17.2692 15.6798 17.9024 15.6798 18.2929 15.2893Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @else
                                                    <svg class="ml-1" width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg" stroke="">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                                                fill="#6B7280"></path>
                                                        </g>
                                                    </svg>
                                                    @endif
                                                    @endif
                                                </button>
                                            </th>
                                            @endif
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Documentos
                                            </th>
                                            <th scope="col" width="200" class="px-6 py-3 bg-gray-50">

                                            </th>
                                        </form>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($companies != null)
                                    @foreach ($companies as $company)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                            <button
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                @if (Company::findOrFail($company->id)->company_photo_path)
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                    src="/storage/{{ Company::findOrFail($company->id)->company_photo_path }}"
                                                    alt="{{ Company::findOrFail($company->id)->name }}" />
                                                @else
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                    src="{{ Company::findOrFail($company->id)->profile_photo_url }}"
                                                    alt="{{ Company::findOrFail($company->id)->name }}" />
                                                @endif
                                            </button>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->id }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->name }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->cnpj }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                            echo mb_strimwidth($company->manager, 0, 20, "...");
                                            @endphp
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->tipo }}
                                        </td>
                                        @if ($editor->type == 'Cliente' || $editor->type == 'Administrador')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->ativo ? 'Ativo' : 'Inativo' }}
                                        </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php $texto_status = 'Documentos completos!'; ?>
                                            @if($companies_doc_status)
                                            @foreach ( $companies_doc_status as $status )
                                            @if($company->id == $status['id'])
                                            <?php $texto_status = 'Documentos pendentes!'; ?>
                                            @endif
                                            @endforeach
                                            @endif
                                            {{ $texto_status }}
                                        </td>
                                        <td class=" py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('companies.show', $company->id) }}"
                                                class="mb-2 mr-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded">Verificar</a>
                                            @if(Auth::user()->type != 'Fiscal')
                                            <a href="{{ route('companies.edit', $company->id) }}"
                                                class="mb-2 mr-2 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-2 rounded">Editar</a>
                                            @if ($editor->tipo == 'Contratante' || $editor->type == 'Administrador')
                                            <form class="inline-block"
                                                action="{{ route('companies.destroy', $company->id) }}" method="POST"
                                                onsubmit="return confirm('Você tem certeza?');">
                                                <input type="hidden" name="_method" value="DELETE">
                                                @csrf
                                                <input type="submit"
                                                    class="mb-2 mr-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-2 rounded"
                                                    value="Desativar">
                                            </form>
                                            @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if(isset($companies))
                            {{ $companies->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(Auth::user()->type == 'Cliente' || Auth::user()->type == 'Administrador')
    @if ($editor->tipo == 'Contratante' || $editor->company == null || Auth::user()->type == 'Administrador')
        <div class="main-modal fixed w-full h-100 inset-0 z-50 overflow-hidden flex justify-center items-center animated fadeIn faster"
                    style="background: rgba(0,0,0,.7);">
            <div
                class="border border-teal-500 shadow-lg modal-container bg-white   mx-auto rounded shadow-lg z-50 overflow-y-auto">
                <div class="div500 modal-content py-4 text-left px-6">
                    <!--Title-->
                    <div class="flex justify-between items-center pb-3">
                        <p class="text-2xl font-bold mr-3">Convites</p>
                        <div class="modal-close cursor-pointer z-50 ml-3">
                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 18 18">
                                <path
                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <!--Body-->
                    <div class="my-5">
                        <form method="post" action="{{ route('createInvite', [$editor->id_company]) }}">
                            @csrf
                            <table id="modal-table" class="min-w-full divide-gray-200 w-full">
                                <tr class="border-b">
                                    <th colspan='1' scope="col" class="mr-2 ml-2 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                        ID
                                    </th> 
                                    <th colspan='1' scope="col" class="mr-2 ml-2 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                        Criado por
                                    </th> 
                                    <th colspan='1' scope="col" class="mr-2 ml-2 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                        Usado pelo usuário
                                    </th> 
                                    <th colspan='1' scope="col" class="mr-2 ml-2 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                        Usado pela empresa
                                    </th> 
                                    <th colspan='1' scope="col" class="mr-2 ml-2 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                        Código de convite
                                    </th> 
                                    <th colspan='1' scope="col" class="mr-2 ml-2 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                        Status
                                    </th> 
                                </tr>
                                @foreach ($invites as $invite)
                                    <tr class="border-b">
                                        <td class="td200 text-center py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200">
                                            <p>{{ $invite->id }}</p>
                                        </td>
                                        <td class="td200 text-center py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200">
                                            @foreach($users as $user)
                                                @if($user->id == $invite->id_owner)
                                                    <p>{{ mb_strimwidth($user->name, 0, 20, "...") }}</p>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="td200 text-center py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200"> 
                                            <?php $utilized_by_user = false; ?>
                                            @foreach($users as $user)
                                                @if($user->id == $invite->used_by_user)
                                                    <?php $utilized_by_user = true; ?>
                                                    <p>{{ mb_strimwidth($user->name, 0, 20, "...") }}</p>
                                                @endif
                                            @endforeach
                                            @if(!$utilized_by_user)
                                                <p>Não utilizado.</p>
                                            @endif
                                        </td>
                                        <td class="td200 text-center py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200">
                                            <?php $utilized_by_company = false; ?>
                                            @foreach($companies as $company)
                                                @if($company->id == $invite->used_by_company)
                                                    <?php $utilized_by_company = true; ?>
                                                    <p>{{ mb_strimwidth($company->name, 0, 20, "...") }}</p>
                                                @endif
                                            @endforeach
                                            @if(!$utilized_by_company)
                                                <p>Não utilizado.</p>
                                            @endif
                                        </td>
                                        <td class="td200 text-center py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200">
                                            <p>{{ $invite->invite_code }}</p>
                                        </td>
                                        <td class="td200 text-center py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200">
                                            <p>{{ $invite->status }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <!--Footer-->
                        <div class="flex justify-end pt-2">
                            @if(isset($invites))
                                {{ $invites->links() }}
                            @endif
                            @error('document_manager')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button
                                class="focus:outline-none modal-close px-4 bg-gray-400 p-3 rounded-lg text-black hover:bg-gray-300 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                                Criar novo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @endif
    <script>
        const modal = document.querySelector('.main-modal');
        const closeButton = document.querySelectorAll('.modal-close');
        
        const modalClose = () => {
            modal.classList.remove('fadeIn');
            modal.classList.add('fadeOut');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 500);
        }

        const openModal = () => {
            modal.classList.remove('fadeOut');
            modal.classList.add('fadeIn');
            modal.style.display = 'flex';
        }

        for (let i = 0; i < closeButton.length; i++) {
            const elements = closeButton[i];
            elements.onclick = (e) => modalClose();
            modal.style.display = 'none';
            window.onclick = function (event) {
                if (event.target == modal) modalClose();
            }
        }
        @if(isset($_GET['new_invite']) || isset($_GET['invites']))
            openModal();
        @endif
    </script>
</x-app-layout>
