<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Cargos
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if(Auth::user()->type != 'Usuário')
                <div class="block mb-8 mb-4">
                    <a href="{{ route('companies.show', $company_id) }}" class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Empresa</a>
                    <a href="{{ route('responsibilities.create', $company_id) }}" class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Cadastrar Cargo</a>
                </div>
            @endif
            </form>
                <!--Search Bar-->
                <!-- <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="search" id="default-search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Mockups, Logos..." required>
                    <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                </div> -->
                <!--End Search Bar-->
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8" style="min-width: 100%;  nowrap;">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <thead>
                                <tr>
                                    <th scope="col" width="50" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Empresa
                                    </th>
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50">

                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($responsibilities as $responsibility)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $responsibility->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $responsibility->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $responsibility->company }}
                                        </td>
                                        <td class=" py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('responsibilities.show', [$company_id, $responsibility->id]) }}" class="mb-2 mr-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded">Verificar</a>
                                            @if(Auth::user()->type != 'Usuário')
                                                <a href="{{ route('responsibilities.edit', [$company_id, $responsibility->id]) }}" class="mb-2 mr-2 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-2 rounded">Editar</a>
                                                
                                                    <form class="inline-block" action="{{ route('responsibilities.destroy', [$company_id, $responsibility->id]) }}" method="POST" onsubmit="return confirm('Você tem certeza?');">
                                                        <input type="hidden" name="_method" value="DELETE" >
                                                        @csrf
                                                        <input type="submit" class="mb-2 mr-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-2 rounded" value="Desativar">
                                                    </form>
                                                
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                {{ $responsibilities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>