@extends('layouts.master')

@section('title', 'Ftp Curve Data')

@section('stylesheet')
    <link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
    <link rel="stylesheet" media="screen, print" href="/css/notifications/sweetalert2/sweetalert2.bundle.css">
@endsection

@section('content')
    <!-- BEGIN Page Content -->
    <!-- the #js-page-content id is needed for some plugins to initialize -->
    <main id="js-page-content" role="main" class="page-content">
        <ol class="breadcrumb page-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
            <li class="breadcrumb-item active">Ftp Curve Data</li>
            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
        </ol>
        <div class="subheader">
            <h1 class="subheader-title">
                <i class='subheader-icon fal fa-table'></i> Ftp Curve Data
            </h1>
        </div>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="row">
            <div class="col-xl-12">
                <div id="panel-Ftp Curve Data-index" class="panel">
                    <div class="panel-hdr">
                        <h2>
                            Ftp Curve Data <span class="fw-300"><i>Table</i></span>
                        </h2>
                        <div class="panel-toolbar">
                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip"
                                    data-offset="0,10" data-original-title="Collapse"></button>
                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                                    data-offset="0,10" data-original-title="Fullscreen"></button>
                            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip"
                                    data-offset="0,10" data-original-title="Close"></button>
                        </div>
                    </div>
                    <div class="panel-container show">
                        <div class="panel-content table-responsive">
                            <!--<form class="kt-form" method="post" action="#" enctype="multipart/form-data" id="formexcel">-->
                            <div class="form-row">
                                <div class="col-md-6 mb-4" hidden>
                                    <label class="form-label" for="JenisTimbang">Jenis Timbang<span
                                            class="text-danger">*</span> </label>
                                    <select class="form-control" name="JenisTimbang" id="JenisTimbang" required>
                                        <option value="">-</option>
                                        <option value="1">Pembelian</option>
                                        <option value="2">Penjualan</option>
                                        <option value="3">Timbang Saja</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-4" style="text-align: right;">
                                    <button type="button" class="btn btn-primary waves-effect waves-themed bloading" data-toggle="modal" data-target="#addModal">Add Data</button>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <table id="mytable_1" class="table table-bordered table-hover table-striped w-100">
                                        <thead>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--</form>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal-label"
         aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModal-label">Add Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formAdd" method="post" action="#">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col-md-12">
                                <a href="{{asset('template-upload-excel/ftp-curve-data-upload-example.xlsx')}}" class="text-secondary fw-700" download="">
                                    <h3 style="text-align: center">Download Template For Upload Excel Here</h3></a>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="fileName">File Excel</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" id="fileName" name="fileName" accept=".xlsx" required>
                                    </div>
                                </div>
                                <span class="help-block" style="font-size: 13px">Only accept file .xlsx</span>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="monthFile">Month File<span class="text-danger">*</span></label>
                                <select class="form-control" id="monthFile" name="monthFile">
                                    <option value="">-Choose</option>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="note">Note<span class="text-danger">*</span></label>
                                <input name="note" id="note" type="text" value="" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary bloading"><span class="fal fa-upload mr-1"></span>Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- this overlay is activated only when mobile menu is triggered -->
    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->
@endsection

