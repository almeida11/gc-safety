<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Usuários
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if(Auth::user()->type != 'Usuário')
                <div class="block mb-8 sm:px-6 lg:px-8 mb-4">
                    <a href="{{ route('companies.create') }}" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">Cadastrar Empresa</a>
                </div>
            @endif
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8" style="min-width: 100%; white-space: nowrap;">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 w-full">
                                <thead>
                                <tr>
                                <th scope="col" width="50" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Razao Social
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome Fantasia
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Atividade Principal
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        CNPJ
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gerente
                                    </th>
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50">

                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($companies as $company)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->id }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->razao_social }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->nome_fantasia }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->atividade_principal }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $company->cnpj }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @foreach ($users as $user)
                                                {{ $user->id == $company->id_manager ? $user->name : '' }}
                                            @endforeach
                                        </td>

                                        <td class=" py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('companies.show', $company->id) }}" class="mb-2 mr-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded">Verificar</a>
                                            
                                                <a href="{{ route('companies.edit', $company->id) }}" class="mb-2 mr-2 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-2 rounded">Editar</a>
                                                <form class="inline-block" action="{{ route('companies.index') }}" method="POST" onsubmit="return confirm('Você tem certeza?');">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    @csrf
                                                    <input type="submit" class="mb-2 mr-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-2 rounded" value="Desativar">
                                                </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>