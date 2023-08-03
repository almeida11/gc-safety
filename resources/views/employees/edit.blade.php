<?php

function limpaString($string) {
    return $document_name = preg_replace('/[ -]+/' , '_' , strtolower( preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim($string)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")) ));
}

?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Funcionário
        </h2>
    </x-slot>

    <div>
        
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="block mb-8 mb-4">
                <a href="{{ route('employees.index', $company_id) }}" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">Voltar a Lista</a>
                @if(Auth::user()->type != 'Usuário')
                    @if ($editor->tipo == 'Contratante')
                        <a href="#" onclick="openModal()" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded">Gerir Documentos</a>
                    @endif
                @endif
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
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    display: flex;
                    flex-wrap: nowrap;
                    align-items: center;
                    width: 100%;
                    justify-content: space-between;
                }

                .modal-size-pdf {
                    width: 1100px;
                    height: 800px;
                    text-align: center;
                    -webkit-box-shadow: 10px 10px 29px -4px rgba(0,0,0,0.49);
                    -moz-box-shadow: 10px 10px 29px -4px rgba(0,0,0,0.49);
                    box-shadow: 10px 10px 29px -4px rgba(0,0,0,0.49);
                }
            </style>

            <div class="main-modal fixed w-full h-100 inset-0 z-50 overflow-hidden flex justify-center items-center animated fadeIn faster"
                style="background: rgba(0,0,0,.7);">
                <div
                    class="border border-teal-500 shadow-lg modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
                    <div class="div500 modal-content py-4 text-left px-6">
                        <!--Title-->
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold mr-3">Gerenciar Documentos</p>
                            <div class="modal-close cursor-pointer z-50 ml-3">
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
                            <form method="post" action="{{ route('editdoc', [$company_id, $employee->id]) }}">
                                @csrf
                                <table class="min-w-full divide-y divide-gray-200 w-full">
                                    @foreach($documents as $document)
                                        <tr class="border-b">
                                            <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $document->name }}
                                            </th> 
                                            <td class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                <button type="button" id="1" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                                onclick="selecionarBox(this, '{{ $document->name }}')" >
                                                @if($employee->documents)
                                                    @foreach(json_decode($employee->documents) as $document2)
                                                        @if($document->name == $document2)
                                                            NÃO 
                                                        @endif
                                                    @endforeach
                                                @endif
                                                EXIGIR!
                                                </button>
                                                <div>
                                                    <input type="checkbox" id="{{ $document->name }}" name="documents[]" value = "{{ $document->name }}" class="hidden" @if($employee->documents)
                                                    @foreach(json_decode($employee->documents) as $document2) @if($document->name == $document2) checked @endif @endforeach @endif >
                                                    <label for = "{{ $document->name }}" class="hidden"> {{ $document->name }} </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            <!--Footer-->
                            <div class="flex justify-end pt-2">
                                @error('document_manager')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <button
                                    class="focus:outline-none modal-close px-4 bg-gray-400 p-3 rounded-lg text-black hover:bg-gray-300 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="main-modal2 fixed w-full h-100 inset-0 z-50 overflow-hidden flex justify-center items-center animated fadeIn faster"
                style="background: rgba(0,0,0,.7);">
                <div
                    class="border border-teal-500 shadow-lg modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
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
                        <div class="my-5">
                            <form class="table_info"  method="post" action="{{ route('updatedoc', [$company_id, $employee->id]) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-left">
                                    <table class="min-w-full divide-y divide-gray-200 w-full">
                                        <tr class="border-b">
                                            <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Enviar Documento
                                            </th> 
                                            <td class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
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
                                        <tr class="border-b">
                                            <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data de Vencimento
                                            </th> 
                                            <td class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                    <input type="date" name="due_date" id="due_date" 
                                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                                        wire:model.defer="state.due_date" autocomplete="due_date" value="" />
                                                <div>
                                                    <!-- Input file -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <input type="text" name="modal_type" id="modal_type" class="hidden" />

                                    <!--Footer-->
                                    <div class="pb-2 pt-2 save_button">
                                        <button
                                            class="focus:outline-none modal-close2 px-4 bg-gray-400 p-3 rounded-lg text-black hover:bg-gray-300 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">Salvar</button>
                                            @error('document_uploader')
                                                <p class="text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                </div>
                                
                                <div class="modal-right" id="modal-div-obj">
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
            <script>
                const modal = document.querySelector('.main-modal');
                const closeButton = document.querySelectorAll('.modal-close');

                const modal2 = document.querySelector('.main-modal2');
                const closeButton2 = document.querySelectorAll('.modal-close2');
                const title2 = document.getElementById('modal-title2');
                const object2 = document.getElementById("modal-object");
                const due_date = document.getElementById("due_date");

                const due_dates = [@if($employee->documents)
@foreach(json_decode($employee->documents) as $document)
@foreach(json_decode($documents) as $db_document)
@if($db_document->name == $document)
@if($document_paths->first())
@foreach($document_paths as $document_path)
@if(limpaString($document_path->type))
@if(limpaString($document_path->type) == limpaString($db_document->name))
<?php $document_name_display = $document_path->name;
$document_path_display = $document_path->path; ?>
<?php echo htmlspecialchars_decode("{type:\"".$document_path->type."\",due_date:\"".$document_path->due_date."\"},"); ?>
@break
@endif
@endif
@endforeach
@endif
@endif
@endforeach
@endforeach
@endif
];

                const paths = [@if($employee->documents)
@foreach(json_decode($employee->documents) as $document)
@foreach(json_decode($documents) as $db_document)
@if($db_document->name == $document)
@if($document_paths->first())
@foreach($document_paths as $document_path)
@if(limpaString($document_path->type))
@if(limpaString($document_path->type) == limpaString($db_document->name))
<?php $document_name_display = $document_path->name;
$document_path_display = $document_path->path; ?>
<?php echo htmlspecialchars_decode("{type:\"".$document_path->type."\",path:\"".url("storage/{$document_path->path}/{$document_path->name}#view=FitH")."\"},"); ?>
@break
@endif
@endif
@endforeach
@endif
@endif
@endforeach
@endforeach
@endif
];

                const modalClose = () => {
                    modal.classList.remove('fadeIn');
                    modal.classList.add('fadeOut');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 500);
                }

                const openModal = () => {
                    modal.classList.remove('fadeOut');
                    modal.classList.add('fadeIn');
                    modal.style.display = 'flex';
                }

                for (let i = 0; i < closeButton.length; i++) {

                    const elements = closeButton[i];

                    elements.onclick = (e) => modalClose();

                    modal.style.display = 'none';

                    window.onclick = function (event) {
                        if (event.target == modal) modalClose();
                    }
                }
                @error('document_manager') openModal() @enderror

                const modalClose2 = () => {
                    modal2.classList.remove('fadeIn');
                    modal2.classList.add('fadeOut');
                    setTimeout(() => {
                        modal2.style.display = 'none';
                    }, 500);
                }

                const openModal2 = (title) => {

                    var modalType = document.getElementById("modal_type");
                    modalType.value = title;

                    var OldElement = document.getElementById("modal-object");
                    if(OldElement) {
                        OldElement.remove();
                    }
                    // type="application/pdf" width="100%" height="500px"
                    var NewElement = document.createElement('object');
                    NewElement.type = 'application/pdf';
                    NewElement.data = 'temp';
                    NewElement.id = 'modal-object';
                    NewElement.classList.add('modal-size-pdf');

                    var NewPha = document.createElement('p');
                    NewPha.innerText = 'Documento não existe!';
                    NewPha.id = 'modal-object-p';
                    NewPha.classList.add('modal-size-pdf');

                    var element1 = document.getElementById("modal-div-obj");
                    element1.appendChild(NewElement);
                    
                    var element2 = document.getElementById("modal-object");
                    element2.appendChild(NewPha);

                    for (let index = 0; index < due_dates.length; index++) {
                        if(due_dates[index].type == title) {
                            due_date.value = due_dates[index].due_date;
                            break;
                        } else {
                            due_date.value = null;
                        }
                    }

                    for (let index = 0; index < paths.length; index++) {
                        if(paths[index].type == title) {
                            var OldElement = document.getElementById("modal-object");
                            if(OldElement) {
                                OldElement.remove();
                            }

                            var NewElement = document.createElement('object');
                            NewElement.type = 'application/pdf';
                            NewElement.data = paths[index].path;
                            NewElement.id = 'modal-object';
                            NewElement.classList.add('modal-size-pdf');
                        }
                        var NewPha = document.createElement('p');
                        NewPha.innerText = 'Documento não existe!';
                        NewPha.id = 'modal-object-p';
                        NewPha.classList.add('modal-size-pdf');

                        var element1 = document.getElementById("modal-div-obj");
                        element1.appendChild(NewElement);
                        
                        var element2 = document.getElementById("modal-object");
                        element2.appendChild(NewPha);

                        
                    }
                    var modal_button2 = document.querySelector('.modal-form-button');
                    modal_button2.id = title.concat('bt');

                    var modal_button2 = document.querySelector('.modal-form-input');
                    modal_button2.id = title.concat('fl');
                    modal_button2.name = title;
                    
                    title2.innerText = title;
                    modal2.classList.remove('fadeOut');
                    modal2.classList.add('fadeIn');
                    modal2.style.display = 'flex';
                }

                for (let i = 0; i < closeButton2.length; i++) {

                    const elements2 = closeButton2[i];

                    elements2.onclick = (e) => modalClose2();

                    modal2.style.display = 'none';

                    window.onclick = function (event) {
                        if (event.target == modal2) modalClose2();
                    }
                }
                @error('document_uploader_type') openModal2('{{ $message }}') @enderror
            </script>
            
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form method="post" action="{{ route('employees.update', [$company_id, $employee->id]) }}" enctype="multipart/form-data">
                    <div class="flex flex-col">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ $employee->id }}
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="name" id="name"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.name" autocomplete="name"
                                        value="{{ old('name', $employee->name) }}" />
                                    @error('name')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CPF
                                </th>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="cpf" id="cpf"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.cpf" autocomplete="cpf"  onkeypress="$(this).mask('000.000.000-00')"
                                        value="{{ old('cpf', $employee->cpf) }}" />
                                    @error('cpf')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admissão
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="date" name="admission" id="admission" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    wire:model.defer="state.admission" autocomplete="new-admission"
                                        value="{{ old('admission', $employee->admission) }}"/>
                                    @error('admission')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cargo
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="id_responsibility" name="id_responsibility"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                        wire:model="id_responsibility">
                                            @foreach($responsibilities as $responsibility)
                                                <option value="{{ $responsibility->id }}" {{ $employee->responsibility == $responsibility->name ? 'selected' : '' }}>
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
                                            
                                        <button onclick="openModal2('{{ $document_name }}')" type="button" id="{{ limpaString($document_name).'btn' }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                            onclick="getDocument('{{ limpaString($document_name).'btn' }}', '{{ limpaString($document_name).'fl' }}')">
                                            <?php $document_name_display = 'Enviar!' ?>
                                            @if($document_paths->first())
                                                @foreach($document_paths as $document_path)
                                                    @if(limpaString($document_path->type))
                                                        @if(limpaString($document_path->type) == limpaString($document_name))
                                                            <?php $document_name_display = $document_path->name;
                                                                $document_path_display = $document_path->path;
                                                                $check_doc = true;  ?>
                                                                
                                                            @break
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
                            
                            <!-- @if($employee->documents)
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
                                        <button type="button" id="{{ limpaString($document_name).'btn' }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                            onclick="getDocument('{{ limpaString($document_name).'btn' }}', '{{ limpaString($document_name).'fl' }}')">
                                            <?php $document_name_display = 'Enviar!' ?>
                                            @if($document_paths->first())
                                                @foreach($document_paths as $document_path)
                                                    @if(limpaString($document_path->type))
                                                        @if(limpaString($document_path->type) == limpaString($document_name))
                                                            <?php $document_name_display = $document_path->name;
                                                                $check_doc = true;  ?>
                                                            @break
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                            {{ $document_name_display }}
                                        </button>
                                                <input type="file" name="{{ $document_name }}" id="{{ limpaString($document_name).'fl' }}" class="hidden" onchange="changeName(this, '{{ limpaString($document_name).'btn' }}', '{{ limpaString($document_name).'fl' }}')"
                                                {{ $document_name }}
                                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                                />
                                            @error('document'.$document_name)
                                                <p class="text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            @endif -->
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Setor
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="id_sector" name="id_sector"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                        wire:model="id_sector">
                                            @foreach($sectors as $sector)
                                                <option value="{{ $sector->id }}"  {{ $employee->sector == $sector->name ? 'selected' : '' }}>
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
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Empresa
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <div class="col-span-6 sm:col-span-4">
                                        <select id="id_company" name="id_company"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful"
                                        wire:model="id_company">
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" {{ $employee->company == $company->nome_fantasia ? 'selected' : '' }}>
                                                    {{ $company->nome_fantasia }}
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
                                            <option value='0' {{ $employee->active ? '' : 'selected' }}>
                                                Inativo
                                            </option>
                                        </select>
                                        @error('active') 
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </td>
                            </tr>

                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuário Criado Em
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ $employee->created_at }}
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuário Atualizado Em
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ $employee->updated_at }}
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