@section('javascript')
    <script src="/js/datagrid/datatables/datatables.bundle.js"></script>
    <script src="/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{!! csrf_token() !!}'
            },
            ajaxStart: function(){},
            beforeSend: function (result, status, xhr) {// sebelum send ajax
                let bloading=$(".bloading");
                bloading.prop('disabled', true);
                if(bloading.text()==="Saving") {
                    bloading.html('Loading...');
                }
            },
            complete: function (result, status, xhr) {// complete ajax
                let bloading=$(".bloading");
                bloading.prop('disabled', false);
                if(bloading.text()==="Loading...") {
                    bloading.html('Saving');
                }
            },
            error: function (result, status, xhr) { //global error
                $('.modal').modal('hide');
                let bloading=$(".bloading");
                bloading.prop('disabled', false);
                if(bloading.text()==="Loading...") {
                    bloading.html('Saving');
                }
                if(xhr!=='abort') {
                    swalError(result.responseText);
                }
                $("#fileName").replaceWith($("#fileName").val('').clone(true));
            }
        });
        $(document).ready(function () {
            initTable();
            $('#formAdd').submit(function (e) {
                e.preventDefault();
                $('#addModal').modal('hide');
                Swal.fire({
                    type: "info",
                    title: 'Saving...',
                    html: 'Please Wait.',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '{{route("fcd.upload")}}',
                    type: "post",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: true,
                    xhrFields: {
                        'responseType': 'blob'
                    },
                    success: function(data, status, xhr) {
                        $("#fileName").replaceWith($("#fileName").val('').clone(true));
                        $("#note").val('');
                        $("#monthFile").val('');
                        Swal.fire("Succes!", "Upload Succes.", "success");
                        initTable();
                        xhrToExcel(data,xhr);
                    },
                });
            });
        });
        var mytable_1='';
        function initTable() {
            if(mytable_1!==''){
                mytable_1.ajax.reload();
                return false;
            }
            mytable_1= $('#mytable_1').DataTable( {
                'pageLength': 10,
                'scrollCollapse': true,
                searchDelay: 600,
                processing: true,
                serverSide: true,
                orderCellsTop: true,
                responsive: true,
                "ajax": {
                    "url": "{{route('fcd.table')}}",
                    "type": "POST",
                    "data": function (d) {
                        d.periode = $('#periodeSearch').val();
                        d.tahun = $('#tahunSearch').val();
                    },
                },
                "columns": [
                    { title: 'Note', data: 'note' },
                    {
                        title: 'Created', data: 'created_at',
                        render: function (cellData, type, row) {
                            return row.created_at+' | '+row.created_by;
                        }
                    },
                    {
                        title: 'Updated', data: 'updated_at',
                        render: function (cellData, type, row) {
                            if(row.updated_at===null){
                                return '';
                            }
                            return row.updated_at+' | '+row.updated_by;
                        }
                    },
                    {
                        title: 'Action', data: 'created_at',
                        render: function (cellData, type, row) {
                            var deleteButton='';
                            if(row.created_by==='{{str_replace("@idn.ccb.com","", auth()->user()->email)}}'){
                                deleteButton=`
                                <button onclick="deleteData('${row.id_upload}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"
                                class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed delButton bloading">
                                <i class="fal fa-trash"></i>
                                </button>`;
                            }
                            return `
                         <button onclick="downloadExcel('${row.id_upload}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Download" class="btn btn-info btn-icon rounded-circle waves-effect waves-themed bloading">
							<i class="fal fa-arrow-down"></i>
							</button>
							${deleteButton}`;
                        }
                    },
                ],
                order: [[1, 'desc']],
            });
        }
        function downloadExcel(id_upload){
            $.ajax({
                url: "{{route('fcd.download')}}/" + id_upload,
                type: "get",
                xhrFields: {
                    'responseType': 'blob'
                },
                success: function (data, status, xhr) {
                    xhrToExcel(data, xhr);
                },
            });
        }
        function deleteData(id_upload){
            Swal.fire({
                title: "Are You Sure To Delete ?",
                type: "warning",
                reverseButtons: true,
                confirmButtonColor: '#428bca',
                showCancelButton: true,
                confirmButtonText: "Yes !",
                cancelButtonText: "No !",
            }).then(function (result) {
                if (result.value) {
                    $.post("{{route('fcd.delete')}}", {id_upload: id_upload},
                        function (data, status) {
                            initTable();
                        }
                    );
                }
            });
        }
        function swalError(err) {
            Swal.fire({
                title: "Process Failed",
                type: "warning",
                html: err,
                //focusConfirm: false,
                confirmButtonText: '<i class="fal fa-thumbs-down"></i> Close',
                //allowOutsideClick: false
            });
        }
        function xhrToExcel(data,xhr) {
            var link = document.createElement('a'), filename = 'file.xlsx';
            if(xhr.getResponseHeader('Content-Disposition')){//filename
                filename = xhr.getResponseHeader('Content-Disposition');
                filename=filename.match(/filename="(.*?)"/)[1];
                filename=decodeURIComponent(escape(filename));
            }
            link.href = URL.createObjectURL(data);
            link.download = filename;
            link.click();
        }
    </script>
@endsection
