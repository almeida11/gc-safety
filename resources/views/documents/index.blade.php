<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Documentos
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

            <div class="block mb-8 mb-4">
                <a href="{{ route('companies.show', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Empresa</a>
                @if(Auth::user()->type != 'Fiscal')
                @if ($editor->tipo == 'Contratante')
                <a href="{{ route('documents.create', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Cadastrar Documentos</a>
                @endif
                @endif
            </div>

            </form>
            <!--Search Bar-->
            <div class="relative">
                <form method="GET" action="{{ route('documents.index', $company_id) }}">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 bg-gray-50" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input name="query-documents" value="{{ $busca }}" type="search" id="default-search"
                        class="mb-2 block w-full p-4 pl-10 text-sm text-black border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-50 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500 text-gray-500 bg-gray-50"
                        placeholder="Pesquisar">
                    <button type="submit"
                        class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Pesquisar</button>
                </form>
            </div>
            <!--End Search Bar-->
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
                    style="min-width: 100%;  nowrap;">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <thead>
                                <tr>
                                    <form method="GET" action="{{ route('documents.index', $company_id) }}">
                                        <input id="order-companie" name="order-companie" type="search"
                                            class="hidden">
                                        <input id="method-companie" name="method-companie" type="search"
                                            class="hidden">
                                        <th scope="col" width="50" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <button type="submit"
                                                class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                onclick="orderBy('name', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                Nome
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
                                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <button type="submit"
                                                class="flex items-center justify-center bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                onclick="orderBy('company', '<?php echo $method == 'asc' ? 'desc' : 'asc'; ?>')">
                                                Empresa
                                                @if($orderby == 'company')
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
                                        <th scope="col" width="200" class="px-6 py-3 bg-gray-50">

                                        </th>
                                    </form>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($documents as $document)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $document->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $document->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $document->company }}
                                    </td>
                                    <td class=" py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('documents.show', [$company_id, $document->id]) }}"
                                            class="mb-2 mr-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded">Verificar</a>
                                        @if(Auth::user()->type != 'Fiscal')
                                        <a href="{{ route('documents.edit', [$company_id, $document->id]) }}"
                                            class="mb-2 mr-2 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-2 rounded">Editar</a>

                                        <form class="inline-block"
                                            action="{{ route('documents.destroy', [$company_id, $document->id]) }}"
                                            method="POST" onsubmit="return confirm('Você tem certeza?');">
                                            <input type="hidden" name="_method" value="DELETE">
                                            @csrf
                                            <input type="submit"
                                                class="mb-2 mr-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-2 rounded"
                                                value="Desativar">
                                        </form>

                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
