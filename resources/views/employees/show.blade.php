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
            <style>
                .animated {
                    -webkit-animation-duration: 1s;
                    animation-duration: 1s;
                    -webkit-animation-fill-mode: both;
                    animation-fill-mode: both;
                }

                .animated.faster {
                    -webkit-animation-duration: 500ms;
                    animation-duration: 500ms;
                }

                .fadeIn {
                    -webkit-animation-name: fadeIn;
                    animation-name: fadeIn;
                }

                .fadeOut {
                    -webkit-animation-name: fadeOut;
                    animation-name: fadeOut;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }

                    to {
                        opacity: 1;
                    }
                }

                @keyframes fadeOut {
                    from {
                        opacity: 1;
                    }

                    to {
                        opacity: 0;
                    }
                }
                
                .td200 {
                    width:200px;
                }

                .div500 {
                    width:500px;
                }
                .table_info {
                    height: 100%;
                    display: flex;
                    flex-direction: row;
                    flex-wrap: nowrap;
                    justify-content: space-evenly;
                }

                .div1000 {
                    width:1600px;
                }

                .modal-left {
                    position: relative;
                }

                .modal-right {
                    border-radius: 1px;
                    box-shadow: 0px 0px 0px 2px rgba(0,0,0,0.3);
                }

                .save_button {
                    padding-top: .5rem;
                    padding-bottom: .5rem;
                    float: right;
                    bottom: 0;
                    right: 0;
                    display: flex;
                    flex-wrap: nowrap;
                    align-items: center;
                    justify-content: space-between;
                }

                .modal-size-pdf {
                    width: 1100px;
                    height: 100%;
                    text-align: center;
                    -webkit-box-shadow: 10px 10px 29px -4px rgba(0,0,0,0.49);
                    -moz-box-shadow: 10px 10px 29px -4px rgba(0,0,0,0.49);
                    box-shadow: 10px 10px 29px -4px rgba(0,0,0,0.49);
                }

                .modal_body {
                    height: 80vh;
                }

                .text-reset {
                    margin: 0;
                    padding: 0;
                    border: 0;
                    font-size: 100%;
                    font: inherit;
                    vertical-align: baseline;
                }
            </style>
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
                    
                    @if($employee->documents)
                                            @foreach(json_decode($employee->documents) as $document)
                                                @foreach(json_decode($documents) as $db_document)
                                                    @if($db_document->name == $document)
                                                    <?php $document_name = $db_document->name;
                                                            $check_doc = false; ?>
                                                    @endif
                                                @endforeach
                                                <tr class="border-b">
                                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ $document_name }}
                                                    </th>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                        
                                                    <button onclick="openModal2('{{ $document_name }}')" type="button" id="{{ limpaString($document_name).'btn' }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2">
                                                        <?php $document_name_display = 'Enviar!' ?>
                                                        @if($document_paths->first())
                                                            @foreach($document_paths as $document_path)
                                                                @if($document_path->actual == 1)
                                                                    @if(limpaString($document_path->type))
                                                                        @if(limpaString($document_path->type) == limpaString($document_name))
                                                                            <?php $document_name_display = $document_path->name;
                                                                                $document_path_display = $document_path->path;
                                                                                $check_doc = true;  ?>
                                                                                
                                                                            @break
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        
                                                        {{ $document_name_display }}
                                                    </button>
                                                        @error('document'.$document_name)
                                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
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
    
    <div class="main-modal3 fixed w-full h-100 inset-0 z-100 overflow-hidden flex justify-center items-center animated fadeIn faster"
                style="background: rgba(0,0,0,.7);">
                <div
                    class="border border-teal-500 shadow-lg modal-container bg-white   mx-auto rounded shadow-lg z-50 overflow-y-auto">
                    <div class="div500 modal-content py-4 text-left px-6">
                        <!--Title-->
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold mr-3">Descrição da Recusa</p>
                            <div class="modal-close3 cursor-pointer z-50 ml-3">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 18 18">
                                    <path
                                        d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <!--Body-->
                        <div class="my-5">
                            <div>
                                <div class="mt-1">
                                    <textarea id="disapproveDesc" name="description" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Descrição..."></textarea>
                                </div>

                            </div>
                        </div>
                        <!--Footer-->
                        <div class="flex justify-end pt-2"> 
                            @error('document_manager')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button onclick="disapproveDocModal(this)" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
    <div class="main-modal2 fixed w-full h-100 inset-0 z-50 overflow-hidden flex justify-center items-center animated fadeIn faster"
                style="background: rgba(0,0,0,.7);">
                <div
                    class="border border-teal-500 shadow-lg modal-container bg-white   mx-auto rounded shadow-lg z-50 overflow-y-auto">
                    <div class="div1000 modal-content py-4 text-left px-6">
                        <!--Title-->
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold mr-3" id="modal-title2">Title</p>
                            <div class="modal-close2 cursor-pointer z-50 ml-3">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 18 18">
                                    <path
                                        d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <!--Body-->
                        <div class="my-5 modal_body">
                            <form class="table_info"  method="post" action="{{ route('updatedoc', [$company_id, $employee->id]) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-left">
                                    <table id="modal-table" class="min-w-full divide-y divide-gray-200 w-full">
                                        <tr class="border-b hidden"  id="modal_doc_info">
                                            <th colspan='2' scope="col" class=" py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Dados do Documento
                                            </th> 
                                        </tr>
                                        <tr class="border-b hidden" id="modal_date_info">
                                            <th scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Data de Vencimento
                                            </th> 
                                            <td class="td200  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                    <input type="date" name="old_due_date" id="old_due_date" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' +2 year')) }}" 
                                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                                        wire:model.defer="state.old_due_date" autocomplete="old_due_date" value="" onchange="allowUpdate()" />
                                                <div>
                                                    <!-- Input file -->
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_status_info">
                                            <th scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Status
                                            </th> 
                                            <td id="modal-status" class="td200  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_save_update">
                                            <th colspan='2' scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider bg-white">
                                                <div class="pb-2 pt-2 save_button">
                                                    @error('document_uploader')
                                                        <p id="document_uploader" class="text-reset text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                    <button id="save_button"
                                                        class="ml-2 focus:outline-none modal-close2 px-4 bg-gray-400 p-3 rounded-lg text-black hover:bg-gray-300 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">Salvar</button>
                                                        
                                                </div>
                                            </th> 
                                        
                                        </tr>
                                        <tr class="border-b hidden" id="sended-by-info">
                                            <th scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Enviado por
                                            </th> 
                                            <td id="sended-by" class="td300  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="aproved-by-info">
                                            <th id="aproved-by-text" scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                
                                            </th> 
                                            <td id="aproved-by" class="td300  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="desc-info">
                                            <th scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Descrição
                                            </th> 
                                            <td id="desc" class="td300  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                
                                            </td>
                                        </tr>
                                        @if(Auth::user()->type != 'Fiscal')
                                            <tr class="border-b hidden"  id="modal_aprove_title">
                                                <th colspan='2' scope="col" class=" py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                    Aprovar Documento
                                                </th> 
                                            </tr>
                                            <tr class="border-b hidden" id="modal_aprove_text">
                                                <th scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                    <button type="button" id="2" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                                    onclick="approveDoc(this)" >
                                                        Aprovar
                                                    </button>
                                                    <div>
                                                        <input type="text" name="approve" id="approve" value="" class="hidden"/>
                                                        <textarea id="disapproveDescription" name="disapproveDescription" class="hidden"></textarea>
                                                    </div>
                                                </th>
                                                <td class="td200  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200">
                                                    <button type="button" id="1" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                                    onclick="disapproveDoc(this)" >
                                                        Recusar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                        @if(Auth::user()->type != 'Fiscal') 
                                        <tr class="border-b">
                                            <th colspan='2' scope="col" class=" py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Enviar novo Documento
                                            </th> 
                                        </tr>
                                        <tr class="border-b">
                                            <th scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Documento 
                                            </th> 
                                            <td class="td200  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-gray-200">
                                                <button type="button" id="1" class="modal-form-button inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                                onclick="getDocument(this)" >
                                                    Enviar!
                                                </button>
                                                <div>
                                                    <input type="file" name="1" id="1" class="modal-form-input hidden" onchange="changeName(this)"
                                                    
                                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b" id="modal_date_create">
                                            <th scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Data de Vencimento
                                            </th> 
                                            <td class="td200  py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                    <input type="date" name="new_due_date" id="new_due_date" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' +2 year')) }}" 
                                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                                        wire:model.defer="state.new_due_date" autocomplete="new_due_date" value="" onchange="allowCreate()"  />
                                                <div>
                                                    <!-- Input file -->
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_save_create">
                                            <th colspan='2' scope="col" class=" py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider bg-white">
                                                <div class="pb-2 pt-2 save_button">
                                                    @error('document_uploader')
                                                        <p id="document_uploader" class="text-reset text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                    <button id="save_button"
                                                        class="ml-2 focus:outline-none modal-close2 px-4 bg-gray-400 p-3 rounded-lg text-black hover:bg-gray-300 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">Salvar</button>
                                                        
                                                </div>
                                            </th> 
                                        
                                        </tr>
                                        @endif
                                        <tr class="border-b hidden" id="modal_historic">
                                            <th colspan='2' scope="col" class=" py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-white">
                                                Histórico de Envios
                                            </th> 
                                        </tr>
                                    </table>

                                    <input type="text" name="modal_type" id="modal_type" class="hidden" />

                                    <!--Footer-->
                                    
                                </div>
                                
                                <div class="modal-right" id="modal-div-obj">
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>

    <script>

        const modal2 = document.querySelector('.main-modal2');
        const closeButton2 = document.querySelectorAll('.modal-close2');
        
        const modal3 = document.querySelector('.main-modal3');
        const closeButton3 = document.querySelectorAll('.modal-close3');

        const title2 = document.getElementById('modal-title2');
        const object2 = document.getElementById("modal-object");
        const old_due_date = document.getElementById("old_due_date");
        const modal_status = document.getElementById("modal-status");
        const sended_by = document.getElementById("sended-by");
        const aproved_by = document.getElementById("aproved-by");
        const aproved_by_text = document.getElementById("aproved-by-text");
        const desc = document.getElementById("desc");
        const save_button = document.getElementById("save_button");
        const modal_doc_info = document.getElementById("modal_doc_info");
        const modal_date_info = document.getElementById("modal_date_info");
        const modal_status_info = document.getElementById("modal_status_info");
        const sended_by_info = document.getElementById("sended-by-info");
        const aproved_by_info = document.getElementById("aproved-by-info");
        const desc_info = document.getElementById("desc-info");
        const modal_historic = document.getElementById("modal_historic");
        @if(Auth::user()->type != 'Fiscal') const new_due_date = document.getElementById("new_due_date");
        const modal_save_create = document.getElementById("modal_save_create");
        const modal_date_create = document.getElementById("modal_date_create");
        const modal_aprove_text = document.getElementById("modal_aprove_text");
        const modal_aprove_title = document.getElementById("modal_aprove_title"); @endif
        const disapproveDesc = document.getElementById("disapproveDesc");
        const disapproveDescription = document.getElementById("disapproveDescription");

        const paths = [@if($employee->documents)
@foreach(json_decode($employee->documents) as $document)
@foreach(json_decode($documents) as $db_document)
@if($db_document->name == $document)
@if($document_paths->first())
@foreach($document_paths as $document_path)
@if($document_path->actual == 1)
@if(limpaString($document_path->type))
@if(limpaString($document_path->type) == limpaString($db_document->name))
<?php $document_name_display = $document_path->name;
$document_path_display = $document_path->path; ?>
<?php echo htmlspecialchars_decode("{type:\"".$document_path->type."\",path:\"".url("storage/{$document_path->path}/{$document_path->name}#view=FitH")."\", due_date: '".$document_path->due_date."', status: '" . $document_path->status . "', created_at: '" . $document_path->created_at . "', updated_at: '" . $document_path->updated_at . "', sended_by: '" . $document_path->sended_by . "', aproved_by: '" . $document_path->aproved_by . "', desc: '" . $document_path->desc . "'},"); ?>
@endif
@endif
@endif
@endforeach
@endif
@endif
@endforeach
@endforeach
@endif
];
                const old_paths = [@if($document_paths->first())
@foreach($document_paths as $document_path)
@if($document_path->actual == 0)
<?php echo htmlspecialchars_decode("{type:\"".$document_path->type."\",path:\"".url("storage/{$document_path->path}/{$document_path->name}#view=FitH")."\", due_date: '".$document_path->due_date."', status: '" . $document_path->status . "', created_at: '" . $document_path->created_at . "', updated_at: '" . $document_path->updated_at . "', sended_by: '" . $document_path->sended_by . "', aproved_by: '" . $document_path->aproved_by . "', desc: '" . $document_path->desc . "'},"); ?>
@endif
@endforeach
@endif
];


        const modalClose3 = () => {
            disapproveDescription.required = false;
            disapproveDesc.required = false;
            modal3.classList.remove('fadeIn');
            modal3.classList.add('fadeOut');
            setTimeout(() => {
                modal3.style.display = 'none';
            }, 500);
        }

        const openModal3 = () => {
            disapproveDescription.required = true;
            disapproveDesc.required = true;
            modal3.classList.remove('fadeOut');
            modal3.classList.add('fadeIn');
            modal3.style.display = 'flex';
        }

        const approveDoc = (e) => {
            const approve_input = document.getElementById("approve");
            approve_input.value = 'yes';
            save_button.click();
        }
        
        const disapproveDoc = (e) => {
            openModal3();
            // const approve_input = document.getElementById("approve");
            // approve_input.value = 'no';
            // save_button.click();
        }

        const disapproveDocModal = (e) => {
            const approve_input = document.getElementById("approve");
            approve_input.value = 'no';
            
            disapproveDescription.value = disapproveDesc.value;
            save_button.click();
        }

        const modalClose2 = () => {
            modal2.classList.remove('fadeIn');
            modal2.classList.add('fadeOut');
            setTimeout(() => {
                modal2.style.display = 'none';
            }, 500);
        }

        const openModal2 = (title, path_par = false) => {

let button_update = document.getElementById('modal_save_update');
if(button_update) {
    button_update.classList.add("hidden");
}

var modal_tr_list = document.querySelectorAll('.modal-tr');

if (modal_tr_list) {
    for (let index = 0; index < modal_tr_list.length; index++) {
        modal_tr_list[index].remove();
    }
}
var modalType = document.getElementById("modal_type");
modalType.value = title;

const approve_input = document.getElementById("approve");
approve_input.name = 'approve'.concat(title);

var OldElement = document.getElementById("modal-object");
if(OldElement) {
    OldElement.remove();
}
// type="application/pdf" width="100%" height="500px"
var objectPdfViewer = document.createElement('object');
objectPdfViewer.type = 'application/pdf';
objectPdfViewer.data = 'temp';
objectPdfViewer.id = 'modal-object';
objectPdfViewer.classList.add('modal-size-pdf');

var elementPHtmlPdfViewer = document.createElement('p');
elementPHtmlPdfViewer.innerText = 'Documento não existe!';
elementPHtmlPdfViewer.id = 'modal-object-p';
elementPHtmlPdfViewer.classList.add('modal-size-pdf');

var modal_div_obj = document.getElementById("modal-div-obj");
modal_div_obj.appendChild(objectPdfViewer);

var modal_object = document.getElementById("modal-object"); 
modal_object.appendChild(elementPHtmlPdfViewer);

if(!(path_par)){
    for (let index = 0; index < paths.length; index++) {
        if(paths[index].type == title) {
            
            var modal_button2 = document.querySelector('.modal-form-button');
            if(modal_button2) {
                modal_button2.innerText = 'Enviar!';
            }
            
            @if(Auth::user()->type != 'Fiscal') var modal_button2 = document.querySelector('.modal-form-input'); @endif
            if(modal_button2) {
                modal_button2.value = '';
            }
            @if(Auth::user()->type != 'Fiscal') new_due_date.value = null;
            modal_save_create.classList.add("hidden"); @endif
            old_due_date.value = paths[index].due_date;
            modal_status.innerText = paths[index].status;
            sended_by.innerText = paths[index].sended_by;
            desc.innerText = paths[index].desc;
            aproved_by.innerText = paths[index].aproved_by;

            modal_doc_info.classList.remove("hidden");
            modal_date_info.classList.remove("hidden");
            modal_status_info.classList.remove("hidden");
            sended_by_info.classList.remove("hidden");
            modal_historic.classList.remove("hidden");
            @if(Auth::user()->type != 'Fiscal') modal_aprove_text.classList.remove("hidden");
            modal_aprove_title.classList.remove("hidden"); @endif
            if(paths[index].status == 'Aprovado') {
                aproved_by_info.classList.remove("hidden");
                aproved_by_text.innerText = "Aprovado por";
            }
            if(paths[index].status == 'Reprovado') {
                aproved_by_info.classList.remove("hidden");
                desc_info.classList.remove("hidden");
                aproved_by_text.innerText = "Recusado por";
            }
            break;
        } else {
            old_due_date.value = null;
            modal_status.innerText = null;
            sended_by.innerText = null;
            modal_doc_info.classList.add("hidden");
            modal_date_info.classList.add("hidden");
            modal_status_info.classList.add("hidden");
            sended_by_info.classList.add("hidden");
            aproved_by_info.classList.add("hidden");
            desc_info.classList.add("hidden");
            modal_historic.classList.add("hidden");
            @if(Auth::user()->type != 'Fiscal') modal_aprove_text.classList.add("hidden");
            modal_aprove_title.classList.add("hidden"); @endif
            var elementMainTRButtonOldPDFViewer = document.getElementById("main-modal-tr");
            if(elementMainTRButtonOldPDFViewer) {
                elementMainTRButtonOldPDFViewer.remove();
            }
            
        }
    }
    for (let index = 0; index < paths.length; index++) {
        if(paths[index].type == title) {
            if(paths[index].desc) {

            }
            var modal_button2 = document.getElementById(title.concat('bt'));
            if(modal_button2) {
                modal_button2.disabled = false;
            }
            save_button.disabled = false;
            old_due_date.disabled = false;

            var modal_object = document.getElementById("modal-object");
            if(modal_object) {
                modal_object.remove();
            }
            
            var elementMainTRButtonOldPDFViewer = document.getElementById("main-modal-tr");
            if(elementMainTRButtonOldPDFViewer) {
                elementMainTRButtonOldPDFViewer.remove();
            }

            var objectPdfViewer = document.createElement('object');
            objectPdfViewer.type = 'application/pdf';
            objectPdfViewer.data = paths[index].path;
            objectPdfViewer.id = 'modal-object';
            objectPdfViewer.classList.add('modal-size-pdf');

            var elementMainTRButtonOldPDFViewer = document.createElement('tr');
            elementMainTRButtonOldPDFViewer.id = 'main-modal-tr';
            elementMainTRButtonOldPDFViewer.classList.add('main-modal-tr', 'border-b');

            var main_modal_table = document.getElementById('modal-table').getElementsByTagName('tbody')[0]; 
            main_modal_table.appendChild(elementMainTRButtonOldPDFViewer);

            var elementMainTHButtonOldPDFViewer = document.createElement('th');
            elementMainTHButtonOldPDFViewer.id = 'main-modal-th';

            var main_modal_tr = document.getElementById('main-modal-tr'); 
            main_modal_tr.appendChild(elementMainTHButtonOldPDFViewer);

            var elementMainTheTextPDFViewer = document.createElement('p');
            elementMainTheTextPDFViewer.id = 'main-modal-p';
            elementMainTheTextPDFViewer.innerText = 'Enviado às '.concat(paths[index].updated_at);
            elementMainTheTextPDFViewer.classList.add('py-3', 'bg-gray-50', 'text-left', 'text-xs', 'font-medium', 'text-gray-500', 'tracking-wider', 'bg-white');

            var main_modal_th = document.getElementById('main-modal-th'); 
            main_modal_th.appendChild(elementMainTheTextPDFViewer);

            var elementMainTDButtonOldPDFViewer = document.createElement('td');
            elementMainTDButtonOldPDFViewer.id = 'main-modal-td';
            elementMainTDButtonOldPDFViewer.classList.add('td200', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900', 'bg-white', 'divide-y', 'divide-gray-200');

            main_modal_tr.appendChild(elementMainTDButtonOldPDFViewer);

            var elementMainButtonOldPDFViewer = document.createElement('button');
            elementMainButtonOldPDFViewer.onclick = function () {
                openModal2(title);
            };
            elementMainButtonOldPDFViewer.type = 'button';
            elementMainButtonOldPDFViewer.id = 'main-modal-btn';
            elementMainButtonOldPDFViewer.disabled = true;
            elementMainButtonOldPDFViewer.innerText = 'Verificar!';
            elementMainButtonOldPDFViewer.classList.add('inline-flex' ,'items-center' ,'px-4' ,'py-2' ,'bg-white' ,'border' ,'border-gray-300' ,'rounded-md' ,'font-semibold' ,'text-xs' ,'text-gray-700' ,'uppercase' ,'tracking-widest' ,'shadow-sm' ,'hover:bg-gray-50' ,'focus:outline-none' ,'focus:ring-2' ,'focus:ring-indigo-500' ,'focus:ring-offset-2' ,'disabled:opacity-25' ,'transition' ,'ease-in-out' ,'duration-150' ,'mt-2' ,'mr-2');

            var modal_td = document.getElementById('main-modal-td'); 
            modal_td.appendChild(elementMainButtonOldPDFViewer);
        }
        var elementPHtmlPdfViewer = document.createElement('p');
        elementPHtmlPdfViewer.innerText = 'Documento não existe!';
        elementPHtmlPdfViewer.id = 'modal-object-p';
        elementPHtmlPdfViewer.classList.add('modal-size-pdf');

        var modal_div_obj = document.getElementById("modal-div-obj");
        modal_div_obj.appendChild(objectPdfViewer);
        
        var modal_object = document.getElementById("modal-object");
        modal_object.appendChild(elementPHtmlPdfViewer);
        
    }
} else {
    var modal_button2 = document.getElementById(title.concat('bt'));
    modal_button2.disabled = true;
    var elementMainButtonOldPDFViewer = document.getElementById("main-modal-btn");
    old_due_date.value = path_par.due_date;
    modal_status.innerText = path_par.status;
    old_due_date.disabled = true;
    old_due_date.name = null;
    save_button.disabled = true;
    @if(Auth::user()->type != 'Fiscal') modal_aprove_text.classList.add("hidden");
    modal_aprove_title.classList.add("hidden"); @endif

    var OldElement = document.getElementById("modal-object");
    if(OldElement) {
        OldElement.remove();
    }

    var objectPdfViewer = document.createElement('object');
    objectPdfViewer.type = 'application/pdf';
    objectPdfViewer.data = path_par.path;
    objectPdfViewer.id = 'modal-object';
    objectPdfViewer.classList.add('modal-size-pdf');
    var elementPHtmlPdfViewer = document.createElement('p');
    elementPHtmlPdfViewer.innerText = 'Documento não existe!';
    elementPHtmlPdfViewer.id = 'modal-object-p';
    elementPHtmlPdfViewer.classList.add('modal-size-pdf');

    var modal_div_obj = document.getElementById("modal-div-obj");
    modal_div_obj.appendChild(objectPdfViewer);
    
    var modal_object = document.getElementById("modal-object");
    modal_object.appendChild(elementPHtmlPdfViewer);
}
@if(Auth::user()->type != 'Fiscal') 
var modal_button2 = document.querySelector('.modal-form-button');
modal_button2.id = title.concat('bt');

var modal_button2 = document.querySelector('.modal-form-input');
modal_button2.id = title.concat('fl');
modal_button2.name = title; @endif

title2.innerText = title;
modal2.classList.remove('fadeOut');
modal2.classList.add('fadeIn');
modal2.style.display = 'flex';

for (let index = old_paths.length -1; index >= 0 ; index--){
    if(old_paths[index].type == title) {
        // old_paths[index].type;
        modal_historic.classList.remove("hidden");

        var elementTRButtonOldPDFViewer = document.createElement('tr');
        elementTRButtonOldPDFViewer.id = 'modal-tr-'+ index;
        elementTRButtonOldPDFViewer.classList.add('modal-tr', 'border-b');

        var modal_table = document.getElementById('modal-table').getElementsByTagName('tbody')[0]; 
        modal_table.appendChild(elementTRButtonOldPDFViewer);

        var elementTHButtonOldPDFViewer = document.createElement('th');
        elementTHButtonOldPDFViewer.id = 'modal-th-'+ index;
        // elementTHButtonOldPDFViewer.innerText = old_paths[index].created_at;
        // elementTHButtonOldPDFViewer.classList.add('py-3', 'bg-gray-50', 'text-left', 'text-xs', 'font-medium', 'text-gray-500', 'uppercase', 'tracking-wider', 'bg-white');

        var modal_tr = document.getElementById('modal-tr-'+ index); 
        modal_tr.appendChild(elementTHButtonOldPDFViewer);

        var elementPButtonOldPDFViewer = document.createElement('p');
        elementPButtonOldPDFViewer.id = 'modal-p'+ index;
        
        elementPButtonOldPDFViewer.innerText = 'Enviado às '.concat(old_paths[index].created_at);
        elementPButtonOldPDFViewer.classList.add('py-3', 'bg-gray-50', 'text-left', 'text-xs', 'font-medium', 'text-gray-500', 'tracking-wider', 'bg-white');

        var modal_th = document.getElementById('modal-th-'+ index); 
        modal_th.appendChild(elementPButtonOldPDFViewer);

        var elementTDButtonOldPDFViewer = document.createElement('td');
        elementTDButtonOldPDFViewer.id = 'modal-td-'+ index;
        elementTDButtonOldPDFViewer.classList.add('td200', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-900', 'bg-white', 'divide-y', 'divide-gray-200');

        modal_tr.appendChild(elementTDButtonOldPDFViewer);

        var elementButtonOldPDFViewer = document.createElement('button');
        elementButtonOldPDFViewer.onclick = function () {
            openModal2(title, old_paths[index]);
        };
        elementButtonOldPDFViewer.type = 'button';
        if(old_paths[index].path == path_par.path) {
            elementButtonOldPDFViewer.disabled = true;
        }
        elementButtonOldPDFViewer.id = 'modal-btn-'+ index;
        elementButtonOldPDFViewer.innerText = 'Verificar!';
        elementButtonOldPDFViewer.classList.add('inline-flex' ,'items-center' ,'px-4' ,'py-2' ,'bg-white' ,'border' ,'border-gray-300' ,'rounded-md' ,'font-semibold' ,'text-xs' ,'text-gray-700' ,'uppercase' ,'tracking-widest' ,'shadow-sm' ,'hover:bg-gray-50' ,'focus:outline-none' ,'focus:ring-2' ,'focus:ring-indigo-500' ,'focus:ring-offset-2' ,'disabled:opacity-25' ,'transition' ,'ease-in-out' ,'duration-150' ,'mt-2' ,'mr-2');

        var modal_td = document.getElementById('modal-td-'+ index); 
        modal_td.appendChild(elementButtonOldPDFViewer);
    }
}
@if(Auth::user()->type == 'Fiscal') old_due_date.disabled = true; @endif
}

        for (let i = 0; i < closeButton2.length; i++) {
            const elements2 = closeButton2[i];
            elements2.onclick = (e) => modalClose2();
            modal2.style.display = 'none';
            window.onclick = function (event) {
                if (event.target == modal2) modalClose2();
            }
        }

    for (let i = 0; i < closeButton3.length; i++) {

        const elements3 = closeButton3[i];

        elements3.onclick = (e) => modalClose3();

        modal3.style.display = 'none';

        window.onclick = function (event) {
            if (event.target == modal3) modalClose3();
        }
    }

    @error('document_manager') 
        openModal()
        let button_update = document.getElementById('modal_save_update');
        if(button_update) {
            button_update.classList.add("hidden");
        }
    @enderror

    @error('document_uploader_type')
        openModal2('{{ $message }}')
        let button_update = document.getElementById('modal_save_update');
        if(button_update) {
            button_update.classList.add("hidden");
        }
    @enderror
    </script>
</x-app-layout>
