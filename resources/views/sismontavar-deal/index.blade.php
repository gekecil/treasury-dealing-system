@extends('layouts.master')

@section('title', 'SISMONTAVAR - Sales')

@section('stylesheet')
        <link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
        <link rel="stylesheet" media="screen, print" href="/css/formplugins/select2/select2.bundle.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item"><a href="#sales">Sales</a></li>
                            <li class="breadcrumb-item active">SISMONTAVAR</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-table'></i> SISMONTAVAR
                            </h1>
                        </div>
						@if (session('status'))
							<div class="alert alert-success">
								{{ session('status') }}
							</div>
						@endif
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-sismontavar-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
											SISMONTAVAR <span class="fw-300"><i>Table</i></span>
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
					<!-- Modal -->
					<div class="modal fade" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<form action="{{ route('sismontavar-deals.store') }}" method="post">
									@csrf
									
									<input type="hidden" name="account-number">
									<input type="hidden" name="account-cif">
									<input type="hidden" name="account-name">
									<div class="modal-header pb-0">
										<h4 class="modal-title">SISMONTAVAR Data</h4>
										<div>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true"><i class="fal fa-times"></i></span>
											</button>
											<time></time>
										</div>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label class="form-label" for="account">Account</label>
											<select name="account" required></select>
										</div>
										<div class="form-group">
											<label class="form-label" for="deal-type">Deal Type</label>
											<select name="deal-type" class="form-control" required>
												<option value>Choose</option>
												<option value="TOD">TOD</option>
												<option value="TOM">TOM</option>
												<option value="Spot">Spot</option>
												<option value="Forward">Forward</option>
												<option value="SWAP">SWAP</option>
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="direction">Direction</label>
											<select name="direction" class="form-control" required>
												<option value>Choose</option>
												<option value="Buy">Buy</option>
												<option value="Sell">Sell</option>
												<option value="Buy and Sell">Buy and Sell</option>
												<option value="Sell and Buy">Sell and Buy</option>
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="base-currency">Base Currency</label>
											<select name="currency-pair" class="form-control" required>
												<option value>Choose</option>
@foreach ($currencyPair as $value)
												<option value="{{ $value->id }}">{{
                                                    ($value->baseCurrency->secondary_code ?: $value->baseCurrency->primary_code)
                                                }}/IDR</option>
@endforeach
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="base-volume">Base Volume</label>
											<input type="hidden" name="base-volume" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="quote-volume">Quote Volume</label>
											<input type="hidden" name="quote-volume" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="periods">Periods</label>
											<input type="number" name="periods" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="near-rate">Near Rate</label>
											<input type="hidden" name="near-rate" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="far-rate">Far Rate</label>
											<input type="hidden" name="far-rate" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="transaction-purpose">Transaction Purpose</label>
											<input type="text" name="transaction-purpose" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="transaction-date">Transaction Date</label>
											<input type="text" name="transaction-date" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="near-value-date">Near Value Date</label>
											<input type="text" name="near-value-date" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="far-value-date">Far Value Date</label>
											<input type="text" name="far-value-date" class="form-control" autocomplete="off" required>
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
@endsection

@section('javascript')
					<script src="/js/datagrid/datatables/datatables.bundle.js"></script>
					<script src="/moment/min/moment.min.js"></script>
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
					<script src="/js/formplugins/select2/select2.bundle.js"></script>

                    <script type="text/javascript">
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
							initApp.buildNavigation(myapp_config.navHooks);

							$.fn.dataTable.ext.errMode = 'throw';

							dtAdvance = $('#dt-advance').DataTable({
								responsive: true,
								lengthChange: false,
								paging: true,
								pageLength: 50,
								bInfo: false,
								order: [],
								searching: true,
								searchable: true,
								select: true,
								dom: "<'row mb-3'" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f>" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>" +
									">" +
									"<'row'<'col-sm-12'tr>>" +
									"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										text: '<span class="fal fa-plus mr-1"></span>Add',
										titleAttr: 'Add the SISMONTAVAR',
										className: 'btn btn-outline-primary waves-effect waves-themed',
										action: function() {
											$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').modal();
										}
									}
								],
								serverSide: true,
								processing: true,
								ajax: {
									method: 'GET',
									url: @json(route('api.sismontavar-deals.index')),
									data: function(params) {
										params.api_token = $(document).find('meta[name="api-token"]').attr('content');
									},
								},
								columns: [
									{
										title: 'Transaction ID',
										data: 'transaction_id'
									},
									{
										title: 'Corporate Name',
										data: 'corporate_name'
									},
									{
										title: 'Currency Pairs',
										className: 'text-center',
										data: 'base_currency',
										render: function(data, type, row, meta) {
											return data.concat('/').concat(row.quote_currency);
										}
									},
									{
										title: 'Direction',
										data: 'direction'
									},
									{
										title: 'Near Rate',
										className: 'text-right',
										data: 'near_rate'
									},
									{
										title: 'Base Volume',
										className: 'text-right',
										data: 'base_volume'
									},
									{
										title: 'Periods',
										className: 'text-center',
										data: 'periods'
									},
									{
										title: 'Created At',
										data: 'created_at',
										className: 'text-center',
										render: function(data, type, row, meta) {
											return moment(data).format('llll');
										}
									},
									{
										title: 'Status',
										data: 'status_text',
										className: 'text-center',
										render: function(data, type, row, meta) {
                                            if (row.status_code != 200) {
                                                return row.status_code;
                                            }

											return JSON.parse(data).Message;
										}
									}
								],
								language: {
									infoFiltered: ''
								},
								createdRow: function(row, data, dataIndex) {
									$(row).addClass('pointer');
								},
								initComplete: function(settings, json) {
                                    settings.oInstance.api().columns().header().to$().addClass('text-center');

									window.setInterval( function () {
										settings.oInstance.api().ajax.reload(null, false);
									}, 15000 );

								}
							});
                        });

					</script>
@endsection
