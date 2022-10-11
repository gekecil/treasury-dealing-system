@extends('layouts.master')

@section('title', 'Currencies')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item active">Currencies</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-flag-checkered'></i> Currencies
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
                                <div id="panel-currency-index" class="panel">
									<div class="panel-hdr">
                                        <h2>
                                            Currency <span class="fw-300"><i>Table</i></span>
                                        </h2>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
											<!-- datatable start -->
                                            <table id="dt-currency" class="table table-bordered table-hover table-striped w-100">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>
															<i class="fal fa-check"></i>
														</th>
														<th>id</th>
														<th>Currency Pairs</th>
														<th>Interbank</th>
														<th>Sales</th>
														<th>Dealable FX Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
@foreach ($currency as $value)
                                                    <tr>
                                                        <td></td>
														<td>{{ $value->id }}</td>
                                                        <td data-primary-base-currency-code="{{ $value->baseCurrency->primary_code }}" data-primary-counter-currency-code="{{
                                                            $value->counterCurrency()->firstOrNew([], [
                                                                'primary_code' => ''
                                                            ])
                                                            ->primary_code
                                                        }}">
															{{
                                                                $value->baseCurrency->primary_code.'/'.(
                                                                    $value->counterCurrency()->firstOrNew([], [
                                                                        'primary_code' => 'IDR'
                                                                    ])
                                                                    ->primary_code
                                                                )
                                                            }}
														</td>
@if ($value->belongs_to_interbank)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
@if ($value->belongs_to_sales)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
@if ($value->dealable_fx_rate)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
                                                    </tr>
@endforeach
                                                </tbody>
                                            </table>
                                            <!-- datatable end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
                                <div id="panel-cross-currency-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
                                            Cross Currency <span class="fw-300"><i>Table</i></span>
                                        </h2>
                                    </div>
									<div class="panel-container show">
                                        <div class="panel-content">
											<!-- datatable start -->
                                            <table id="dt-cross-currency" class="table table-bordered table-hover table-striped w-100">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>
															<i class="fal fa-check"></i>
														</th>
														<th>id</th>
														<th>Currency Pairs</th>
														<th>Interbank</th>
														<th>Sales</th>
														<th>Dealable FX Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
@foreach ($crossCurrency as $value)
                                                    <tr>
                                                        <td class="select-checkbox text-center"></td>
														<td>{{ $value->id }}</td>
                                                        <td data-primary-base-currency-code="{{ $value->baseCurrency->primary_code }}" data-primary-counter-currency-code="{{
                                                            $value->counterCurrency()->firstOrNew([], [
                                                                'primary_code' => ''
                                                            ])
                                                            ->primary_code
                                                        }}">
															{{
                                                                $value->baseCurrency->primary_code.'/'.(
                                                                    $value->counterCurrency()->firstOrNew([], [
                                                                        'primary_code' => 'IDR'
                                                                    ])
                                                                    ->primary_code
                                                                )
                                                            }}
														</td>
@if ($value->belongs_to_interbank)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
@if ($value->belongs_to_sales)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
@if ($value->dealable_fx_rate)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
                                                    </tr>
@endforeach
                                                </tbody>
                                            </table>
                                            <!-- datatable end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
                                <div id="panel-special-currency-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
                                            Special Currency <span class="fw-300"><i>Table</i></span>
                                        </h2>
                                    </div>
									<div class="panel-container show">
                                        <div class="panel-content">
											<!-- datatable start -->
                                            <table id="dt-special-currency" class="table table-bordered table-hover table-striped w-100">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>
															<i class="fal fa-check"></i>
														</th>
														<th>id</th>
														<th>Currency Pairs</th>
														<th>Interbank</th>
														<th>Sales</th>
														<th>Dealable FX Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
