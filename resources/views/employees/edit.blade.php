<?php
use App\Models\Employee;

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
                <a href="{{ route('employees.index', $company_id) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Voltar a Lista</a>
                @if(Auth::user()->type != 'Funcionário')
                @if ($editor->tipo == 'Contratante')
                <a href="#" onclick="openModal()"
                    class="bg-gray-200 hover:bg-gray-300 text-black  py-2 px-4 rounded">Gerir Documentos</a>
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
                    width: 200px;
                }

                .div500 {
                    width: 500px;
                }

                .table_info {
                    height: 100%;
                    display: flex;
                    flex-direction: row;
                    flex-wrap: nowrap;
                    justify-content: space-evenly;
                }

                .div1000 {
                    width: 1600px;
                }

                .modal-left {
                    position: relative;
                }

                .modal-right {
                    border-radius: 1px;
                    box-shadow: 0px 0px 0px 2px rgba(0, 0, 0, 0.3);
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
                    -webkit-box-shadow: 10px 10px 29px -4px rgba(0, 0, 0, 0.49);
                    -moz-box-shadow: 10px 10px 29px -4px rgba(0, 0, 0, 0.49);
                    box-shadow: 10px 10px 29px -4px rgba(0, 0, 0, 0.49);
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

            <div class="main-modal fixed w-full h-100 inset-0 z-50 overflow-hidden flex justify-center items-center animated fadeIn faster"
                style="background: rgba(0,0,0,.7);">
                <div
                    class="border border-teal-500 shadow-lg modal-container bg-white   mx-auto rounded shadow-lg z-50 overflow-y-auto">
                    <div class="div500 modal-content py-4 text-left px-6">
                        <!--Title-->
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold mr-3">Gerenciar Documentos</p>
                            <div class="modal-close cursor-pointer z-50 ml-3">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                                    height="18" viewBox="0 0 18 18">
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
                                        <th scope="col"
                                            class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $document->name }}
                                        </th>
                                        <td
                                            class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                            <button type="button" id="1"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                                onclick="selecionarBox(this, '{{ $document->name }}')">
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
                                                <input type="checkbox" id="{{ $document->name }}" name="documents[]"
                                                    value="{{ $document->name }}" class="hidden"
                                                    @if($employee->documents)
                                                @foreach(json_decode($employee->documents) as $document2)
                                                @if($document->name == $document2) checked @endif @endforeach @endif >
                                                <label for="{{ $document->name }}" class="hidden"> {{ $document->name }}
                                                </label>
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
                    class="border border-teal-500 shadow-lg modal-container bg-white   mx-auto rounded shadow-lg z-50 overflow-y-auto">
                    <div class="div1000 modal-content py-4 text-left px-6">
                        <!--Title-->
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold mr-3" id="modal-title2">Title</p>
                            <div class="modal-close2 cursor-pointer z-50 ml-3">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                                    height="18" viewBox="0 0 18 18">
                                    <path
                                        d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <!--Body-->
                        <div class="my-5 modal_body">
                            <form class="table_info" method="post"
                                action="{{ route('updatedoc', [$company_id, $employee->id]) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-left">
                                    <table id="modal-table" class="min-w-full divide-y divide-gray-200 w-full">
                                        <tr class="border-b hidden" id="modal_doc_info">
                                            <th colspan='2' scope="col"
                                                class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dados do Documento
                                            </th>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_date_info">
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data de Vencimento
                                            </th>
                                            <td
                                                class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                <input type="date" name="old_due_date" id="old_due_date"
                                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                                    wire:model.defer="state.old_due_date" autocomplete="old_due_date"
                                                    value="" onchange="allowUpdate()" />
                                                <div>
                                                    <!-- Input file -->
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_status_info">
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <td id="modal-status"
                                                class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">

                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_save_update">
                                            <th colspan='2' scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider">
                                                <div class="pb-2 pt-2 save_button">
                                                    @error('document_uploader')
                                                    <p id="document_uploader" class="text-reset text-sm text-red-600">
                                                        {{ $message }}</p>
                                                    @enderror
                                                    <button id="save_button"
                                                        class="ml-2 focus:outline-none modal-close2 px-4 bg-gray-400 p-3 rounded-lg text-black hover:bg-gray-300 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">Salvar</button>

                                                </div>
                                            </th>

                                        </tr>
                                        <tr class="border-b">
                                            <th colspan='2' scope="col"
                                                class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Enviar novo Documento
                                            </th>
                                        </tr>
                                        <tr class="border-b">
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Documento
                                            </th>
                                            <td
                                                class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                <button type="button" id="1"
                                                    class="modal-form-button inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2"
                                                    onclick="getDocument(this)">
                                                    Enviar!
                                                </button>
                                                <div>
                                                    <input type="file" name="1" id="1" class="modal-form-input hidden"
                                                        onchange="changeName(this)"
                                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-ful" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_date_create">
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data de Vencimento
                                            </th>
                                            <td
                                                class="td200 px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                                <input type="date" name="new_due_date" id="new_due_date"
                                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                                    wire:model.defer="state.new_due_date" autocomplete="new_due_date"
                                                    value="" onchange="allowCreate()" />
                                                <div>
                                                    <!-- Input file -->
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hidden" id="modal_save_create">
                                            <th colspan='2' scope="col"
                                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider">
                                                <div class="pb-2 pt-2 save_button">
                                                    @error('document_uploader')
                                                    <p id="document_uploader" class="text-reset text-sm text-red-600">
                                                        {{ $message }}</p>
                                                    @enderror
                                                    <button id="save_button"
                                                        class="ml-2 focus:outline-none modal-close2 px-4 bg-gray-400 p-3 rounded-lg text-black hover:bg-gray-300 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">Salvar</button>

                                                </div>
                                            </th>

                                        </tr>
                                        <tr class="border-b hidden" id="modal_historic">
                                            <th colspan='2' scope="col"
                                                class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                const modal = document.querySelector('.main-modal');
                const closeButton = document.querySelectorAll('.modal-close');

                const modal2 = document.querySelector('.main-modal2');
                const closeButton2 = document.querySelectorAll('.modal-close2');
                const title2 = document.getElementById('modal-title2');
                const object2 = document.getElementById("modal-object");
                const old_due_date = document.getElementById("old_due_date");
                const modal_status = document.getElementById("modal-status");
                const save_button = document.getElementById("save_button");
                const modal_doc_info = document.getElementById("modal_doc_info");
                const modal_date_info = document.getElementById("modal_date_info");
                const modal_status_info = document.getElementById("modal_status_info");
                const modal_historic = document.getElementById("modal_historic");
                const new_due_date = document.getElementById("new_due_date");
                const modal_save_create = document.getElementById("modal_save_create");
                const modal_date_create = document.getElementById("modal_date_create");


                const paths = [@if($employee - > documents)
                    @foreach(json_decode($employee - > documents) as $document)
                    @foreach(json_decode($documents) as $db_document)
                    @if($db_document - > name == $document)
                    @if($document_paths - > first())
                    @foreach($document_paths as $document_path)
                    @if($document_path - > actual == 1)
                    @if(limpaString($document_path - > type))
                    @if(limpaString($document_path - > type) == limpaString($db_document - > name)) <
                    ? php $document_name_display = $document_path - > name;
                    $document_path_display = $document_path - > path; ? >
                    <
                    ? php echo htmlspecialchars_decode("{type:\"".$document_path - > type.
                        "\",path:\"".url("storage/{$document_path->path}/{$document_path->name}#view=FitH").
                        "\", due_date: '".$document_path - > due_date.
                        "', status: '".$document_path - > status.
                        "', created_at: '".$document_path - > created_at.
                        "'},"); ? >
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
                const old_paths = [@if($document_paths - > first())
                    @foreach($document_paths as $document_path)
                    @if($document_path - > actual == 0) <
                    ? php echo htmlspecialchars_decode("{type:\"".$document_path - > type.
                        "\",path:\"".url("storage/{$document_path->path}/{$document_path->name}#view=FitH").
                        "\", due_date: '".$document_path - > due_date.
                        "', status: '".$document_path - > status.
                        "', created_at: '".$document_path - > created_at.
                        "'},"); ? >
                    @endif
                    @endforeach
                    @endif
                ];

                const modalClose = () => {
                    modal.classList.remove('fadeIn');
                    modal.classList.add('fadeOut');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 500);
                    ~ç
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

                const openModal2 = (title, path_par = false) => {
                    if (modal_date_create) {
                        modal_date_create.classList.add("hidden");
                    }

                    let button_update = document.getElementById('modal_save_update');
                    if (button_update) {
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

                    var OldElement = document.getElementById("modal-object");
                    if (OldElement) {
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

                    if (!(path_par)) {
                        for (let index = 0; index < paths.length; index++) {
                            if (paths[index].type == title) {

                                var modal_button2 = document.querySelector('.modal-form-button');
                                if (modal_button2) {
                                    modal_button2.innerText = 'Enviar!';
                                }

                                var modal_button2 = document.querySelector('.modal-form-input');
                                if (modal_button2) {
                                    modal_button2.value = '';
                                }
                                new_due_date.value = null;
                                modal_save_create.classList.add("hidden");
                                old_due_date.value = paths[index].due_date;
                                modal_status.innerText = paths[index].status;
                                modal_doc_info.classList.remove("hidden");
                                modal_date_info.classList.remove("hidden");
                                modal_status_info.classList.remove("hidden");
                                modal_historic.classList.remove("hidden");
                                break;
                            } else {
                                old_due_date.value = null;
                                modal_status.innerText = null;
                                modal_doc_info.classList.add("hidden");
                                modal_date_info.classList.add("hidden");
                                modal_status_info.classList.add("hidden");
                                modal_historic.classList.add("hidden");
                                var elementMainTRButtonOldPDFViewer = document.getElementById("main-modal-tr");
                                if (elementMainTRButtonOldPDFViewer) {
                                    elementMainTRButtonOldPDFViewer.remove();
                                }

                            }
                        }
                        for (let index = 0; index < paths.length; index++) {
                            if (paths[index].type == title) {
                                var modal_button2 = document.getElementById(title.concat('bt'));
                                if (modal_button2) {
                                    modal_button2.disabled = false;
                                }
                                save_button.disabled = false;
                                old_due_date.disabled = false;

                                var modal_object = document.getElementById("modal-object");
                                if (modal_object) {
                                    modal_object.remove();
                                }

                                var elementMainTRButtonOldPDFViewer = document.getElementById("main-modal-tr");
                                if (elementMainTRButtonOldPDFViewer) {
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

                                var main_modal_table = document.getElementById('modal-table').getElementsByTagName(
                                    'tbody')[0];
                                main_modal_table.appendChild(elementMainTRButtonOldPDFViewer);

                                var elementMainTHButtonOldPDFViewer = document.createElement('th');
                                elementMainTHButtonOldPDFViewer.id = 'main-modal-th';
                                elementMainTHButtonOldPDFViewer.innerText = paths[index].created_at.concat(
                                    ' (ATUAL)');
                                elementMainTHButtonOldPDFViewer.classList.add('px-6', 'py-3', 'bg-gray-50',
                                    'text-left', 'text-xs', 'font-medium', 'text-gray-500', 'uppercase',
                                    'tracking-wider');

                                var main_modal_tr = document.getElementById('main-modal-tr');
                                main_modal_tr.appendChild(elementMainTHButtonOldPDFViewer);

                                var elementMainTDButtonOldPDFViewer = document.createElement('td');
                                elementMainTDButtonOldPDFViewer.id = 'main-modal-td';
                                elementMainTDButtonOldPDFViewer.classList.add('td200', 'px-6', 'py-4',
                                    'whitespace-nowrap', 'text-sm', 'text-gray-900', 'bg-white', 'divide-y',
                                    'divide-gray-200');

                                main_modal_tr.appendChild(elementMainTDButtonOldPDFViewer);

                                var elementMainButtonOldPDFViewer = document.createElement('button');
                                elementMainButtonOldPDFViewer.onclick = function () {
                                    openModal2(title);
                                };
                                elementMainButtonOldPDFViewer.type = 'button';
                                elementMainButtonOldPDFViewer.id = 'main-modal-btn';
                                elementMainButtonOldPDFViewer.disabled = true;
                                elementMainButtonOldPDFViewer.innerText = 'Verificar!';
                                elementMainButtonOldPDFViewer.classList.add('inline-flex', 'items-center', 'px-4',
                                    'py-2', 'bg-white', 'border', 'border-gray-300', 'rounded-md',
                                    'font-semibold', 'text-xs', 'text-gray-700', 'uppercase', 'tracking-widest',
                                    'shadow-sm', 'hover:bg-gray-50', 'focus:outline-none', 'focus:ring-2',
                                    'focus:ring-indigo-500', 'focus:ring-offset-2', 'disabled:opacity-25',
                                    'transition', 'ease-in-out', 'duration-150', 'mt-2', 'mr-2');

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
                        elementMainButtonOldPDFViewer.disabled = false;
                        old_due_date.value = path_par.due_date;
                        modal_status.innerText = path_par.status;
                        old_due_date.disabled = true;
                        old_due_date.name = null;
                        save_button.disabled = true;

                        var OldElement = document.getElementById("modal-object");
                        if (OldElement) {
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
                    var modal_button2 = document.querySelector('.modal-form-button');
                    modal_button2.id = title.concat('bt');

                    var modal_button2 = document.querySelector('.modal-form-input');
                    modal_button2.id = title.concat('fl');
                    modal_button2.name = title;

                    title2.innerText = title;
                    modal2.classList.remove('fadeOut');
                    modal2.classList.add('fadeIn');
                    modal2.style.display = 'flex';

                    for (let index = 0; index < old_paths.length; index++) {
                        if (old_paths[index].type == title) {
                            // old_paths[index].type;
                            modal_historic.classList.remove("hidden");

                            var elementTRButtonOldPDFViewer = document.createElement('tr');
                            elementTRButtonOldPDFViewer.id = 'modal-tr-' + index;
                            elementTRButtonOldPDFViewer.classList.add('modal-tr', 'border-b');

                            var modal_table = document.getElementById('modal-table').getElementsByTagName('tbody')[
                                0];
                            modal_table.appendChild(elementTRButtonOldPDFViewer);

                            var elementTHButtonOldPDFViewer = document.createElement('th');
                            elementTHButtonOldPDFViewer.id = 'modal-th-' + index;
                            elementTHButtonOldPDFViewer.innerText = old_paths[index].created_at;
                            elementTHButtonOldPDFViewer.classList.add('px-6', 'py-3', 'bg-gray-50', 'text-left',
                                'text-xs', 'font-medium', 'text-gray-500', 'uppercase', 'tracking-wider');

                            var modal_tr = document.getElementById('modal-tr-' + index);
                            modal_tr.appendChild(elementTHButtonOldPDFViewer);

                            var elementTDButtonOldPDFViewer = document.createElement('td');
                            elementTDButtonOldPDFViewer.id = 'modal-td-' + index;
                            elementTDButtonOldPDFViewer.classList.add('td200', 'px-6', 'py-4', 'whitespace-nowrap',
                                'text-sm', 'text-gray-900', 'bg-white', 'divide-y', 'divide-gray-200');

                            modal_tr.appendChild(elementTDButtonOldPDFViewer);

                            var elementButtonOldPDFViewer = document.createElement('button');
                            elementButtonOldPDFViewer.onclick = function () {
                                openModal2(title, old_paths[index]);
                            };
                            elementButtonOldPDFViewer.type = 'button';
                            if (old_paths[index].path == path_par.path) {
                                elementButtonOldPDFViewer.disabled = true;
                            }
                            elementButtonOldPDFViewer.id = 'modal-btn-' + index;
                            elementButtonOldPDFViewer.innerText = 'Verificar!';
                            elementButtonOldPDFViewer.classList.add('inline-flex', 'items-center', 'px-4', 'py-2',
                                'bg-white', 'border', 'border-gray-300', 'rounded-md', 'font-semibold',
                                'text-xs', 'text-gray-700', 'uppercase', 'tracking-widest', 'shadow-sm',
                                'hover:bg-gray-50', 'focus:outline-none', 'focus:ring-2',
                                'focus:ring-indigo-500', 'focus:ring-offset-2', 'disabled:opacity-25',
                                'transition', 'ease-in-out', 'duration-150', 'mt-2', 'mr-2');

                            var modal_td = document.getElementById('modal-td-' + index);
                            modal_td.appendChild(elementButtonOldPDFViewer);
                        }
                    }

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
                <form method="post" action="{{ route('employees.update', [$company_id, $employee->id]) }}"
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
                                            name="employee_photo_path" x-on:change="
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

                                        <x-secondary-button class="mt-2 mr-2" type="button"
                                            x-on:click.prevent="$refs.photo.click()">
                                            {{ __('Select A New Photo') }}
                                        </x-secondary-button>

                                        @if (Employee::findOrFail($employee->id)->employee_photo_path)
                                        <x-secondary-button type="button" class="mt-2" onclick="fdeleteProfilePhoto()"
                                            wire:click="deleteProfilePhoto">
                                            {{ __('Remove Photo') }}
                                        </x-secondary-button>

                                        <input type="text" id="deleteProfile" name="deleteProfilePhoto" class="hidden">
                                        @endif

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
                                    {{ $employee->id }}
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
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
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CPF
                                </th>

                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    <input type="text" name="cpf" id="cpf"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.cpf" autocomplete="cpf"
                                        onkeypress="$(this).mask('000.000.000-00')"
                                        value="{{ old('cpf', $employee->cpf) }}" />
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
                                    <input type="date" name="admission" id="admission"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        wire:model.defer="state.admission" autocomplete="new-admission"
                                        value="{{ old('admission', $employee->admission) }}" />
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
                                            <option value="{{ $responsibility->id }}"
                                                {{ $employee->responsibility == $responsibility->name ? 'selected' : '' }}>
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
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $document_name }}
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">

                                    <button onclick="openModal2('{{ $document_name }}')" type="button"
                                        id="{{ limpaString($document_name).'btn' }}"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mt-2 mr-2">
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
                                            <option value="{{ $sector->id }}"
                                                {{ $employee->sector == $sector->name ? 'selected' : '' }}>
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
                                            <option value="{{ $company->id }}"
                                                {{ $employee->company == $company->name ? 'selected' : '' }}>
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
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Funcionário Criado Em
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
                                    {{ $employee->created_at }}
                                </td>
                            </tr>
                            <tr class="border-b">
                                <th scope="col"
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Funcionário Atualizado Em
                                </th>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 bg-white divide-y divide-gray-200">
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
