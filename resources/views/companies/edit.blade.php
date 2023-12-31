<?php use App\Models\Company; ?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Empresa
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('companies.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
                <a href="{{ route('employees.index', $company->id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Funcionários</a>
                <a href="{{ route('responsibilities.index', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Cargos</a>
                <a href="{{ route('sectors.index', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Setores</a>
                @if(Auth::user()->type != 'Usuário')
                @if ($editor->tipo == 'Contratante')
                <a href="{{ route('documents.index', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Tipos de Documentos</a>
                @endif
                @endif
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="post" action="{{ route('companies.update', $company->id) }}"
                    enctype="multipart/form-data">
                    <div class="flex flex-col">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <tr class="border-b">
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Foto
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <div x-data="{photoName: null, photoPreview: null}"
                                        class="col-span-6 sm:col-span-4">
                                        <!-- Profile Photo File Input -->
                                        <input type="file" class="hidden" wire:model="photo" x-ref="photo"
                                            name="company_photo_path" x-on:change="
                                                                photoName = $refs.photo.files[0].name;
                                                                const reader = new FileReader();
                                                                reader.onload = (e) => {
                                                                    photoPreview = e.target.result;
                                                                };
                                                                reader.readAsDataURL($refs.photo.files[0]);
                                                        " />

                                        <x-label for="photo" value="{{ __('Foto') }}" />

                                        <!-- Current Profile Photo -->

                                        @if (Company::findOrFail($company->id)->company_photo_path)
                                        <div class="mt-2" x-show="! photoPreview">
                                            <img src="/storage/{{ Company::findOrFail($company->id)->company_photo_path }}"
                                                alt="{{ Company::findOrFail($company->id)->name }}"
                                                class="rounded-full h-20 w-20 object-cover">
                                        </div>
                                        @else
                                        <div class="mt-2" x-show="! photoPreview">
                                            <img src="{{ Company::findOrFail($company->id)->profile_photo_url }}"
                                                alt="{{ Company::findOrFail($company->id)->name }}"
                                                class="rounded-full h-20 w-20 object-cover">
                                        </div>
                                        @endif

                                        <!-- New Profile Photo Preview -->
                                        <div class="mt-2" x-show="photoPreview" style="display: none;">
                                            <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                                                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                            </span>
                                        </div>

                                        <x-secondary-button class="mt-2 mr-2" type="button"
                                            x-on:click.prevent="$refs.photo.click()">
                                            {{ __('Select A New Photo') }}
                                        </x-secondary-button>

                                        @if (Company::findOrFail($company->id)->company_photo_path)
                                        <x-secondary-button type="button" class="mt-2" onclick="fdeleteProfilePhoto()"
                                            wire:click="deleteProfilePhoto">
                                            {{ __('Remove Photo') }}
                                        </x-secondary-button>

                                        <input type="text" id="deleteProfile" name="deleteProfilePhoto" class="hidden">
                                        @endif

                                        @error('foto')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror

                                        <x-input-error for="photo" class="mt-2" />
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ $company->id }}
                                </td>
                            </tr>

                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Razão Social
                                </th>

                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="razao_social" id="razao_social"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.razao_social" autocomplete="razao_social"
                                        value="{{ old('razao_social', $company->razao_social) }}" />
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
                                        wire:model.defer="state.name" autocomplete="name"
                                        value="{{ old('name', $company->name) }}" />
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
                                        wire:model.defer="state.atividade_principal" autocomplete="atividade_principal"
                                        value="{{ old('atividade_principal', $company->atividade_principal) }}" />
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
                                        wire:model.defer="state.cnae" autocomplete="cnae"
                                        value="{{ old('cnae', $company->cnae) }}" />
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
                                        wire:model.defer="state.cnpj" autocomplete="cnpj"
                                        value="{{ old('cnpj', $company->cnpj) }}"
                                        onkeypress="$(this).mask('00.000.000/0000-00')"
                                        onkeypress="return onlyNumberKey(event)"
                                        pattern="[0-9]{2}.[0-9]{3}.[0-9]{3}\/[0-9]{4}-[0-9]{2}$" />
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
                                        wire:model.defer="state.endereco" autocomplete="endereco"
                                        value="{{ old('endereco', $company->endereco) }}" />
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
                                        wire:model.defer="state.bairro" autocomplete="bairro"
                                        value="{{ old('bairro', $company->bairro) }}" />
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
                                        wire:model.defer="state.cep" autocomplete="cep"
                                        value="{{ old('cep', $company->cep) }}" onkeypress="$(this).mask('00.000-000')"
                                        onkeypress="return onlyNumberKey(event)" pattern="[0-9]{2}.[0-9]{3}-[0-9]{3}" />
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
                                        wire:model.defer="state.cidade" autocomplete="cidade"
                                        value="{{ old('cidade', $company->cidade) }}" />
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
                                        wire:model.defer="state.telefone" autocomplete="telefone"
                                        value="{{ old('telefone', $company->telefone) }}"
                                        onkeypress="$(this).mask('(00)0000-00009')"
                                        onkeypress="return onlyNumberKey(event)"
                                        pattern="\([0-9]{2}\)[0-9]{4}-[0-9]{4,5}" />
                                    @error('telefone')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            @if (($editor->type == 'Cliente' && $editor->tipo == 'Contratante') || $editor->type == 'Administrador')
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status da Empresa
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="active" name="active"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="active">
                                            <option value='1'>
                                                Ativa
                                            </option>
                                            <option value='0' {{ $company->ativo ? '' : 'selected' }}>
                                                Inativa
                                            </option>
                                        </select>
                                        @error('active')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo da Empresa
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="active" name="active"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                            wire:model="active">
                                            @if ($editor->type == 'Administrador')
                                            <option value='1'>
                                                Contratante
                                            </option>
                                            @endif
                                            <option value='0' {{ $company->tipo == 'Contratada' ? 'selected' : '' }}>
                                                Contratada
                                            </option>
                                        </select>
                                        @error('active')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
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
                                            <option value="{{ $user->id }}"
                                                {{ $company->id_manager == $user->id ? 'selected' : '' }}>
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
                            @else
                            @if ($editor->is_manager == 1)
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
                                            <option value="{{ $user->id }}"
                                                {{ $company->id_manager == $user->id ? 'selected' : '' }}>
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
                            @endif
                            @endif
                        </table>
                    </div>
                    @csrf
                    @method('put')
                    @error('erro')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="shadow overflow-hidden sm:rounded-md">
                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <div>
                                @error('erro')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
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
