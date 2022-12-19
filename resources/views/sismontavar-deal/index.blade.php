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
@if (
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
					<!-- Modal -->
					<div class="modal fade" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
@if (request()->route()->named('sales-fx.index'))
								<form action="{{ route('sales-fx.store') }}" method="post">
@elseif (request()->route()->named('sales-special-rate-deal.index'))
								<form action="{{ route('sales-special-rate-deal.store') }}" method="post">
@endif
									@csrf
									
									<input type="hidden" name="base-primary-code" required>
									<input type="hidden" name="base-secondary-code" required>
									<input type="hidden" name="counter-primary-code">
									<input type="hidden" name="counter-secondary-code">
									<input type="hidden" name="buy-sell" required>
									<input type="hidden" name="threshold">
									<input type="hidden" name="sales-limit">
									<input type="hidden" name="base-currency-closing-rate">
									<input type="hidden" name="world-currency-closing-rate">
									<input type="hidden" name="world-currency-code">
									<input type="hidden" name="encrypted-query-string">
									<input type="hidden" name="account-number">
									<input type="hidden" name="account-cif">
									<input type="hidden" name="account-name">
									<input type="hidden" name="branch-name">
									<input type="hidden" name="sismontavar-threshold" value="{{
                                        App\SismontavarOption::latest()
                                        ->firstOrNew([], ['threshold' => null])
                                        ->threshold
                                    }}">
@cannot ('update', new App\SalesDeal)
									<input type="hidden" name="region">
@endcan
									<div class="modal-header pb-0">
										<h4 class="modal-title"></h4>
										<div>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true"><i class="fal fa-times"></i></span>
											</button>
											<time></time>
										</div>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label class="form-label" for="interoffice-rate">Interoffice Rate</label>
											<input type="hidden" name="interoffice-rate" required>
@if (request()->route()->named('sales-fx.index'))
											<input type="text" class="form-control" readonly>
@elseif (request()->route()->named('sales-special-rate-deal.index'))
											<input type="text" class="form-control" autocomplete="off" required>
@endif
										</div>
										<div class="form-group">
											<label class="form-label" for="customer-rate">Customer Rate</label>
											<input type="hidden" name="customer-rate" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="base-amount">Base Amount</label>
											<input type="hidden" name="amount" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="counter-amount">Counter Amount</label>
											<input type="text" class="form-control" readonly>
										</div>
@can ('update', new App\SalesDeal)
										<div class="form-group">
											<label class="form-label" for="region">Region</label>
											<select name="region" class="form-control" onkeydown="event.preventDefault()" required>
												<option value>Choose</option>
@foreach ($regions->pluck('region')->unique()->sort() as $value)
												<option value="{{ $value }}">{{ $value }}</option>
@endforeach
											</select>
										</div>
										<div class="form-group collapse">
											<label class="form-label" for="branch">Branch</label>
											<select name="branch-code" class="form-control" onkeydown="event.preventDefault()">
												<option value>Choose</option>
											</select>
										</div>
@can ('create', 'App\User')
										<div class="form-group">
											<label class="form-label" for="dealer">Dealer</label>
											<select name="dealer-id" class="form-control" onkeydown="event.preventDefault()" required></select>
										</div>
