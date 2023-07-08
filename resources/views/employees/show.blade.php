<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mostrar Funcionário
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('employees.index') }}" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">Voltar a Lista</a>
            </div>
            <div class="flex flex-col">
                <table class="min-w-full divide-y divide-gray-200 w-full">
                    @if ($editor->type == 'Administrador')
                        <tr class="border-b">
                            <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                {{ $user->id }}
                            </td>
                        </tr>
                    @endif
                    <tr class="border-b">
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome Completo
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            <!--employees.name-->
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Admissão
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            <!--employees.admission-->
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cargo
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            <!--employees.responsibility-->
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Setor
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            <!--employees.sector-->
                            {{ is_null($user->company) ? 'Não Configurado' : $user->company }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Empresa
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ is_null($user->company) ? 'Não Configurado' : $user->company }}
                        </td>
                    </tr>
                    @if ($editor->type == 'Administrador')
                        <tr class="border-b">
                            <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status do Funcionário
                            </th>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                {{ $user->active ? 'Ativo' : 'Inativo' }}
                            </td>
                        </tr>
                    @endif
                    <tr class="border-b">
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Funcionário Atualizado Em
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $user->updated_at }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>