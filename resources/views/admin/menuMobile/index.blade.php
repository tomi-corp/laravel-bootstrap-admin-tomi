@extends('layout.app')
@section('title', 'Menu Mobile')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Menu Mobile</li>
                    </ol>
                </div>
                <h4 class="page-title">Menu Mobile</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="float-right">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal"
                            data-target=".bs-example-modal-center"><i class="mdi mdi-plus"></i> Tambah Menu</button>
                    </div>
                </div>
            </div>


            <div class="row pt-4" id="menuCard">

            </div>



            {{-- modal Tambah --}}
            <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
                aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;" id="modalAdd">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myCenterModalLabel">Tambah Menu</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <form id="addFrom" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Title Menu</label>
                                    <input type="text" value="" id="title" class="form-control" required
                                        placeholder="Title Menu" />
                                    <small id="errTitle" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label>Icon Menu </label>
                                    <input type="file" accept="image/*" id="icon" class="form-control" required
                                        placeholder="Title Menu" />
                                    <small id="errIcon" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label>Path </label>
                                    <input type="text" value="" id="path" class="form-control" required
                                        placeholder="Path Menu" />
                                    <small id="errPath" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label>Status </label>
                                    <select class="form-control" id="status" required>
                                        <option value="show">Show</option>
                                        <option value="hidden">Hidden</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    <small id="errStatus" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                                <button class="btn btn-primary" type="submit" id="buttonSubmit">save <div id="loadingText">
                                    </div></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- modal Edit --}}
            <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
                aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;" id="modalEdit">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myCenterModalLabel">Edit Menu</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <form id="addFrom" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Title Menu</label>
                                    <input type="text" value="" id="titleEdit" class="form-control" required
                                        placeholder="Title Menu" />
                                    <small id="errEditTitle" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label>Icon Menu </label>
                                    <input type="file" accept="image/*" id="iconEdit" class="form-control" required
                                        placeholder="Title Menu" />
                                    <small id="errEditIcon" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label>Path </label>
                                    <input type="text" value="" id="pathEdit" class="form-control" required
                                        placeholder="Path Menu" />
                                    <small id="errEditPath" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label>Status </label>
                                    <select class="form-control" id="statusEdit" required>
                                        <option value="show">Show</option>
                                        <option value="hidden">Hidden</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    <small id="errEditStatus" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                                <button class="btn btn-primary" type="submit" id="buttonSubmitEdit">save <div
                                        id="loadingTextEdit">
                                    </div></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- modal Delete --}}
            <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
                aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;" id="modalHapus">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myCenterModalLabel">Hapus Menu</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <p class="text-center">Anda yakin akan menghapus menu mobile ini?</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                            <button class="btn btn-danger" type="submit" id="buttonHapus">save <div
                                    id="loadingTextHapus">
                                </div></button>
                        </div>
                    </div>
                </div>
            </div>


        </div>


    </div>



@endsection