@foreach ($specialCurrency as $value)
                                                    <tr>
                                                        <td class="select-checkbox text-center"></td>
														<td>{{ $value->id }}</td>
                                                        <td data-primary-base-currency-code="{{ $value->baseCurrency->primary_code }}" data-secondary-base-currency-code="{{ $value->baseCurrency->secondary_code }}" data-primary-counter-currency-code="{{
                                                            $value->counterCurrency()->firstOrNew([], [
                                                                'primary_code' => ''
                                                            ])
                                                            ->primary_code
                                                        }}" data-secondary-counter-currency-code="{{
                                                            $value->counterCurrency()->firstOrNew([], [
                                                                'secondary_code' => ''
                                                            ])
                                                            ->secondary_code
                                                        }}">
															{{
                                                                $value->baseCurrency->secondary_code.'/'.(
                                                                    $value->counterCurrency()->firstOrNew([], [
                                                                        'primary_code' => 'IDR'
                                                                    ])
                                                                    ->primary_code
                                                                )
                                                            }}
														</td>
@if ($value->belongs_to_interbank)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
@if ($value->belongs_to_sales)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
@if ($value->dealable_fx_rate)
														<td class="text-center text-success">
															<i class="fal fa-check-circle fa-2x"></i>
														</td>
@else
														<td class="text-center text-danger">
															<i class="fal fa-times-circle fa-2x"></i>
														</td>
@endif
                                                    </tr>
