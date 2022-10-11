@extends('layouts.master')

@section('title', 'Rejections - Sales')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item"><a href="#sales">Sales</a></li>
                            <li class="breadcrumb-item active">Rejections</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-times-circle'></i> Rejections
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
                                <div id="panel-rejection-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>Rejection <span class="fw-300"><i>Table</i></span></h2>
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
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
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
@can ('view', new App\Cancellation)
								select: true,
@endcan
								order: [],
								serverSide: true,
								processing: true,
								ajax: {
									headers: { 'X-CSRF-TOKEN': $(document).find('meta[name="csrf-token"]').attr('content') },
									method: 'GET',
									url: @json(route('api.cancellations.index')),
									data: {
										api_token: $(document).find('meta[name="api-token"]').attr('content'),
										is_rejection: true
									}
								},
								columns: [
									{
										title: 'Customer Name',
										data: 'sales_deal.account.name',
										render: function(data, type, row, meta) {
											return data.trim();
										}
									},
									{
										title: 'Currency Pairs',
                                        data: 'sales_deal.currency_pair',
										className: 'text-center',
                                        render: function(data, type, row, meta) {
                                            if (!data.base_currency.secondary_code) {
                                                data.base_currency.secondary_code = data.base_currency.primary_code;
                                            }

											return (
                                                (data.base_currency.secondary_code).concat('/')
                                                    .concat(
                                                        data.counter_currency_id ? (
                                                            data.counter_currency.primary_code
                                                        ) : (
                                                            'IDR'
                                                        )
                                                    )
                                            );
										}
									},
									{
										title: 'Base Amount',
										data: 'sales_deal.amount',
										className: 'text-right',
										render: function(data, type, row, meta) {
											data = parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: data.split('.').slice(1).join().length
											});
											
											row.element = document.createElement('span');
											row.element.innerHTML = data;
											
											return row.element.outerHTML;
										}
									},
									{
										title: 'Customer Rate',
										data: 'sales_deal.customer_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											data = parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: 2,
												maximumFractionDigits: data.split('.').slice(1).join().length
											});
											
											row.element = document.createElement('span');
											row.element.innerHTML = data;
											
											return row.element.outerHTML;
										}
									},
									{
										title: 'Interoffice Rate',
										data: 'sales_deal.interoffice_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											data = parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: 2,
												maximumFractionDigits: data.split('.').slice(1).join().length
											});
											
											row.element = document.createElement('span');
											row.element.innerHTML = data;
											
											return row.element.outerHTML;
										}
									},
									{
										title: 'Buy/Sell',
										data: 'sales_deal.buy_or_sell.name',
										className: 'text-center text-capitalize',
										render: function(data, type, row, meta) {
											return ('bank').concat(' ').concat(data);
										}
									},
									{
										title: 'Created At',
										data: 'created_at',
										className: 'text-center',
										render: function(data, type, row, meta) {
											return moment(data).format('lll');
										}
									}
								],
								language: {
									infoFiltered: ''
								},
@can ('view', new App\Cancellation)
								createdRow: function(row, data, dataIndex) {
									$(row).addClass('pointer');
								},
@endcan
								initComplete: function(settings, json) {
									settings.oInstance.api().columns().header().to$().addClass('text-center');
									settings.oInstance.api().table().header().classList.add('thead-dark');
									
									window.setInterval( function () {
										settings.oInstance.api().ajax.reload(null, false);
									}, 15000 );
								}
							})
							
						})
						
					</script>
@endsection
