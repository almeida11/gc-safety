<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cadastrar Usuário
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="post" action="{{ route('users.store')}}">
                    <div class="flex flex-col">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="name" id="name"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.name" autocomplete="name"/>
                                    @error('name')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    E-mail
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="email" name="email" id="email"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.email" autocomplete="email"/>
                                    @error('email')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Senha
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="password" name="password" id="password" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.password" autocomplete="new-password" />
                                    @error('password')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Confirmar Senha
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.password" autocomplete="new-password" />
                                    @error('password')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Empresa
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="company" name="company"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                        wire:model="company">
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" @if ($editor->company == $company->name) selected @endif>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                           
                                        </select>
                                        @error('company')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo de Usuário
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="type" name="type"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                        wire:model="type">
                                            @if (Auth::user()->type == 'Cliente')
                                                <option value="Cliente">
                                                    Cliente
                                                </option>
                                            @endif
                                            <option value="Prestador">
                                                Prestador
                                            </option>
                                            <option value="Fiscal" selected>
                                                Fiscal
                                            </option>
                                        </select>
                                        @error('type')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            @if ($editor->type == 'Administrador')
                                <tr class="border-b">
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status do Usuário
                                    </th>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                        <div class="col-span-6 sm:col-span-4">
                                            <select id="active" name="active"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="active">
                                                <option value='1' selected>
                                                    Ativo
                                                </option>
                                                <option value='0'>
                                                    Inativo
                                                </option>
                                            </select>
                                            @error('active')
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
                            <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                                Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>