@endforeach
                                                </tbody>
                                            </table>
                                            <!-- datatable end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
					<!-- Modal default-->
					<div id="modal-currency" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<form action="{{ route('currencies.store') }}" method="post">
									@csrf
									
									<div class="modal-header">
										<h4 class="modal-title">
											currency
											<small class="m-0 text-muted">create currency</small>
											<small class="m-0 text-muted">edit currency</small>
										</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true"><i class="fal fa-times"></i></span>
										</button>
									</div>
									<div class="modal-body pt-0">
										<div class="form-group">
											<label class="form-label" for="currency-code">Currency Code</label>
											<input type="text" name="primary-base-currency-code" class="form-control text-uppercase" maxlength="3" autocomplete="off" required>
										</div>
										<div class="custom-control custom-switch mb-3">
											<input type="checkbox" name="belongs-to-interbank" class="custom-control-input" id="currency-switch-interbank">
											<label class="custom-control-label" for="currency-switch-interbank">Interbank</label>
										</div>
										<div class="custom-control custom-switch mb-3">
											<input type="checkbox" name="belongs-to-sales" class="custom-control-input" id="currency-switch-sales">
											<label class="custom-control-label" for="currency-switch-sales">Sales</label>
										</div>
										<div class="custom-control custom-switch mb-3 collapse">
											<input type="checkbox" name="dealable-fx-rate" class="custom-control-input" id="currency-switch-dealable-fx-rate">
											<label class="custom-control-label" for="currency-switch-dealable-fx-rate">Dealable FX Rate</label>
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
					<div id="modal-cross-currency" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<form action="{{ route('currencies.store') }}" method="post">
									@csrf
									
									<div class="modal-header">
										<h4 class="modal-title">
											cross currency
											<small class="m-0 text-muted">create cross currency</small>
											<small class="m-0 text-muted">edit cross currency</small>
										</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true"><i class="fal fa-times"></i></span>
										</button>
									</div>
									<div class="modal-body pt-0">
										<div class="form-group">
											<label class="form-label" for="cross-base-currency-code">Base Currency Code</label>
											<input type="text" name="primary-base-currency-code" class="form-control text-uppercase" maxlength="3" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="cross-counter-currency-code">Counter Currency Code</label>
											<input type="text" name="primary-counter-currency-code" class="form-control text-uppercase" maxlength="3" autocomplete="off" required>
										</div>
										<div class="custom-control custom-switch mb-3">
											<input type="checkbox" name="belongs-to-interbank" class="custom-control-input" id="cross-switch-interbank">
											<label class="custom-control-label" for="cross-switch-interbank">Interbank</label>
										</div>
										<div class="custom-control custom-switch mb-3">
											<input type="checkbox" name="belongs-to-sales" class="custom-control-input" id="cross-switch-sales">
											<label class="custom-control-label" for="cross-switch-sales">Sales</label>
										</div>
										<div class="custom-control custom-switch mb-3 collapse">
											<input type="checkbox" name="dealable-fx-rate" class="custom-control-input" id="cross-switch-dealable-fx-rate">
											<label class="custom-control-label" for="cross-switch-dealable-fx-rate">Dealable FX Rate</label>
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
					<div id="modal-special-currency" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<form action="{{ route('currencies.store') }}" method="post">
									@csrf
									
									<div class="modal-header">
										<h4 class="modal-title">
											special currency
											<small class="m-0 text-muted">create special currency</small>
											<small class="m-0 text-muted">edit special currency</small>
										</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true"><i class="fal fa-times"></i></span>
										</button>
									</div>
									<div class="modal-body pt-0">
										<div class="form-group">
											<label class="form-label" for="special-currency-code">Primary Currency Code</label>
											<input type="text" name="primary-base-currency-code" class="form-control" autocomplete="off" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="special-currency-code">Secondary Currency Code</label>
											<input type="text" name="secondary-base-currency-code" class="form-control" autocomplete="off" required>
										</div>
										<div class="custom-control custom-switch mb-3">
											<input type="checkbox" name="belongs-to-interbank" class="custom-control-input" id="special-switch-interbank">
											<label class="custom-control-label" for="special-switch-interbank">Interbank</label>
										</div>
										<div class="custom-control custom-switch mb-3">
											<input type="checkbox" name="belongs-to-sales" class="custom-control-input" id="special-switch-sales">
											<label class="custom-control-label" for="special-switch-sales">Sales</label>
										</div>
										<div class="custom-control custom-switch mb-3 collapse">
											<input type="checkbox" name="dealable-fx-rate" class="custom-control-input" id="special-switch-dealable-fx-rate">
											<label class="custom-control-label" for="special-switch-dealable-fx-rate">Dealable FX Rate</label>
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
					<script type="text/javascript">
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
							initApp.buildNavigation(myapp_config.navHooks);

							$('table').filter('#dt-currency,#dt-cross-currency,#dt-special-currency').DataTable({
								responsive: true,
								fixedHeader: {
									headerOffset: $(document.body).hasClass('header-function-fixed') ? $('header.page-header').outerHeight() : 0
								},
								paging: false,
								bInfo: false,
@can ('create', 'App\Currency')
								select: {
									style: 'multi',
									items: 'cell'
								},
								dom: "<'row mb-3'" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f>" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>" +
									">" +
									"<'row'<'col-sm-12'tr>>" +
									"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										text: '<span class="fal fa-plus-square mr-1"></span>Create',
										titleAttr: 'Create Currency',
										className: 'btn btn-outline-primary waves-effect waves-themed mr-1',
										action: function ( e, dt, node, config ) {
											$('.modal').filter(('#').concat(node.attr('aria-controls').replace('dt', 'modal')))
												.find('.modal-title .text-muted').eq(0).show();

											$('.modal').filter(('#').concat(node.attr('aria-controls').replace('dt', 'modal')))
												.find('.modal-title .text-muted').eq(1).hide();

											$('.modal').filter(('#').concat(node.attr('aria-controls').replace('dt', 'modal')))
											.find('[name$="currency-code"]').each( function(key, element) {
												element.value = '';
												element.readOnly = false;
											})

											$('.modal').filter(('#').concat(node.attr('aria-controls').replace('dt', 'modal')))
											.find('input[type="checkbox"]').each( function(key, element) {
												element.checked = false;
											})

											$('.modal').filter(('#').concat(node.attr('aria-controls').replace('dt', 'modal'))).modal();
										}
									},
									{
										text: '<span class="fal fa-times-square mr-1"></span>Delete',
										titleAttr: 'Delete Currency',
										className: 'btn btn-outline-danger waves-effect waves-themed collapse',
										action: function ( e, dt, node, config ) {
											dt.button().container().siblings().remove();
											dt.button().container().after(document.createElement('form'));
											dt.button().container().next()
												.attr('action', @json(route('currencies.destroy', ['currency' => 'deletes'])));
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
												dt.button().container().next().children().eq(rowLoop + 3).val(
													dt.cell({
														column: 1,
														row: rowIdx
													}).data()
												);
											})

											$('#modal-alert').modal();
										}
									}
								],
								columnDefs: [
									{
										targets: 0,
										className: 'select-checkbox text-center',
										width: '5%'
									},
                                    {
										targets: [-1, -2, -3],
										width: '20%'
									}
								],
