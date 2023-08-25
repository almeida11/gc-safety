<?php use App\Models\Company; ?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mostrar Empresa
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
            <div class="flex flex-col">
                <table class="min-w-full divide-y divide-gray-200 w-full">
                    <tr class="border-b">
                        <th
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Foto
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                                <!-- Profile Photo File Input -->
                                <input type="file" class="hidden" wire:model="photo" x-ref="photo" x-on:change="
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->id }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Razão Social
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->razao_social }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome Fantasia
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->name }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Atividade Principal
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->atividade_principal }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            CNAE
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->cnae }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            CNPJ
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->cnpj }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Endereço
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->endereco }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bairro
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->bairro }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            CEP
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->cep }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cidade
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->cidade }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Telefone
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->telefone }}
                        </td>
                    </tr>
                    @if ($editor->type == 'Administrador')
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status da Empresa
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->ativo ? 'Ativa' : 'Inativa' }}
                        </td>
                    </tr>
                    @endif
                    @if ($editor->type == 'Administrador' or $editor->type == 'Moderador')
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo de Empresa
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->tipo }}
                        </td>
                    </tr>
                    @endif
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Gerente
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                            @if($user->id == $company->id_manager)
                            {{ ($user->name) }}
                            @endif
                            @endforeach
                        </td>
                    </tr>

                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            E-mail do Gerente
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                            @if($user->id == $company->id_manager)
                            {{ ($user->email) }}
                            @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Empresa Criado Em
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->created_at }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Empresa Atualizado Em
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $company->updated_at }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
