<?php
use App\Models\Employee;
function limpaString($string) {
    return $document_name = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($string)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));
}

?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mostrar Funcionário
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('employees.index', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
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
                                @if (Employee::findOrFail($employee->id)->employee_photo_path)
                                <div class="mt-2" x-show="! photoPreview">
                                    <img src="/storage/{{ Employee::findOrFail($employee->id)->employee_photo_path }}"
                                        alt="{{ Employee::findOrFail($employee->id)->name }}"
                                        class="rounded-full h-20 w-20 object-cover">
                                </div>
                                @else
                                <div class="mt-2" x-show="! photoPreview">
                                    <img src="{{ Employee::findOrFail($employee->id)->profile_photo_url }}"
                                        alt="{{ Employee::findOrFail($employee->id)->name }}"
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
                            {{ $employee->id }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome Completo
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $employee->name }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            CPF
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $employee->cpf }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Admissão
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $employee->admission }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cargo
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $employee->responsibility }}
                        </td>
                    </tr>
                    @foreach($responsibilities as $responsibility) @endforeach
                    @if($responsibility->documents)
                    @foreach(json_decode($responsibility->documents) as $document)
                    @foreach(json_decode($documents) as $db_document)
                    @if($db_document->id == $document)
                    <?php $document_name = $db_document->name ?>
                    @endif
                    @endforeach
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $document_name }}
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            <button type="button" id="{{ limpaString($document_name).'btn' }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                onclick="getDocument('{{ limpaString($document_name).'btn' }}', '{{ limpaString($document_name).'fl' }}')">

                                @if($document_paths->first())
                                @foreach($document_paths as $document_path)
                                @if(limpaString($document_path->type))
                                {{ $document_path->name }}
                                @endif
                                @endforeach
                                @else
                                Enviar!
                                @endif
                            </button>
                            @error('document')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </td>
                    </tr>
                    @endforeach
                    @endif

                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Setor
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $employee->sector }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Empresa
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $employee->company }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th scope="col"
                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status do Funcionário
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                            {{ $employee->active ? 'Ativo' : 'Inativo' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
