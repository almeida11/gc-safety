<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mostrar Cargo
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('responsibilities.index', $company_id) }}" class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
            </div>
            <div class="flex flex-col">
                <table class="min-w-full divide-y divide-gray-200 w-full">
                        <tr class="border-b">
                            <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                {{ $responsibility->id }}
                            </td>
                        </tr>
                    <tr class="border-b">
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $responsibility->name }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>