@endcan
@endcan
										<div class="form-group">
											<label class="form-label" for="account">Account</label>
											<select name="account" required></select>
										</div>
                                        <div class="form-group">
											<label class="form-label" for="monthly-usd-equivalent">Monthly USD Equivalent</label>
											<input type="text" class="form-control" value="0" readonly>
										</div>
										<div class="form-group">
											<label class="form-label" for="tod-tom-spot-forward">TOD/TOM/Spot/Forward</label>
											<select name="tod-tom-spot-forward" class="form-control" onkeydown="event.preventDefault()" required>
												<option value>Choose</option>
												<option value="TOD">TOD</option>
												<option value="TOM">TOM</option>
												<option value="spot">Spot</option>
												<option value="forward">Forward</option>
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="tt-bn">TT/BN</label>
											<select name="tt-bn" class="form-control" onkeydown="event.preventDefault()" required>
												<option value>Choose</option>
												<option value="TT">TT</option>
												<option value="BN">BN</option>
											</select>
										</div>
                                        <div class="form-group">
											<label class="form-label" for="lhbu-remarks-code">LHBU Remarks</label>
											<select name="lhbu-remarks-code" required></select>
                                            <select name="lhbu-remarks-kind" required></select>
                                            <textarea name="other-lhbu-remarks-kind" class="form-control mt-2 collapse" rows="5"></textarea>
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
											alert();
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
										title: '<i class="fal fa-check"></i>',
										orderable: false,
										data: null,
										defaultContent: '',
										className: 'select-checkbox text-center pointer',
										width: '5%'
									},
									{
										title: 'Title',
										data: 'title'
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
										title: 'Updated At',
										data: 'updated_at',
										className: 'text-center',
										render: function(data, type, row, meta) {
											return moment(data).format('llll');
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

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('show.bs.modal', function(e) {

							$(e.currentTarget).find('input[name="encrypted-query-string"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.encryptedQueryString
							);

							$(e.currentTarget).find('input[name="interoffice-rate"]').next().val('');
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().trigger('input');

							$(e.currentTarget).find('.modal-header .modal-title').text(e.relatedTarget.children[0].innerHTML);
							$(e.currentTarget).find('.modal-header .modal-title').append(document.createElement('small'));
							$(e.currentTarget).find('.modal-header .modal-title small').addClass('m-0', 'text-muted');
							$(e.currentTarget).find('.modal-header .modal-title small').addClass('m-0', 'text-muted')
								.text(e.relatedTarget.closest('.card').querySelector('.card-title strong').innerHTML);

                            if (
                                (e.relatedTarget.children[0].innerHTML.trim().toLowerCase() === 'bank sell') && (
                                    !e.relatedTarget.closest('[class^=col-sm]').dataset.counterCurrencyCode
                                )
                            ) {
                                $(e.currentTarget).find('[for="monthly-usd-equivalent"]').closest('.form-group').show();

                            } else {
                                $(e.currentTarget).find('[for="monthly-usd-equivalent"]').closest('.form-group').hide();
                            }

							$(e.currentTarget).find('input[name="base-primary-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.basePrimaryCode
							);

							$(e.currentTarget).find('input[name="base-secondary-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.baseSecondaryCode
							);

							$(e.currentTarget).find('input[name="counter-primary-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.counterPrimaryCode
							);

							$(e.currentTarget).find('input[name="counter-secondary-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.counterSecondaryCode
							);

							$(e.currentTarget).find('input[name="buy-sell"]').val(e.relatedTarget.children[0].innerHTML.trim());

							$(e.currentTarget).find('input[name="base-currency-closing-rate"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.baseCurrencyClosingRate
							);
							$(e.currentTarget).find('input[name="world-currency-closing-rate"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.worldCurrencyClosingRate
							);
							$(e.currentTarget).find('input[name="world-currency-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.worldCurrencyCode
							);

							$(e.currentTarget).find('input[name="customer-rate"]').next().val('');
							$(e.currentTarget).find('input[name="customer-rate"]').next().trigger('input');

							$(e.currentTarget).find('input[name="amount"]').next().val('');
							$(e.currentTarget).find('input[name="amount"]').next().trigger('input');

                            $(e.currentTarget).find('input[name="interoffice-rate"]').get(0).dataset.minimum = '1';
                            $(e.currentTarget).find('input[name="amount"]').get(0).dataset.minimum = '1';

                            switch (e.relatedTarget.children[0].innerHTML.trim().toLowerCase()) {
                                case 'bank buy':
                                    $(e.currentTarget).find('input[name="customer-rate"]').get(0).dataset.minimum = '1';
                                    $(e.currentTarget).find('input[name="customer-rate"]').get(0).dataset.maximum = $(e.currentTarget)
                                        .find('input[name="interoffice-rate"]')
                                        .val();

                                break;

                                default:
                                    $(e.currentTarget).find('input[name="customer-rate"]').get(0).dataset.minimum = $(e.currentTarget)
                                        .find('input[name="interoffice-rate"]')
                                        .val();

                                    delete $(e.currentTarget).find('input[name="customer-rate"]').get(0).dataset.maximum;

                            }

                            if ($(e.currentTarget).find('input[name="sales-limit"]').val().length) {
                                $(e.currentTarget).find('input[name="amount"]').get(0).dataset.maximum = $(e.currentTarget)
                                    .find('input[name="sales-limit"]')
                                    .val();
                            }

						})

                        $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('hidden.bs.modal', function(e) {
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().val('');
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().trigger('input');

							$(e.currentTarget).find('input[name="customer-rate"]').next().val('');
                            $(e.currentTarget).find('input[name="customer-rate"]').next().trigger('input');

							$(e.currentTarget).find('input[name="amount"]').next().val('');
							$(e.currentTarget).find('input[name="amount"]').next().trigger('input');

                        })

					</script>
@endsection