@endcan
								searching: false,
								ordering: false,
								language: {
									emptyTable: 'No data available'
								},
								createdRow: function(row, data, dataIndex) {
									if (this.fnSettings().oInit.select) {
										$(row).addClass('pointer');
									}
								},
								initComplete: function(settings, json) {
									settings.oInstance.api().columns().header().to$().addClass('text-center');
									settings.oInstance.api().column(-5).visible(false);

									if (!settings.oInstance.api().column(0).init().select) {
										settings.oInstance.api().column(0).visible(false);
									}
								}

							}).on('select', function(e, dt, type, indexes) {
								dt.cells({
									selected: true
								})
								.deselect()

								if ((type === 'cell') && (indexes[0].column > 0)) {
									$('.modal').filter(('#').concat(e.currentTarget.closest('table').id.replace('dt', 'modal')))
										.find('.modal-title .text-muted').eq(1).show();

									$('.modal').filter(('#').concat(e.currentTarget.closest('table').id.replace('dt', 'modal')))
										.find('.modal-title .text-muted').eq(0).hide();

									$('.modal').filter(('#').concat(e.currentTarget.closest('table').id.replace('dt', 'modal')))
									.find('[name$="currency-code"]').each( function(key, element) {
                                        element.value = dt.cell({
												column: 2,
												row: indexes[0].row
											})
											.node()
											.dataset[(
												element.name.replace(/-(.)/g, function(match, offset, string) {
													return match.replace('-', '').toUpperCase();
												})
											)]

										element.readOnly = true;
									})

									$('.modal').filter(('#').concat(e.currentTarget.closest('table').id.replace('dt', 'modal')))
									.find('input[type="checkbox"]').each( function(key, element) {
										element.checked = (
											dt.cell({
												column: (3 + key),
												row: indexes[0].row
											})
											.node()
											.classList.contains('text-success') || (
												!dt.cell({
													column: (3 + key),
													row: indexes[0].row
												})
												.node()
												.classList.contains('text-danger')
											)
										);

                                        $(element).trigger('change');

									})

									$('.modal').filter(('#').concat(e.currentTarget.closest('table').id.replace('dt', 'modal'))).modal();

								} else if ((type === 'cell')) {
									if (dt.row(indexes[0].row).node().classList.contains('selected')) {
										dt.row(indexes[0].row).deselect()
									} else {
										dt.row(indexes[0].row).select()
									}

								} else {
									dt.button(1).node().collapse('show');
									dt.column(0).header().children[0].replaceWith(document.createElement('i'));
									dt.column(0).header().children[0].classList.add('fal', 'fa-times');
								}

							}).on('deselect', function(e, dt, type, indexes) {
								if ((type === 'row') && (dt.rows('.selected').count() < 1)) {
									dt.button(1).node().collapse('hide');
									dt.column(0).header().children[0].replaceWith(document.createElement('i'));
									dt.column(0).header().children[0].classList.add('fal', 'fa-check');
								}
							})

						})

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('input:not([type="hidden"], [type="checkbox"])')
						.on('input', function(e) {
							e.currentTarget.dataset.values = $(e.currentTarget).closest('.modal')
                                .find('input:not([type="hidden"], [type="checkbox"])').get()
								.map(element => element.value.toLowerCase())
								.join('');

							if ($.inArray(
								e.currentTarget.dataset.values,
								$(document).find('table.dataTable')
                                .filter(String('#dt-').concat(e.currentTarget.closest('.modal').id.replace('modal-', '')))
                                .find('[data-primary-base-currency-code]').get().flatMap(element => {
                                    return Object.keys(element.dataset).map((key) => element.dataset[key]).join('').toLowerCase();
                                })
							) >= 0) {
								$(e.currentTarget).tooltip('dispose');
								$(e.currentTarget).tooltip({
									trigger: 'manual',
									title: 'Alert! The currency already exists.',
									template: String('<div class="tooltip" role="tooltip">')
                                        .concat('<div class="arrow"></div>')
                                        .concat('<div class="tooltip-inner bg-danger-500 mw-100"></div>')
                                        .concat('</div>')
								}).tooltip('show');

							} else if (
								e.currentTarget.value &&
								($(e.currentTarget).closest('.modal').find('input:not([type="hidden"], [type="checkbox"])').length > 1) &&
								(
									e.currentTarget.value.toLowerCase() === (
										$(e.currentTarget).closest('.modal')
                                        .find('input:not([type="hidden"], [type="checkbox"])')
                                        .not(e.currentTarget)
										.val().toLowerCase()
									)
								)
							) {
								$(e.currentTarget).tooltip('dispose');
								$(e.currentTarget).tooltip({
									trigger: 'manual',
									title: 'Alert! The currency is same.',
									template: String('<div class="tooltip" role="tooltip">')
                                        .concat('<div class="arrow"></div>')
                                        .concat('<div class="tooltip-inner bg-danger-500 mw-100"></div>')
                                        .concat('</div>')
								}).tooltip('show');

							} else {
								$(e.currentTarget).closest('.modal').find('input:not([type="hidden"], [type="checkbox"])').tooltip('hide');
							}
						})

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('input[type="checkbox"]')
                        .not('[name="dealable-fx-rate"]').on('change', function(e) {
							if (e.currentTarget.checked) {
								$(e.currentTarget).closest('.modal-body').find('input[type="checkbox"]').not('[name="dealable-fx-rate"]').next()
                                .tooltip('hide');

							} else if (
								$(e.currentTarget).closest('.modal-body').find('input[type="checkbox"]').not(e.currentTarget)
                                .not('[name="dealable-fx-rate"]').is(':not(:checked)') && (
                                    $(e.currentTarget).closest('.modal-body').find('input[name="primary-base-currency-code"]').val()
                                )
							) {
								$(e.currentTarget.closest('.modal')).find('input[type="checkbox"]').not('[name="dealable-fx-rate"]').next().tooltip({
									trigger: 'manual',
									placement: 'right',
									title: 'Alert! Please check at least one box to proceed.',
									template: String('<div class="tooltip" role="tooltip">')
                                        .concat('<div class="arrow"></div>')
                                        .concat('<div class="tooltip-inner bg-danger-500 mw-100"></div>')
                                        .concat('</div>')

								}).tooltip('show');
							}

                            if (e.currentTarget.name === 'belongs-to-sales') {
                                $(e.currentTarget).closest('.modal-body').find('input[type="checkbox"][name="dealable-fx-rate"]').parent()
                                .collapse(
                                    e.currentTarget.checked ? (
                                        'show'
                                    ) : (
                                        'hide'
                                    )
                                );
                            }

						})

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('[type="submit"]').on('focus', function(e) {
							$(e.currentTarget).closest('form').find('[type="checkbox"]').trigger('change');
						})

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('form').on('submit', function(e) {
							if ($(e.currentTarget).find('[aria-describedby^="tooltip"]').length) {
								e.preventDefault();
							}
						})

					</script>
@endsection
