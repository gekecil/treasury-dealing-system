@extends('layouts.master')

@section('title', 'Accounts')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item active">Accounts</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-id-card'></i> Accounts
                            </h1>
                        </div>
@if (session('status'))
						<div id="alert-dismissible" class="panel-container show">
							<div class="panel-content">
								<div class="alert alert-success alert-dismissible fade show" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true"><i class="fal fa-times"></i></span>
									</button>
									<strong>Well Done!</strong> {{ session('status') }}
								</div>
							</div>
						</div>
@endif
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-news-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
											Accounts <span class="fw-300"><i>Table</i></span>
										</h2>
										<div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
											<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
											<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <!-- datatable start -->
                                            <table id="dt-advance" class="table table-bordered table-hover table-striped w-100"></table>
                                            <!-- datatable end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
					<!-- Modal default-->
					<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<form action="{{ route('accounts.store') }}" method="post">
									@csrf
									
									<div class="modal-header">
										<h4 class="modal-title">
											Create Account
										</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true"><i class="fal fa-times"></i></span>
										</button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label class="form-label" for="number">Account Number</label>
											<input type="number" name="number" class="form-control" min="1000000000" max="9999999999" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="cif">CIF</label>
											<input type="number" name="cif" class="form-control" min="1000000" max="99999999" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="name">Account Name</label>
											<input type="text" name="name" class="form-control" required>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-primary">Submit</button>
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
					<script src="/moment/min/moment.min.js"></script>
					<script type="text/javascript">
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							initApp.buildNavigation(myapp_config.navHooks);
							
							$.fn.dataTable.ext.errMode = 'throw';
							
							dtAdvance = $('#dt-advance').DataTable({
								responsive: true,
								fixedHeader: {
									headerOffset: $(document.body)
										.hasClass('header-function-fixed') ? $('header.page-header').outerHeight() : 0
								},
								paging: true,
								pageLength: 50,
								lengthChange: false,
								bInfo: false,
								order: [],
@can ('create', 'App\Account')
@can ('delete', new App\Account)
								select: {
									style: 'multi',
									items: 'cell',
									selector: '.select-checkbox'
								},
@endcan
								dom: "<'row mb-3'" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f>" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>" +
									">" +
									"<'row'<'col-sm-12'tr>>" +
									"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										text: '<span class="fal fa-plus-square mr-1"></span>Create',
										titleAttr: 'Create Account',
										className: 'btn btn-outline-primary waves-effect waves-themed mr-1',
										action: function ( e, dt, node, config ) {
											$('.modal:not(.js-modal-settings):not(.modal-alert)').modal();
										}
									}
@can ('delete', new App\Account)
									,
									{
										text: '<span class="fal fa-times-square mr-1"></span>Delete',
										titleAttr: 'Delete Account',
										className: 'btn btn-outline-danger waves-effect waves-themed collapse',
										action: function ( e, dt, node, config ) {
											dt.button().container().siblings().remove();
											dt.button().container().after(document.createElement('form'));
											dt.button().container().next().attr('action', @json(route('accounts.destroy', ['account' => 'deletes'])));
											dt.button().container().next().attr('method', 'post');
											
											dt.button().container().next().append(document.createElement('button'));
											dt.button().container().next().children().eq(0).addClass('collapse');
											dt.button().container().next().children().eq(0).attr('data-target', '#modal-alert');
											
											dt.button().container().next().append(document.createElement('input'));
											dt.button().container().next().children().eq(1).attr('type', 'hidden');
											dt.button().container().next().children().eq(1).attr('name', '_method');
											dt.button().container().next().children().eq(1).val(('delete').toUpperCase());
											
											dt.button().container().next().append(document.createElement('input'));
											dt.button().container().next().children().eq(2).attr('type', 'hidden');
											dt.button().container().next().children().eq(2).attr('name', '_token');
											dt.button().container().next().children().eq(2).val(
                                                $(document).find('meta[name="csrf-token"]').attr('content')
                                            );
											
											dt.rows({
												selected: true
											}).every( function ( rowIdx, tableLoop, rowLoop ) {
												dt.button().container().next().append(document.createElement('input'));
												dt.button().container().next().children().eq(rowLoop + 3).attr('type', 'hidden');
												dt.button().container().next().children().eq(rowLoop + 3).attr('name', 'deletes[]');
												dt.button().container().next().children().eq(rowLoop + 3).val(dt.row(rowIdx).data().id);
											})
											
											$('#modal-alert').modal();
										}
									}
@endcan
								],
@endcan
								serverSide: true,
								processing: true,
								ajax: {
									url: '{{ route("api.accounts.index") }}',
									type: 'GET',
									dataType: 'json',
									data: {
                                        api_token: $(document).find('meta[name="api-token"]').attr('content')
                                    }
								},
								language: {
									infoFiltered: ''
								},
								columns: [
@can ('delete', new App\Account)
									{
										title: '<i class="fal fa-check"></i>',
										orderable: false,
										data: null,
										defaultContent: '',
										className: 'select-checkbox text-center pointer',
										width: '5%'
									},
@endcan
									{
										title: 'Account Name',
										data: 'name',
										render: function(data, type, row, meta) {
											return data.trim();
										}
									},
									{
										title: 'Account Number',
										className: 'text-center',
										data: 'number'
									},
									{
										title: 'CIF',
										className: 'text-center',
										data: 'cif'
									},
									{
										title: 'Monthly USD Equivalent',
										data: 'monthly_usd_equivalent',
										className: 'text-right',
										render: function(data, type, row, meta) {
											return data.toLocaleString('en-US');
										}
									}
								],
								initComplete: function(settings, json) {
									settings.oInstance.api().columns().header().to$().addClass('text-center');
									settings.oInstance.api().table().header().classList.add('thead-dark');
								}
							})
						})
					</script>
@endsection
