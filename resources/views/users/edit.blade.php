<?php use App\Models\User;?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Usuário
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="post" action="{{ route('users.update', $user->id) }}">
                    <div class="flex flex-col">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <tr class="border-b">
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Foto
                                </th>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                        <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                                            <!-- Profile Photo File Input -->
                                            <input type="file" class="hidden"
                                                        wire:model="photo"
                                                        x-ref="photo"
                                                        x-on:change="
                                                                photoName = $refs.photo.files[0].name;
                                                                const reader = new FileReader();
                                                                reader.onload = (e) => {
                                                                    photoPreview = e.target.result;
                                                                };
                                                                reader.readAsDataURL($refs.photo.files[0]);
                                                        " />

                                            <x-label for="photo" value="{{ __('Foto') }}" />

                                            <!-- Current Profile Photo -->
                                            <div class="mt-2" x-show="! photoPreview">
                                                <img src="{{ User::findOrFail($user->id)->profile_photo_url }}" alt="{{ User::findOrFail($user->id)->name }}" class="rounded-full h-20 w-20 object-cover">
                                            </div>

                                            <!-- New Profile Photo Preview -->
                                            <div class="mt-2" x-show="photoPreview" style="display: none;">
                                                <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                                                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                                </span>
                                            </div>

                                            <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                                                {{ __('Select A New Photo') }}
                                            </x-secondary-button>

                                            @if (User::findOrFail($user->id)->profile_photo_path)
                                                <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                                                    {{ __('Remove Photo') }}
                                                </x-secondary-button>
                                            @endif

                                            <x-input-error for="photo" class="mt-2" />
                                        </div>
                                    @endif
                                    </td>
                            </tr>
                            @if ($editor->type == 'Cliente')
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
                                    Nome
                                </th>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="name" id="name"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.name" autocomplete="name"
                                        value="{{ old('name', $user->name) }}" />
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
                                    wire:model.defer="state.email" autocomplete="username" 
                                        value="{{ old('email', $user->email) }}" />
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
                                    Empresa
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="company" name="company"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                        wire:model="company">
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" {{ $user->company == $company->name ? 'selected' : '' }}>
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

                            <!-- <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Empresa
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="company" id="company" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.company" autocomplete="new-company" value="{{ old('company', $user->company) }}" />
                                    @error('company')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr> -->
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    E-mail Verificado Em
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ is_null($user->email_verified_at) ? 'Nunca' : $user->email_verified_at }}
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
                                            <option value="Prestador" {{ $user->type == 'Prestador' ? 'selected' : '' }}>
                                                Prestador
                                            </option>
                                            <option value="Fiscal" {{ $user->type == 'Fiscal' ? 'selected' : '' }}>
                                                Fiscal
                                            </option>
                                        </select>
                                        @error('type')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            @if ($editor->type == 'Cliente')
                                <tr class="border-b">
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status do Usuário
                                    </th>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                        <div class="col-span-6 sm:col-span-4">
                                            <select id="active" name="active"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="active">
                                                <option value='1'>
                                                    Ativo
                                                </option>
                                                <option value='0' {{ $user->active ? '' : 'selected' }}>
                                                    Inativo
                                                </option>
                                            </select>
                                            @error('active')
                                                <p class="text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @if ($editor->type == 'Prestador')
                                    <tr class="border-b">
                                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status do Usuário
                                        </th>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                            <div class="col-span-6 sm:col-span-4">
                                                <select id="active" name="active"
                                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                                wire:model="active">
                                                    <option value='1'>
                                                        Ativo
                                                    </option>
                                                    <option value='0' {{ $user->active ? '' : 'selected' }}>
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
                            @endif

                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuário Criado Em
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ $user->created_at }}
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuário Atualizado Em
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ $user->updated_at }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    @csrf
                    @method('put')
                    <div class="shadow overflow-hidden sm:rounded-md">
                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <div>
                                @error('erro')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>