@push('js')
    <script>
        let image = '';
        let hapus = '';
        let edit = '';

        $(document).ready(function() {
            listMenu = () => {
                $.ajax({
                    url: '{!! route('admin.listMenu.index') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(listMenu) {
                        $('#menuCard').find('div').remove();
                        $.each(listMenu.menu, function(key, menu) {
                            const htmlString = `
                                <div class="col-md-4" id="cardListMenu">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h5 style="margin: 2px; padding-bottom:4px;">${menu.title}</h5>
                                                        <img src="${menu.icon}" class="img" style="width:42px"/>
                                                    </div>
                                                    <div>
                                                        <i class=" mdi mdi-pencil text-success btn-action-size mr-1" type="button"  onclick="editMenu(${menu.id})"></i>
                                                        <i class=" mdi mdi-trash-can text-danger btn-action-size mr-1" type="button" onclick="deleteMenu(${menu.id})"></i>
                                                    </div>
                                                </div>

                                                <div class="pt-2">
                                                    <div class="d-flex justify-content-between pb-1">
                                                        <div>
                                                            <span>Status</span>
                                                        </div>
                                                        <div>
                                                            <span class="${menu.status == 'show' ? "text-success" : "text-warning"}">${menu.status}</span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between pb-1">
                                                        <div>
                                                            <span>Path:</span>
                                                        </div>
                                                        <div>
                                                            <h5 style="margin:2px;">${menu.path}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between pb-1">
                                                        <div>
                                                            <span>Last Updated</span>
                                                        </div>
                                                        <div>
                                                            <h5 style="margin:2px;">${menu.lastUpdate}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                `;

                            $('#menuCard').append(htmlString)
                        });
                    }
                });
            }
            listMenu();

        });

        $('#icon').on('change', function() {
            var img = this.files[0];
            var reader = new FileReader();
            reader.onloadend = function() {
                image = reader.result
            }
            reader.readAsDataURL(img);
        });

        $('#iconEdit').on('change', function() {
            var img = this.files[0];
            var reader = new FileReader();
            reader.onloadend = function() {
                image = reader.result
            }
            reader.readAsDataURL(img);
        });

        deleteMenu = (id) => {
            hapus = id;
            $('#modalHapus').modal('show');
        }

        editMenu = (id) => {
            edit = id;
            $.ajax({
                url: '{!! route('admin.menumobile.detail') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': edit
                },
                type: 'POST',
                dataType: 'json',
                success: function(detail) {
                    $('#titleEdit').val(detail.menu.title);
                    $('#pathEdit').val(detail.menu.path);
                    $('#statusEdit').val(detail.menu.status);
                    $('#modalEdit').modal('show');
                }
            });
        }

        $("#buttonHapus").on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{!! route('admin.menumobile.delete') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': hapus
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonHapus').attr("disabled", true);
                    $('#loadingTextHapus').addClass("spinner-border");
                },
                success: function(submitHapus) {
                    listMenu();
                    toastr.success(submitHapus.message)
                    $("#modalHapus").modal('hide');
                    $('#buttonHapus').attr("disabled", false);
                    $('#loadingTextHapus').removeClass("spinner-border");
                }
            });

        })


        $("#buttonSubmitEdit").on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{!! route('admin.menumobile.update') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': edit,
                    'title': $("#titleEdit").val(),
                    'icon': image,
                    'path': $("#pathEdit").val(),
                    'status': $("#statusEdit").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmitEdit').attr("disabled", true);
                    $('#loadingTextEdit').addClass("spinner-border");
                },
                success: function(submitRes) {
                    toastr.success(submitRes.message)
                    listMenu();
                    $("#modalEdit").modal('hide');
                    $('#errTitleEdit').text();
                    $('#errIconEdit').text();
                    $('#errPathEdit').text();
                    $('#errStatusEdit').text();
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                },
                error: function(submitRes) {
                    $('#errTitleEdit').text(submitRes.responseJSON.errors.title);
                    $('#errIconEdit').text(submitRes.responseJSON.errors.icon);
                    $('#errPathEdit').text(submitRes.responseJSON.errors.path);
                    $('#errStatusEdit').text(submitRes.responseJSON.errors.status);
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                }
            });

        })



        $("#addFrom").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '{!! route('admin.menumobile.add') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'title': $("#title").val(),
                    'icon': image,
                    'path': $("#path").val(),
                    'status': $("#status").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(submitRes) {
                    toastr.success(submitRes.message)
                    listMenu();
                    $("#modalAdd").modal('hide');
                    $('#errTitle').text();
                    $('#errIcon').text();
                    $('#errPath').text();
                    $('#errStatus').text();
                    $('#buttonSubmit').attr("disabled", false);
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                },
                error: function(submitRes) {
                    $('#errTitle').text(submitRes.responseJSON.errors.title);
                    $('#errIcon').text(submitRes.responseJSON.errors.icon);
                    $('#errPath').text(submitRes.responseJSON.errors.path);
                    $('#errStatus').text(submitRes.responseJSON.errors.status);
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                }
            });

        });
    </script>
@endpush
