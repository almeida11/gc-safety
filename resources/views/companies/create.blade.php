<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cadastrar Empresa
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('companies.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="post" action="{{ route('companies.store')}}">
                    <div class="flex flex-col">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Razao Social
                                </th>

                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="razao_social" id="razao_social"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.razao_social" autocomplete="razao_social" />
                                    @error('razao_social')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome Fantasia
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="name" id="name"
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
                                    Atividade Principal
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="atividade_principal" id="atividade_principal"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.atividade_principal"
                                        autocomplete="new-atividade_principal" />
                                    @error('atividade_principal')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CNAE
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="cnae" id="cnae"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.cnae" autocomplete="new-cnae" />
                                    @error('cnae')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CNPJ
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="cnpj" id="cnpj"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.cnpj" autocomplete="new-cnpj" />
                                    @error('cnpj')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Endereço
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="endereco" id="endereco"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.endereco" autocomplete="new-endereco" />
                                    @error('endereco')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bairro
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="bairro" id="bairro"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.bairro" autocomplete="new-bairro" />
                                    @error('bairro')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CEP
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="cep" id="cep"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.cep" autocomplete="new-cep"
                                        onkeypress="$(this).mask('00.000-000')" onkeypress="return onlyNumberKey(event)"
                                        pattern="[0-9]{2}.[0-9]{3}-[0-9]{3}" />
                                    @error('cep')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cidade
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="cidade" id="cidade"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.cidade" autocomplete="new-cidade" />
                                    @error('cidade')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Telefone
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="telefone" id="telefone"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.telefone" autocomplete="new-telefone"
                                        onkeypress="$(this).mask('(00)0000-00009')"
                                        onkeypress="return onlyNumberKey(event)"
                                        pattern="\([0-9]{2}\)[0-9]{4}-[0-9]{4,5}" />
                                    @error('telefone')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>

                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Gerente
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="id_manager" name="id_manager"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="id_manager">
                                            <option hidden>
                                                Selecione um Gerente!
                                            </option>
                                            @foreach ($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('id_manager')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            @if ($editor->type == 'Administrador')
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status da Empresa
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="ativo" name="ativo"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="ativo">
                                            <option value='1' selected>
                                                Ativa
                                            </option>
                                            <option value='0'>
                                                Inativa
                                            </option>
                                        </select>
                                        @error('ativo')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @if ($editor->type == 'Administrador')
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo de Empresa
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="tipo" name="tipo"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="tipo">
                                            <option value='Contratante' selected>
                                                Contratante
                                            </option>
                                            <option value='Contratada'>
                                                Contratada
                                            </option>
                                        </select>
                                        @error('tipo')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            @endif
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
