@extends('layouts.master')

@section('title', 'Interbank - Dealing')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
		<link rel="stylesheet" media="screen, print" href="/css/formplugins/select2/select2.bundle.css">
        <link rel="stylesheet" media="screen, print" href="/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item"><a href="#dealing">Interbank</a></li>
                            <li class="breadcrumb-item active">Dealing</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
						<div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-clipboard-check'></i> Dealing
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
								<div class="panel panel-interbank-deal">
                                    <div class="panel-hdr bg-fusion-400">
                                        <h2>
                                            Cross Currency
                                        </h2>
                                        <div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
											<div class="row collapse">
												<div class="col-sm-3">
													<div class="panel border-0 shadow-none">
														<div class="card-group border-0 shadow-none">
															<div class="card border-0 rounded-0">
																<div class="card-header text-center">
																	<h2 class="card-title"><strong></strong></h2>
																</div>
																<div class="row">
																	<div class="col-md-6 pr-0 text-left">
																		<button type="button" class="btn btn-success shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Buy</strong></button>
																	</div>
																	<div class="col-md-6 pl-0 text-right">
																		<button type="button" class="btn btn-danger shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Sell</strong></button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12">
								<div class="panel panel-interbank-deal">
                                    <div class="panel-hdr bg-primary-700">
                                        <h2>
                                            Currency
                                        </h2>
                                        <div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
											<div class="row collapse">
												<div class="col-sm-3">
													<div class="panel border-0 shadow-none">
														<div class="card-group border-0 shadow-none">
															<div class="card border-0 rounded-0">
																<div class="card-header text-center">
																	<h2 class="card-title"><strong></strong></h2>
																</div>
																<div class="row">
																	<div class="col-md-6 pr-0 text-left">
																		<button type="button" class="btn btn-success shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Buy</strong></button>
																	</div>
																	<div class="col-md-6 pl-0 text-right">
																		<button type="button" class="btn btn-danger shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Sell</strong></button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
                            <div class="col-xl-12">
                                <div id="panel-interbank-deal-index" class="panel">
                                    <div class="panel-hdr bg-faded">
                                        <h2>
											Interbank Deal <span class="fw-300"><i>Table</i></span>
										</h2>
										<div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
											<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
											<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
											<table id="dt-advance" class="table table-bordered table-hover table-striped w-100">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th rowspan="2">Counterparty</th>
                                                        <th rowspan="2">Currency Pairs</th>
                                                        <th rowspan="2">Base Amount</th>
                                                        <th rowspan="2">Interoffice Rate</th>
                                                        <th rowspan="2">Buy/Sell</th>
                                                        <th class="text-center" colspan="2">Remarks</th>
                                                        <th rowspan="2">Created At</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Basic</th>
                                                        <th>Additional</th>
                                                    </tr>
                                            </table>
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
								<form action="{{ route('interbank-dealing.store') }}" method="post">
									@csrf
									
									<input type="hidden" name="base-currency-code" required>
									<input type="hidden" name="counter-currency-code" required>
									<input type="hidden" name="buy-sell" required>
									<input type="hidden" name="commercial-bank-limit">
									<input type="hidden" name="base-currency-closing-rate">
									<input type="hidden" name="world-currency-closing-rate">
									<input type="hidden" name="world-currency-code">
                                    <input type="hidden" name="created-at" value="{{ \Carbon\Carbon::today()->toDateString() }}">
									<div class="modal-header pb-0">
										<h4 class="modal-title"></h4>
										<div>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true"><i class="fal fa-times"></i></span>
											</button>
										</div>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label class="form-label" for="interoffice-rate">Interoffice Rate</label>
											<input type="hidden" name="interoffice-rate" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="base-currency-rate">Base Currency Rate</label>
											<input type="hidden" name="base-currency-rate" required>
											<input type="text" class="form-control" autocomplete="off" required>
										</div>
                                        <div class="form-group">
											<label class="form-label" for="counter-currency-rate">Counter Currency Rate</label>
											<input type="text" class="form-control" readonly>
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
										<div class="form-group">
											<label class="form-label" for="tod-tom-spot-forward">TOD/TOM/Spot/Forward</label>
											<select name="tod-tom-spot-forward" class="form-control" required>
												<option value>Choose</option>
												<option value="TOD">TOD</option>
												<option value="TOM">TOM</option>
												<option value="spot">Spot</option>
												<option value="forward">Forward</option>
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="counterparty">Counterparty</label>
											<input type="text" name="counterparty" class="form-control" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="remarks">Remarks</label>
                                            <textarea name="basic-remarks" class="form-control mb-2" rows="5"></textarea>
                                            <textarea name="additional-remarks" class="form-control" rows="5"></textarea>
										</div>
                                        <div class="form-group collapse">
                                            <label class="form-label" for="value-date">Value Date</label>
                                            <div class="input-group">
                                                <input type="text" name="settlement-date" class="form-control" placeholder="Select date" id="datepicker" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text fs-xl">
                                                        <i class="fal fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
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
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
					<script src="/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
					<script src="/moment/min/moment.min.js"></script>

					<script type="text/javascript">
						var interbankDeal = function() {
							$.ajax({
								method: 'GET',
								url: @json(route('api.currencies.index')),
								data: {
									api_token: $(document).find('meta[name="api-token"]').attr('content'),
									is_interbank_dealing: 1
								}
							}).done( function(response) {
								response.data.cross_currency = response.data.cross_currency.filter(
                                    cross_currency => cross_currency.belongs_to_interbank
                                );

								response.column = $('.panel-interbank-deal:eq(0) .panel-content > .row > *');

								response.data.cross_currency = response.data.cross_currency.concat(
									response.data.special_currency.filter(
										special_currency => (
											special_currency.counter_currency_id && (
												response.data.closing_rate.find(
													closing_rate => (
                                                        closing_rate.currency.primary_code === special_currency.base_currency.primary_code
                                                    )
												)
											) && (
												response.data.closing_rate.find(
													closing_rate => (
                                                        closing_rate.currency.primary_code === special_currency.counter_currency.primary_code
                                                    )
												)
											)
										)
									)
								).filter(
									cross_currency => (
										response.data.closing_rate.find(
											closing_rate => closing_rate.currency.primary_code === cross_currency.base_currency.primary_code
										) &&
										response.data.closing_rate.find(
											closing_rate => closing_rate.currency.primary_code === cross_currency.counter_currency.primary_code
										)
									)
								)
                                .map((value) => {
                                    if (!value.base_currency.secondary_code) {
                                        value.base_currency.secondary_code = value.base_currency.primary_code;
                                    }

                                    return value;
                                });

								if (
									response.data.closing_rate.find(closing_rate => closing_rate.is_world_currency) &&
									(response.data.commercial_bank_limit || @json(auth()->user()->is_super_administrator)) &&
									response.data.cross_currency.length
								) {
									response.column.parent().collapse('show');

									$.each(response.data.cross_currency, function(key, value) {
										if (key in response.column) {
											value.column = response.column.get(key);
										} else {
											value.column = response.column.get(0).cloneNode('true');
										}

										value.column.dataset.commercialBankLimit = response.data.commercial_bank_limit;
										value.column.dataset.baseCurrencyClosingRate = response.data.closing_rate.find(
											closing_rate => closing_rate.currency.primary_code === value.base_currency.primary_code
										).mid_rate;

										value.worldCurrencyClosingRate = response.data.closing_rate.find(
                                            closing_rate => closing_rate.is_world_currency
                                        );
										value.column.dataset.worldCurrencyCode = value.worldCurrencyClosingRate.currency.primary_code;
										value.column.dataset.worldCurrencyClosingRate = value.worldCurrencyClosingRate.mid_rate;
										value.column.dataset.baseCurrencyCode = value.base_currency.primary_code;
										value.column.dataset.counterCurrencyCode = value.counter_currency.primary_code;

										value.column.querySelector('.card-group').id = 'card-cross';
										value.column.querySelector('.card-group').id += key.toString();

										value.column.querySelector('.card-title strong').innerHTML = value.base_currency.secondary_code;
										value.column.querySelector('.card-title strong').innerHTML += '/';

                                        if (!value.counter_currency_id) {
                                            value.counter_currency = {
                                                primary_code : 'IDR'
                                            }
                                        }

										value.column.querySelector('.card-title strong').innerHTML += value.counter_currency.primary_code;

										if (!(key in response.column)) {
											response.column.get(0).parentElement.appendChild(value.column);
										}
									})

								} else {
									response.column.parent().collapse('hide');
								}

								response.column.filter(':gt(' + (response.data.cross_currency.length - 1) + ')')
								.each( function(key, element) {
									element.remove();
								})

								response.data.currency = response.data.currency.filter(currency => currency.belongs_to_interbank);
								response.column = $('.panel-interbank-deal:eq(1) .panel-content > .row > *');

								response.data.currency = response.data.currency.concat(
									response.data.special_currency.filter(
										special_currency => (
											!special_currency.counter_currency_id && (
												response.data.closing_rate.find(
													closing_rate => (
                                                        closing_rate.currency.primary_code === special_currency.base_currency.primary_code
                                                    )
												)
											)
										)
									)
								).filter(
									currency => (
										response.data.closing_rate.find(
											closing_rate => closing_rate.currency.primary_code === currency.base_currency.primary_code
										)
									)
								)
                                .map((value) => {
                                    if (!value.base_currency.secondary_code) {
                                        value.base_currency.secondary_code = value.base_currency.primary_code;
                                    }

                                    return value;
                                });

								if (
									response.data.closing_rate.find(closing_rate => closing_rate.is_world_currency) &&
									(response.data.commercial_bank_limit || @json(auth()->user()->is_super_administrator)) &&
									response.data.currency.length
								) {
									response.column.parent().collapse('show');

									$.each(response.data.currency, function(key, value) {
										if (key in response.column) {
											value.column = response.column.get(key);
										} else {
											value.column = response.column.get(0).cloneNode('true');
										}

										value.column.dataset.commercialBankLimit = response.data.commercial_bank_limit;
										value.column.dataset.baseCurrencyClosingRate = response.data.closing_rate.find(
											closing_rate => closing_rate.currency.primary_code === value.base_currency.primary_code
										)
                                        .mid_rate;

										value.worldCurrencyClosingRate = response.data.closing_rate.find(
                                            closing_rate => closing_rate.is_world_currency
                                        );
										value.column.dataset.worldCurrencyCode = value.worldCurrencyClosingRate.currency.primary_code;
										value.column.dataset.worldCurrencyClosingRate = value.worldCurrencyClosingRate.mid_rate;
										value.column.dataset.baseCurrencyCode = value.base_currency.primary_code;

										value.column.querySelector('.card-group').id = 'card-cross';
										value.column.querySelector('.card-group').id += key.toString();

										value.column.querySelector('.card-title strong').innerHTML = value.base_currency.secondary_code;
										value.column.querySelector('.card-title strong').innerHTML += '/';

                                        if (!value.counter_currency_id) {
                                            value.counter_currency = {
                                                primary_code : 'IDR'
                                            }
                                        }

										value.column.querySelector('.card-title strong').innerHTML += value.counter_currency.primary_code;

										if (!(key in response.column)) {
											response.column.get(0).parentElement.appendChild(value.column);
										}
									})

								} else {
									response.column.parent().collapse('hide');
								}

								response.column.filter(':gt(' + (response.data.currency.length - 1) + ')')
								.each( function(key, element) {
									element.remove();
								})

							}).fail( function(jqXHR, textStatus, errorThrown) {
								$('.panel-interbank-deal .panel-content > .row > *').parent().collapse('hide');
							})
							
						};
						
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
							initApp.buildNavigation(myapp_config.navHooks);

							interbankDeal();
							window.setInterval(interbankDeal, 10000);

                            $.fn.dataTable.ext.errMode = 'throw';

							dtAdvance = $('#dt-advance').DataTable({
								responsive: true,
								lengthChange: false,
								order: [],
								paging: true,
								pageLength: 50,
								lengthChange: false,
								bInfo: false,
								searching: true,
								searchable: true,
								select: true,
								serverSide: true,
								processing: true,
								ajax: {
									method: 'GET',
									url: @json(route('api.interbank-deals.index')),
									data: {
										api_token: $(document).find('meta[name="api-token"]').attr('content')
									} 
								},
								columns: [
									{
										title: 'Counterparty',
										data: 'counterparty.name'
									},
									{
                                        data: 'currency_pair',
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
										data: 'amount',
										className: 'text-right',
										render: function(data, type, row, meta) {
											return parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: 2
											});
										}
									},
									{
										data: 'interoffice_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											return parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: 7
											});
										}
									},
									{
										data: 'buy_or_sell.name',
										className: 'text-center text-capitalize',
										render: function(data, type, row, meta) {
											return 'bank ' + data;
										}
									},
                                    {
										data: 'basic_remarks'
									},
                                    {
										data: 'additional_remarks'
									},
									{
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
								createdRow: function(row, data, dataIndex) {
									$(row).addClass('pointer');
								},
								initComplete: function(settings, json) {
									settings.oInstance.api().columns().header().to$().addClass('text-center');
									settings.oInstance.api().columns().header().to$().addClass('align-middle');

									window.setInterval( function () {
										settings.oInstance.api().ajax.reload(null, false);
									}, 15000 );
								}
							})

						})

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('show.bs.modal', function(e) {
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().val('');
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().trigger('input');
							$(e.currentTarget).find('input[name="base-currency-rate"]').next().val('');
							$(e.currentTarget).find('input[name="base-currency-rate"]').next().trigger('input');
							$(e.currentTarget).find('input[name="amount"]').next().val('');
							$(e.currentTarget).find('input[name="amount"]').next().trigger('input');

							if (e.relatedTarget.closest('[class^=col-sm]').dataset.counterCurrencyCode) {
								$(e.currentTarget).find('label[for="base-currency-rate"]').closest('.form-group').show();
								$(e.currentTarget).find('label[for="counter-currency-rate"]').closest('.form-group').show();
								
							} else {
								$(e.currentTarget).find('label[for="base-currency-rate"]').closest('.form-group').hide();
								$(e.currentTarget).find('label[for="counter-currency-rate"]').closest('.form-group').hide();
							}

							$(e.currentTarget).find('.modal-header .modal-title').text(e.relatedTarget.children[0].innerHTML);
							$(e.currentTarget).find('.modal-header .modal-title').append(document.createElement('small'));
							$(e.currentTarget).find('.modal-header .modal-title small').addClass('m-0', 'text-muted');
							$(e.currentTarget).find('.modal-header .modal-title small').addClass('m-0', 'text-muted')
								.text(e.relatedTarget.closest('.card').querySelector('.card-title strong').innerHTML);

							$(e.currentTarget).find('input[name="base-currency-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.baseCurrencyCode
							);

							$(e.currentTarget).find('input[name="counter-currency-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.counterCurrencyCode
							);

							$(e.currentTarget).find('input[name="buy-sell"]').val(e.relatedTarget.children[0].innerHTML.trim());

							$(e.currentTarget).find('input[name="commercial-bank-limit"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.commercialBankLimit
							);
							$(e.currentTarget).find('input[name="base-currency-closing-rate"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.baseCurrencyClosingRate
							);
							$(e.currentTarget).find('input[name="world-currency-closing-rate"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.worldCurrencyClosingRate
							);
							$(e.currentTarget).find('input[name="world-currency-code"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.worldCurrencyCode
							);

						})

					</script>
@endsection
