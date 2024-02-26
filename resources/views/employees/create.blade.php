<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cadastrar Funcionário
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('employees.index', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="post" action="{{ route('employees.store', $company_id)}}">
                    <div class="flex flex-col">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>

                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="name" id="name" value="{{ old("name") }}"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.name" autocomplete="name" />
                                    @error('name')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CPF
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="cpf" id="cpf" value="{{ old("cpf") }}"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.cpf" autocomplete="username"
                                        onkeypress="$(this).mask('000.000.000-00')"
                                        onkeypress="return onlyNumberKey(event)"
                                        pattern="[0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2}$" />
                                    @error('cpf')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admissão
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="date" name="admission" id="admission" value="{{ old("admission") }}" min="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' -50 year')) }}" max="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' +1 year')) }}" 
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.admission" autocomplete="new-admission" />
                                    @error('admission')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cargo
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="id_responsibility" name="id_responsibility"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="id_responsibility">
                                            @foreach($responsibilities as $responsibility)
                                            <option value="{{ $responsibility->id }}"  {{ old('id_responsibility') == $responsibility->id ? 'selected' : '' }}>
                                                {{ $responsibility->name }}
                                            </option>
                                            @endforeach

                                        </select>
                                        @error('id_responsibility')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Setor
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="id_sector" name="id_sector"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="id_sector">
                                            @foreach($sectors as $sector)
                                            <option value="{{ $sector->id }}"  {{ old('id_sector') == $sector->id ? 'selected' : '' }}>
                                                {{ $sector->name }}
                                            </option>
                                            @endforeach

                                        </select>
                                        @error('id_sector')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Empresa
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="id_company" name="id_company"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="id_company">
                                            @foreach($companies as $company)
                                            <option value="{{ $company->id }}"  {{ old('id_company') == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                            @endforeach

                                        </select>
                                        @error('id_company')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status do Funcionário
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="active" name="active"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="active">
                                            <option value='1' {{ old('active') == 1 ? 'selected' : '' }}>
                                                Ativo
                                            </option>
                                            <option value='0' {{ old('active') == 0 ? 'selected' : '' }}>
                                                Inativo
                                            </option>
                                        </select>
                                        @error('active')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @csrf
                    <div class="shadow overflow-hidden sm:rounded-md">

                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                            @error('erro')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                                Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
