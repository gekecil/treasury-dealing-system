@extends('layouts.master')

@section('title', collect([
	route('sales-fx.index') => 'FX',
    route('sales-special-rate-deal.index') => 'Request for Fx Deal',
    route('sales-blotter.index') => 'Blotter',
    route('sales-top-ten-obox.index') => 'Top Ten OBOX',
])
->get(request()->url()).' - Sales')

@section('stylesheet')
@if (
	auth()->user()->can('create', 'App\Market') &&
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
        <link rel="stylesheet" media="screen, print" href="/css/formplugins/ion-rangeslider/ion-rangeslider.css">
        <link rel="stylesheet" media="screen, print" href="/css/notifications/sweetalert2/sweetalert2.bundle.css">
@endif
        <link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
        <link rel="stylesheet" media="screen, print" href="/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
@if (
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
        <link rel="stylesheet" media="screen, print" href="/css/formplugins/select2/select2.bundle.css">
@endif
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item"><a href="#sales">Sales</a></li>
                            <li class="breadcrumb-item active">
								{{
									collect([
										route('sales-fx.index') => 'FX',
                                        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
                                        route('sales-blotter.index') => 'Blotter',
                                        route('sales-top-ten-obox.index') => 'Top Ten OBOX',
									])->get(request()->url())
								}}
							</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
						<div class="subheader">
                            <h1 class="subheader-title">
@if (request()->route()->named('sales-fx.index'))
                                <i class='subheader-icon ni ni-my-apps'></i>
@elseif (request()->route()->named('sales-special-rate-deal.index'))
                                <i class='subheader-icon fal fa-cut'></i>
@elseif (request()->route()->named('sales-blotter.index'))
                                <i class='subheader-icon fal fa-archive'></i>
@elseif (request()->route()->named('sales-top-ten-obox.index'))
                                <i class='subheader-icon fal fa-arrow-alt-to-top'></i>
@endif
								{{
									collect([
										route('sales-fx.index') => 'FX',
                                        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
                                        route('sales-blotter.index') => 'Blotter',
                                        route('sales-top-ten-obox.index') => 'Top Ten OBOX',
									])
									->get(request()->url())
								}}
                            </h1>
                        </div>
						<div id="alert-dismissible" class="panel-container show">
							<div class="panel-content">
								<div class="alert alert-danger alert-dismissible fade" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true"><i class="fal fa-times"></i></span>
									</button>
									<strong>Alert!</strong> The threshold has not been setup.
								</div>
@if (session('status'))
								<div class="alert alert-success alert-dismissible fade show" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true"><i class="fal fa-times"></i></span>
									</button>
									<strong>Well Done!</strong> {{ session('status') }}
								</div>
@endif
							</div>
						</div>
@if (
	auth()->user()->can('create', 'App\Market') &&
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
						<div class="row">
							<div class="col-xl-12">
								<div id="panel-market-hour" class="panel">
									<div class="panel-hdr">
										<h2>
											Market hour
										</h2>
										<div class="panel-toolbar">
											<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
											<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
											<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
										</div>
									</div>
									<div class="panel-container show">
										<div class="panel-content">
@if ($market)
											<div class="form-row">
												<div class="form-group col-11 m-0 p-0">
													<input id="market-hour" type="text" data-market-id="{{ $market->id }}" value="" class="d-none" tabindex="-1" readonly="">
												</div>
												<div class="form-group col-1 m-0 p-0 d-flex justify-content-center align-items-center">
													<button class="btn btn-success btn-icon hover-effect-dot">
														<span class="fal fa-check"></span>
													</button>
												</div>
											</div>
@endif
										</div>
									</div>
								</div>
							</div>
						</div>
@endif
@if (
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
                        <div class="row">
							<div class="col-xl-12">
								<div class="panel panel-sales-deal">
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
@if (request()->route()->named('sales-fx.index'))
												<div class="col-sm-4">
													<div class="panel border-0 shadow-none">
														<div class="card-group">
															<div class="card">
																<div class="card-header text-center">
																	<h2 class="card-title">
																		<strong></strong>
																	</h2>
																</div>
																<div class="card-body">
																	<div class="row d-flex justify-content-center">
																		<div class="col-md-6 text-center collapse">
																			<div style="min-height: 4.25rem;">
																				<h2 class="h5">Bank Buy</h2>
																				<h3 class="h4 fw-400 collapse"></h3>
																			</div>
																			<button type="button" class="btn btn-danger m-sm-2" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)">
																				<strong>Bank Buy</strong>
																			</button>
																		</div>
																		<div class="col-md-6 text-center collapse">
																			<div style="min-height: 4.25rem;">
																				<h2 class="h5">Bank Sell</h2>
																				<h3 class="h4 fw-400 collapse"></h3>
																			</div>
																			<button type="button" class="btn btn-success m-sm-2" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)">
																				<strong>Bank Sell</strong>
																			</button>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
@elseif (request()->route()->named('sales-special-rate-deal.index'))
												<div class="col-sm-3">
													<div class="panel border-0 shadow-none">
														<div class="card-group border-0 shadow-none">
															<div class="card border-0 rounded-0">
																<div class="card-header text-center">
																	<h2 class="card-title"><strong></strong></h2>
																</div>
																<div class="row">
																	<div class="col-md-6 pr-0 text-left">
																		<button type="button" class="btn btn-danger shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Buy</strong></button>
																	</div>
																	<div class="col-md-6 pl-0 text-right">
																		<button type="button" class="btn btn-success shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Sell</strong></button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12">
								<div class="panel panel-sales-deal">
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
@if (request()->route()->named('sales-fx.index'))
												<div class="col-sm-4">
													<div class="panel border-0 shadow-none">
														<div class="card-group">
															<div class="card">
																<div class="card-header text-center">
																	<h2 class="card-title">
																		<strong></strong>
																	</h2>
																</div>
																<div class="card-body">
																	<div class="row d-flex justify-content-center">
																		<div class="col-md-6 text-center collapse">
																			<div style="min-height: 4.25rem;">
																				<h2 class="h5">Bank Buy</h2>
																				<h3 class="h4 fw-400 collapse"></h3>
																			</div>
																			<button type="button" class="btn btn-danger m-sm-2" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)">
																				<strong>Bank Buy</strong>
																			</button>
																		</div>
																		<div class="col-md-6 text-center collapse">
																			<div style="min-height: 4.25rem;">
																				<h2 class="h5">Bank Sell</h2>
																				<h3 class="h4 fw-400 collapse"></h3>
																			</div>
																			<button type="button" class="btn btn-success m-sm-2" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)">
																				<strong>Bank Sell</strong>
																			</button>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
@elseif (request()->route()->named('sales-special-rate-deal.index'))
												<div class="col-sm-3">
													<div class="panel border-0 shadow-none">
														<div class="card-group border-0 shadow-none">
															<div class="card border-0 rounded-0">
																<div class="card-header text-center">
																	<h2 class="card-title"><strong></strong></h2>
																</div>
																<div class="row">
																	<div class="col-md-6 pr-0 text-left">
																		<button type="button" class="btn btn-danger shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Buy</strong></button>
																	</div>
																	<div class="col-md-6 pl-0 text-right">
																		<button type="button" class="btn btn-success shadow-none w-100 rounded-0 waves-effect waves-themed" data-toggle="modal" data-target=".modal:not(.js-modal-settings):not(.modal-alert)"><strong>Bank Sell</strong></button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
@endif
@if (request()->route()->named('sales-blotter.index'))
						<div class="row">
                            <div class="col-xl-12">
                                <div id="panel-sales-blotter-export" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form action="{{ route('sales-blotter.excel') }}" method="post" target="_blank">
												@csrf
												
												<input type="hidden" name="branch-code" value>
												<div class="form-row d-flex justify-content-between">
													<div class="form-group col-12 col-md-6">
														<label class="form-label" for="datepicker-from">Date from</label>
                                                        <div class="input-group">
                                                            <input type="text" name="date_from" class="form-control datepicker" placeholder="Select date" data-date-end-date="{{
                                                                \Carbon\Carbon::today()->toDateString()
                                                            }}" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text fs-xl">
                                                                    <i class="fal fa-calendar"></i>
                                                                </span>
                                                            </div>
                                                        </div>
													</div>
													<div class="form-group col-12 col-md-6">
														<label class="form-label" for="datepicker-to">to</label>
                                                        <div class="input-group">
                                                            <input type="text" name="date_to" class="form-control datepicker" placeholder="Select date" data-date-end-date="{{
                                                                \Carbon\Carbon::today()->toDateString()
                                                            }}" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text fs-xl">
                                                                    <i class="fal fa-calendar"></i>
                                                                </span>
                                                            </div>
                                                        </div>
													</div>
												</div>
												<button type="button" class="d-none" data-toggle="modal" data-target="#modal-alert"></button>
											</form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
@endif
						<div class="row">
                            <div class="col-xl-12">
                                <div id="panel-sales-deal-index" class="panel">
                                    <div class="panel-hdr bg-faded">
                                        <h2>
@if (
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
											Reporting Daily <span class="fw-300"><i>Table</i></span>
@else
											Deal <span class="fw-300"><i>Table</i></span>
@endif
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
                                            <table id="dt-advance" class="table table-bordered table-hover table-striped w-100">
                                                <thead class="thead-dark">
                                                    <tr>
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
        route('sales-blotter.index') => 'Blotter',
    ])->has(request()->url()) && (
        auth()->user()->can('view', new App\SalesDeal)
    )
)
														<th>Branch</th>
@endif
                                                        <th>Customer Name</th>
														<th>TT/BN</th>
@if (request()->route()->named('sales-top-ten-obox.index'))
                                                        <th>Value</th>
@endif
@if (request()->route()->named('sales-blotter.index'))
                                                        <th>CIF</th>
@endif
														<th>Currency Pairs</th>
														<th>Base Amount</th>
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
        route('sales-blotter.index') => 'Blotter',
    ])->has(request()->url())
)
														<th>Customer Rate</th>
														<th>Interoffice Rate</th>
														<th>Buy/Sell</th>
														<th>Created At</th>
														<th>Status</th>
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
    ])->has(request()->url())
)
														<th>SISMONTAVAR</th>
@endif
@elseif (request()->route()->named('sales-top-ten-obox.index'))
														<th>Counter Amount</th>
														<th>USD Equivalent</th>
@endif
                                                    </tr>
@if (
    auth()->user()->can('view', new App\SalesDeal) && request()->route()->named('sales-blotter.index')
)
													<tr>
														<th>
															<div class="form-group">
																<select class="form-control">
																	<option value>Choose</option>
																</select>
															</div>
														</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
                                                    </tr>
@endif
                                                </thead>
                                                <tbody></tbody>
                                            </table>
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
@endif
@endsection

@section('javascript')
@if (
	auth()->user()->can('create', 'App\Market') &&
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
					<script src="/js/formplugins/ion-rangeslider/ion-rangeslider.js"></script>
                    <script src="/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
@endif
					<script src="/js/datagrid/datatables/datatables.bundle.js"></script>
					<script src="/moment/min/moment.min.js"></script>
					<script src="/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
@if (
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
					<script src="/js/formplugins/select2/select2.bundle.js"></script>
@elseif (request()->route()->named('sales-top-ten-obox.index'))
					<script src="/js/datagrid/datatables/datatables.export.js"></script>
@endif
                    <script type="text/javascript">
						var countDown;
						var oldFrom;
						var oldTo;
						
						var requestSalesDeal = function() {
							$.ajax({
								method: 'GET',
								url: @json(route('api.currencies.index')),
								data: {
									api_token: $(document).find('meta[name="api-token"]').attr('content'),
									csrf_token: $(document).find('meta[name="csrf-token"]').attr('content'),
                                    is_interbank_dealing: 0
								}
							}).done( function(response) {
								responseSalesDeal(response);

							}).fail( function(jqXHR, textStatus, errorThrown) {
								$('.panel-sales-deal .panel-content > .row > *').parent().collapse('hide');

								if (jqXHR.status === 429) {
									window.setTimeout(requestSalesDeal, (jqXHR.getResponseHeader('Retry-After') * 1000));
								}
							})

						};
						
						var responseSalesDeal = function(response) {
							window.setTimeout(requestSalesDeal, 1000);

                            response.data.base_currency_rate = response.data.currency.filter(currency_rate => currency_rate.belongs_to_sales);

							response.column = $('.panel-sales-deal:eq(0) .panel-content > .row > *');

							response.data.base_currency_rate = response.data.base_currency_rate.filter(
									currency_rate => (
										response.data.closing_rate.find(
											closing_rate => closing_rate.currency.primary_code === currency_rate.base_currency.primary_code
@if (request()->route()->named('sales-fx.index'))
										) && (
											moment().isSame(currency_rate.updated_at, 'day')
										) && (
											currency_rate.dealable_fx_rate
                                        ) && (
											currency_rate.buying_rate || currency_rate.selling_rate
@endif
										)
									)
								)
								.concat(
									response.data.special_currency.filter(currency_rate => currency_rate.belongs_to_sales).filter(
										special_currency_rate => (
											!special_currency_rate.counter_currency_id && (
												response.data.closing_rate.find(
													closing_rate => (
                                                        closing_rate.currency.primary_code === special_currency_rate.base_currency.primary_code
                                                    )
												)
@if (request()->route()->named('sales-fx.index'))
											) && (
                                                moment().isSame(special_currency_rate.updated_at, 'day')
                                            ) && (
                                                special_currency_rate.dealable_fx_rate
                                            ) && (
												special_currency_rate.buying_rate || special_currency_rate.selling_rate
@endif
											)
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
								response.data.closing_rate.find(closing_rate => closing_rate.is_world_currency) && (
                                    $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)')
                                    .find('[name="sismontavar-threshold"]')
                                    .val()
                                    .length
                                ) && (
                                    $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('[name="threshold"]').val().length
                                ) && (
                                    $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)')
                                    .find('[name="sales-limit"]')
                                    .val()
                                    .length || (
                                        @json(auth()->user()->is_super_administrator)
                                    )
                                ) && (
                                    response.data.base_currency_rate.length
                                )
							) {
								response.column.parent().collapse('show');
								
								$.each(response.data.base_currency_rate, function(key, value) {
									if (key in response.column) {
										value.column = response.column.get(key);
									} else {
										value.column = response.column.get(0).cloneNode('true');
									}

									value.column.dataset.baseCurrencyClosingRate = response.data.closing_rate.find(
										closing_rate => (closing_rate.currency.primary_code === value.base_currency.primary_code)
									)
                                    .mid_rate;

									value.worldCurrencyClosingRate = response.data.closing_rate.find(closing_rate => closing_rate.is_world_currency);
									value.column.dataset.worldCurrencyCode = value.worldCurrencyClosingRate.currency.primary_code;
									value.column.dataset.worldCurrencyClosingRate = value.worldCurrencyClosingRate.mid_rate;

                                    value.column.dataset.basePrimaryCode = value.base_currency.primary_code;
                                    value.column.dataset.baseSecondaryCode = value.base_currency.secondary_code;
                                    value.column.dataset.encryptedQueryString = value.encrypted_query_string;

									value.column.querySelector('.card-title strong').innerHTML = value.base_currency.secondary_code;
									value.column.querySelector('.card-title strong').innerHTML += '/';
									value.column.querySelector('.card-title strong').innerHTML += value.counter_currency_id ? value.counter_currency.primary_code : 'IDR';

									if (!(key in response.column)) {
										response.column.get(0).parentElement.appendChild(value.column);
									}

									if (
										$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400')
										.attr('data-text') !== value.buying_rate
									) {
										if (parseFloat(value.buying_rate)) {
											$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400')
												.attr('data-text', value.buying_rate);
											$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400').removeClass('show');
											$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400').collapse('show');
											
											if (!$(value.column).find('.card-body > .row').children().eq(0).hasClass('show')) {
												$(value.column).find('.card-body > .row').children().eq(0).collapse('show');
											}
											
										} else {
											$(value.column).find('.card-body > .row').children().eq(0).collapse('hide');
										}
									}

									if (
										$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400')
										.attr('data-text') !== value.selling_rate
									) {
										if (parseFloat(value.selling_rate)) {
											$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400')
												.attr('data-text', value.selling_rate);
											$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400').removeClass('show');
											$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400').collapse('show');
											
											if (!$(value.column).find('.card-body > .row').children().eq(1).hasClass('show')) {
												$(value.column).find('.card-body > .row').children().eq(1).collapse('show');
											}
											
										} else {
											$(value.column).find('.card-body > .row').children().eq(1).collapse('hide');
										}
									}
								})

							} else {
								response.column.parent().collapse('hide');
							}

							response.column.filter(':gt(' + (response.data.base_currency_rate.length - 1) + ')')
							.each( function(key, element) {
								element.remove();
							})

							response.data.cross_currency_rate = response.data.cross_currency.filter(
								cross_currency_rate => cross_currency_rate.belongs_to_sales
							);
							
							response.column = $('.panel-sales-deal:eq(1) .panel-content > .row > *');
							
							response.data.cross_currency_rate = response.data.cross_currency_rate.filter(
									cross_currency_rate => (
										response.data.closing_rate.find(
											closing_rate => closing_rate.currency.primary_code === cross_currency_rate.base_currency.primary_code
										) && (
											response.data.closing_rate.find(
												closing_rate => (
                                                    closing_rate.currency.primary_code === cross_currency_rate.counter_currency.primary_code
                                                )
											)
@if (request()->route()->named('sales-fx.index'))
										) && (
											moment().isSame(cross_currency_rate.updated_at, 'day')
										) && (
											cross_currency_rate.dealable_fx_rate
                                        ) && (
											cross_currency_rate.buying_rate || cross_currency_rate.selling_rate
@endif
										)
									)
								).concat(
									response.data.special_currency.filter(cross_currency_rate => cross_currency_rate.belongs_to_sales).filter(
										special_currency_rate => (
											special_currency_rate.counter_currency_id && (
												response.data.closing_rate.find(
													closing_rate => (
                                                        closing_rate.currency.primary_code === special_currency_rate.base_currency.primary_code
                                                    )
												)
											) && (
												response.data.closing_rate.find(
													closing_rate => (
                                                        closing_rate.currency.primary_code === special_currency_rate.counter_currency.primary_code
                                                    )
												)
@if (request()->route()->named('sales-fx.index'))
											) && (
                                                moment().isSame(special_currency_rate.updated_at, 'day')
                                            ) && (
                                                special_currency_rate.dealable_fx_rate
                                            ) && (
												special_currency_rate.buying_rate || special_currency_rate.selling_rate
@endif
											)
										)
									)
								).filter(
                                    cross_currency_rate => (
                                        response.data.base_currency_rate.find(
                                            base_currency_rate => (
                                                (
                                                    base_currency_rate.base_currency.primary_code
                                                ) === (
                                                    cross_currency_rate.base_currency.primary_code
                                                ) && (
                                                    base_currency_rate.buying_rate || base_currency_rate.selling_rate
                                                )
                                            )
                                        )
                                    )
                                )
                                .map((value) => {
                                    if (!value.base_currency.secondary_code) {
                                        value.base_currency.secondary_code = value.base_currency.primary_code;
                                    }

                                    if (!value.counter_currency.secondary_code) {
                                        value.counter_currency.secondary_code = value.counter_currency.primary_code;
                                    }

                                    return value;
                                });

							if (
								response.data.closing_rate.find(closing_rate => closing_rate.is_world_currency) && (
                                    $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('[name="threshold"]').val().length
                                ) && (
                                    $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('[name="sales-limit"]').val().length || (
                                        @json(auth()->user()->is_super_administrator)
                                    )
                                ) && (
                                    response.data.cross_currency_rate.length
                                )
							) {
								response.column.parent().collapse('show');
								
								$.each(response.data.cross_currency_rate, function(key, value) {
									if (key in response.column) {
										value.column = response.column.get(key);
									} else {
										value.column = response.column.get(0).cloneNode('true');
									}

									value.column.dataset.baseCurrencyClosingRate = response.data.closing_rate.find(
										closing_rate => (closing_rate.currency.primary_code === value.base_currency.primary_code)
									).mid_rate;

									value.worldCurrencyClosingRate = response.data.closing_rate.find(closing_rate => closing_rate.is_world_currency);
									value.column.dataset.worldCurrencyCode = value.worldCurrencyClosingRate.currency.primary_code;
									value.column.dataset.worldCurrencyClosingRate = value.worldCurrencyClosingRate.mid_rate;

									value.column.dataset.basePrimaryCode = value.base_currency.primary_code;
									value.column.dataset.baseSecondaryCode = value.base_currency.secondary_code;
									value.column.dataset.counterPrimaryCode = value.counter_currency.primary_code;
									value.column.dataset.counterSecondaryCode = value.counter_currency.secondary_code;

									value.column.dataset.encryptedQueryString = value.encrypted_query_string;

									value.column.querySelector('.card-title strong').innerHTML = value.base_currency.secondary_code;
									value.column.querySelector('.card-title strong').innerHTML += '/';
									value.column.querySelector('.card-title strong').innerHTML += value.counter_currency_id ? value.counter_currency.primary_code : 'IDR';

									if (!(key in response.column)) {
										response.column.get(0).parentElement.appendChild(value.column);
									}

									if (
										$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400')
										.attr('data-text') !== value.buying_rate
									) {
										if (
                                            parseFloat(value.buying_rate) && (
                                                parseFloat(
                                                    response.data.base_currency_rate.find(
                                                        base_currency_rate => (
                                                            (
                                                                base_currency_rate.base_currency.primary_code
                                                            ) === (
                                                                value.base_currency.primary_code
                                                            )
                                                        )
                                                    )
                                                    .buying_rate
                                                )
                                            )
                                        ) {
											$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400')
												.attr('data-text', value.buying_rate);
											$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400').removeClass('show');
											$(value.column).find('.card-body > .row').children().eq(0).find('.h4.fw-400').collapse('show');
											
											if (!$(value.column).find('.card-body > .row').children().eq(0).hasClass('show')) {
												$(value.column).find('.card-body > .row').children().eq(0).collapse('show');
											}
											
										} else {
											$(value.column).find('.card-body > .row').children().eq(0).collapse('hide');
										}
									}

									if (
										$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400')
										.attr('data-text') !== value.selling_rate
									) {
										if (
                                            parseFloat(value.selling_rate) && (
                                                parseFloat(
                                                    response.data.base_currency_rate.find(
                                                        base_currency_rate => (
                                                            (
                                                                base_currency_rate.base_currency.primary_code
                                                            ) === (
                                                                value.base_currency.primary_code
                                                            )
                                                        )
                                                    )
                                                    .selling_rate
                                                )
                                            )
                                        ) {
											$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400')
												.attr('data-text', value.selling_rate);
											$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400').removeClass('show');
											$(value.column).find('.card-body > .row').children().eq(1).find('.h4.fw-400').collapse('show');
											
											if (!$(value.column).find('.card-body > .row').children().eq(1).hasClass('show')) {
												$(value.column).find('.card-body > .row').children().eq(1).collapse('show');
											}
											
										} else {
											$(value.column).find('.card-body > .row').children().eq(1).collapse('hide');
										}
									}
								})

							} else {
								response.column.parent().collapse('hide');
							}

							response.column.filter(':gt(' + (response.data.cross_currency_rate.length - 1) + ')')
							.each( function(key, element) {
								element.remove();
							})
						};
						
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
							initApp.buildNavigation(myapp_config.navHooks);

@if (
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
                            $(document).find('select[name="lhbu-remarks-code"]').select2({
                                dropdownParent: $(document).find('select[name="lhbu-remarks-code"]').parent(),
                                containerCssClass: 'mb-2',
                                data: @json(
                                    $lhbuRemarksCode->prepend((object) collect(['id'=> ''])->merge(['text' => ucfirst('kode tujuan')])->toArray())
                                    ->toArray()
                                )
                            })

                            $(document).find('select[name="lhbu-remarks-kind"]').select2({
                                dropdownParent: $(document).find('select[name="lhbu-remarks-kind"]').parent(),
                                data: @json(
                                    $lhbuRemarksKind->prepend((object) collect(['id'=> ''])->merge(['text' => ucfirst('jenis dokumen')])->toArray())
                                    ->toArray()
                                )
                            })
                            .on('select2:select', function(e) {
                                if (parseInt(e.params.data.id) === @json(
                                    $lhbuRemarksKind->firstWhere('name', 'dengan underlying lainnya')->id
                                )) {
                                    $(document).find('select[name="lhbu-remarks-kind"]')
                                    .parent()
                                    .children('[name="other-lhbu-remarks-kind"]')
                                    .prop('required', true);

                                    $(document).find('select[name="lhbu-remarks-kind"]')
                                    .parent()
                                    .children('[name="other-lhbu-remarks-kind"]')
                                    .collapse('show');

                                } else {
                                    $(document).find('select[name="lhbu-remarks-kind"]')
                                    .parent()
                                    .children('[name="other-lhbu-remarks-kind"]')
                                    .prop('required', false);

                                    $(document).find('select[name="lhbu-remarks-kind"]')
                                    .parent()
                                    .children('[name="other-lhbu-remarks-kind"]')
                                    .collapse('hide');
                                }
                            })
@endif

@if (
	auth()->user()->can('create', 'App\Market') &&
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url()) &&
    $market
)
							$('#market-hour').ionRangeSlider({
								grid: true,
								drag_interval: true,
								skin: 'flat',
								type: 'double',
								min: moment('0800', 'hhmm').valueOf(),
								max: moment('1700', 'hhmm').valueOf(),
								from: moment(@json($market->opening_at->toTimeString()), 'hh:mm:ss').valueOf(),
								to: moment(@json($market->closing_at->toTimeString()), 'hh:mm:ss').valueOf(),
								prettify: function(n) {
									return moment(n).format('HH:mm');
								},
								onStart: function(data) {
									if (moment(data.from).isBefore(moment(data.to)) && !$('#panel-market-hour .btn-icon').hasClass('btn-danger')) {
										$('#panel-market-hour .btn.btn-success').addClass('btn-danger');
										$('#panel-market-hour .btn.btn-success').find('.fal').addClass('fa-times');
										
									} else {
@if ($marketTrashed)
										oldFrom = moment(@json($marketTrashed->opening_at->toTimeString()), 'hh:mm:ss').valueOf();
										oldTo = moment(@json($marketTrashed->closing_at->toTimeString()), 'hh:mm:ss').valueOf();
@endif
									}
								},
								onFinish: function(data) {
                                    $.ajax({
										headers: {
											'X-CSRF-TOKEN': $(document).find('meta[name="csrf-token"]').attr('content')
										},
										method: 'POST',
										url: @json(route('api.markets.update', ['market' => 0], false)).replace(
                                            /[0-9]+/g,
                                            $('#market-hour').get(0).dataset.marketId
                                        ),

										data: {
											_method: 'PUT',
											api_token: $(document).find('meta[name="api-token"]').attr('content'),
											opening_at: moment(data.from).format('HH:mm:ss'),
											closing_at: moment(data.to).format('HH:mm:ss')
										}
									})
                                    .done( function(response) {
                                        $('#market-hour').get(0).dataset.marketId = response.data.id;
                                    })
                                    .fail( function(jqXHR, textStatus, errorThrown) {
                                        Swal.fire('Oops...', jqXHR.responseJSON.message, 'error');
									});
								}
							})
@endif

@if (
	collect([
		route('sales-fx.index') => 'FX', route('sales-special-rate-deal.index') => 'Request for Fx Deal'
	])
	->has(request()->url())
)
							window.setTimeout(requestSalesDeal, 1000);
							
@endif
							$.fn.dataTable.ext.errMode = 'throw';
							
							dtAdvance = $('#dt-advance').DataTable({
								responsive: true,
								lengthChange: false,
								paging: true,
								pageLength: 50,
								bInfo: false,
@can ('create', 'App\SalesDeal')
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
        route('sales-blotter.index') => 'Blotter',
    ])->has(request()->url())
)
								order: [],
								orderCellsTop: true,
								searching: true,
								searchable: true,
								select: true,
@elseif (request()->route()->named('sales-top-ten-obox.index'))
								ordering: false,
@endif
@if (
    collect([
		route('sales-blotter.index') => 'Blotter',
        route('sales-top-ten-obox.index') => 'Top Ten OBOX',
	])
	->has(request()->url())
)
								dom: "<'row mb-3'" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f>" +
									"<'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>" +
									">" +
									"<'row'<'col-sm-12'tr>>" +
									"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										text: '<span class="fal fa-download mr-1"></span>Excel',
										titleAttr: 'Generate Excel',
										className: 'btn btn-outline-primary waves-effect waves-themed',
@if (request()->route()->named('sales-blotter.index'))
										action: function() {
											$(document).find('#panel-sales-blotter-export').find('[data-toggle="modal"][data-target="#modal-alert"]').click();
										}
@elseif (request()->route()->named('sales-top-ten-obox.index'))
                                        extend: 'excelHtml5'
@endif
									}
								],
@endif
@endcan
@if (request()->route()->named('sales-blotter.index'))
								serverSide: true,
								processing: true,
@endif
								ajax: {
									method: 'GET',
									url: @json(route('api.sales-deals.index')),
									data: function(params) {
										params.api_token = $(document).find('meta[name="api-token"]').attr('content');
@if (request()->route()->named('sales-fx.index'))
										params.is_sales_fx = true;
@elseif (request()->route()->named('sales-special-rate-deal.index'))
										params.is_sales_special_rate_deal = true;
@endif
										if ($(document).find('input[name="date_from"]').length) {
                                            params.date_from = $(document).find('input[name="date_from"]').val();

                                        } else {
                                            params.date_from = moment().startOf('day').format();
                                        }

                                        if ($(document).find('input[name="date_to"]').length) {
                                            params.date_to = $(document).find('input[name="date_to"]').val();

                                        } else {
                                            params.date_to = moment().startOf('day').format();
                                        }
@if (
	collect([
		route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
        route('sales-top-ten-obox.index') => 'Top Ten OBOX',
	])
	->has(request()->url())
)
									},
                                    dataSrc: function(json) {
@if (request()->route()->named('sales-top-ten-obox.index'))
                                        json.data = json.data.map((value) => {
                                            if (value.currency_pair.base_currency_id === 1) {
                                                value.usd_equivalent = parseFloat(value.amount);

                                            } else {
                                                value.usd_equivalent = parseFloat(value.base_currency_closing_rate.mid_rate);
                                                value.usd_equivalent *= parseFloat(value.amount);
                                                value.usd_equivalent /= parseFloat(value.base_currency_closing_rate.world_currency_closing_mid_rate);
                                            }

                                            return value;
                                        })

                                        json.data = json.data
                                            .sort((x, y) => {
                                                return y.usd_equivalent - x.usd_equivalent;
                                            })
                                            .filter(value => value.usd_equivalent >= 0)
                                            .slice(0, 10)
                                            .concat(
                                                json.data
                                                .sort((x, y) => {
                                                    return y.usd_equivalent - x.usd_equivalent;
                                                })
                                                .filter(value => value.usd_equivalent < 0)
                                                .slice(-10)
                                            )
                                            .filter(value => !value.currency_pair.counter_currency);

@else
                                        $(document).find('[name="sales-limit"]').val(json.sales_limit);
                                        $(document).find('[name="threshold"]').val(json.threshold);

                                        if (!json.threshold) {
                                            $(document).find('#js-page-content').find('#alert-dismissible').find('.alert').addClass('show');
                                            
                                        } else if ($(document).find('#js-page-content').find('#alert-dismissible').find('.alert').hasClass('show')) {
                                            $(document).find('#js-page-content').find('#alert-dismissible').find('.alert').removeClass('show');
                                        }
@endif

                                        return json.data;
@endif
                                    }
								},
								columns: [
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
        route('sales-blotter.index') => 'Blotter',
    ])->has(request()->url()) && (
        auth()->user()->can('view', new App\SalesDeal)
    )
)
									{
										data: 'branch.name'
									},
@endcan
									{
										data: 'account.name',
										render: function(data, type, row, meta) {
											return data.trim();
										}
									},
									{
										data: 'tt_or_bn.name',
										className: 'text-center text-uppercase'
									},
@if (request()->route()->named('sales-top-ten-obox.index'))
									{
										data: 'tod_or_tom_or_spot_or_forward.name',
										className: 'text-center text-uppercase'
									},
@endif
@if (request()->route()->named('sales-blotter.index'))
                                    {
										data: 'account.cif',
                                        className: 'text-center'
									},
@endif
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
											data = parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: data.split('.').slice(1).join().length
											});
											
											row.element = document.createElement('span');
											row.element.innerHTML = data;
											
											if (
												row.can_upload_underlying && (
                                                    meta.settings.json.threshold
                                                ) && (
													row.monthly_usd_equivalent > parseFloat(meta.settings.json.threshold)
												)
											) {
												if (row.sales_deal_file && row.sales_deal_file.confirmed) {
													row.element.classList.add('text-warning');
													
												} else {
													row.element.classList.add('text-danger');
												}
											}
											
											return row.element.outerHTML;
										}
									},
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
        route('sales-blotter.index') => 'Blotter',
    ])->has(request()->url())
)
									{
										data: 'customer_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											return parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: 2,
												maximumFractionDigits: data.split('.').slice(1).join().length
											});
										}
									},
									{
										data: 'interoffice_rate',
										className: 'text-right',
										render: function(data, type, row, meta) {
											data = parseFloat(data).toLocaleString('en-US', {
												minimumFractionDigits: 2,
												maximumFractionDigits: data.split('.').slice(1).join().length
											});
											
											row.element = document.createElement('span');
											row.element.innerHTML = data;
											
											if (row.special_rate_deal) {
												if (row.special_rate_deal.confirmed) {
													row.element.classList.add('text-warning');
													
												} else {
													row.element.classList.add('text-danger');
												}
											}
											
											return row.element.outerHTML;
										}
									},
									{
										data: 'buy_or_sell.name',
										className: 'text-center text-capitalize',
										render: function(data, type, row, meta) {
											return ('bank').concat(' ').concat(data);
										}
									},
@elseif (request()->route()->named('sales-top-ten-obox.index'))
									{
										className: 'text-right',
										render: function(data, type, row, meta) {
                                            data = row.customer_rate;
                                            data *= row.amount;

                                            return data.toLocaleString('en-US', {
												maximumFractionDigits: 2
											});
										}
									},
                                    {
										data: 'usd_equivalent',
										className: 'text-right',
										render: function(data, type, row, meta) {
                                            return Math.abs(data).toLocaleString('en-US', {
												maximumFractionDigits: 2
											});
										}
									}
@endif
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
        route('sales-blotter.index') => 'Blotter',
    ])->has(request()->url())
)
									{
										data: 'created_at',
										className: 'text-center',
										render: function(data, type, row, meta) {
											return moment(data).format('lll');
										}
									},
									{
										className: 'text-center',
                                        orderable: false,
										render: function(data, type, row, meta) {
											row.element = document.createElement('span');

                                            if (
                                                row.can_upload_underlying && (
                                                    meta.settings.json.threshold
                                                ) && (
													row.monthly_usd_equivalent > parseFloat(meta.settings.json.threshold)
												) && (
													!row.sales_deal_file
												)
											)
											{
												row.element.classList.add('badge', 'badge-primary', 'badge-pill');
												row.element.innerHTML = 'Attention';
												
											} else if (
												(row.special_rate_deal && !row.special_rate_deal.confirmed) || (
													row.modification_updated && !row.modification_updated.confirmed
												) || (
													(row.sales_deal_file && !row.sales_deal_file.confirmed)
												)
											)
											{
												row.element.classList.add('badge', 'badge-warning', 'badge-pill');
												row.element.innerHTML = 'Pending';
												
											} else {
												row.element.classList.add('badge', 'badge-success', 'badge-pill');
												row.element.innerHTML = 'Success';
											}
											
											return row.element.outerHTML;
										}
									}
@endif
@if (
    collect([
        route('sales-fx.index') => 'FX',
        route('sales-special-rate-deal.index') => 'Request for Fx Deal',
    ])->has(request()->url())
)
									,{
										className: 'text-center',
                                        orderable: false,
										render: function(data, type, row, meta) {
											row.element = document.createElement('span');

                                            if (row.sismontavar_deal)
                                            {
                                                if (parseFloat(row.sismontavar_deal.status_code) === 200)
                                                {
                                                    row.element.classList.add('badge', 'badge-success', 'badge-pill');
                                                    row.element.innerHTML = 'Success';

                                                } else {
                                                    row.element.classList.add('badge', 'badge-primary', 'badge-pill');
                                                    row.element.innerHTML = 'Attention';
                                                }
                                            }

											return row.element.outerHTML;
										}
									}
@endif
								],
								language: {
									infoFiltered: ''
								},
@can ('create', 'App\SalesDeal')
								createdRow: function(row, data, dataIndex) {
									$(row).addClass('pointer');

@if (request()->route()->named('sales-top-ten-obox.index'))
                                    if (data.usd_equivalent < 0) {
                                        $(row).addClass('table-success');

                                    } else {
                                        $(row).addClass('table-danger');
                                    }

@endif
								},
@endcan
								initComplete: function(settings, json) {
                                    settings.oInstance.api().columns().header().to$().addClass('text-center');

@if (
    auth()->user()->can('view', new App\SalesDeal) && request()->route()->named('sales-blotter.index')
)
									$(settings.oInstance.api().table().header().querySelector('select')).on('change', function (e) {
										if (e.currentTarget.options[e.currentTarget.selectedIndex].value) {
                                            settings.oInstance.api().column(0)
                                            .search(e.currentTarget.options[e.currentTarget.selectedIndex].text)
                                            .draw();                                            
                                        }

									});

									$.each(json.branch, function(key, value) {
										settings.oInstance.api().table().header().querySelector('select').appendChild(new Option(value, key));
									});
@endif

									window.setInterval( function () {
										settings.oInstance.api().ajax.reload(null, false);
									}, 15000 );

									$(settings.oInstance).closest('main').find('form input.datepicker').on('change', function(e) {
										settings.oInstance.api().ajax.reload(null, false);
									})

								}
							});
							
							dtAdvance.on('search', function(e, dt, type, indexes) {
								if ($(e.currentTarget).find('thead tr').last().children().first().find('option:selected')) {
									$(e.currentTarget).closest('main').find('form input[name="branch-code"]')
										.val($(e.currentTarget).find('thead tr').last().children().first().find('option:selected').val());
								}
							})
							
@if (!$market)
							$(document).find('#panel-market-hour > .panel-container > .panel-content').find('.btn').prop('disabled', true);
@endif
							
						})
						
						$('input.datepicker').on('change', function(e) {
							var sibling = $(e.currentTarget).closest('.form-group').siblings().find('input.datepicker');
							
							if ($(e.currentTarget).is('input[name="date_from"]')) {
								if (moment(e.currentTarget.value).isBefore(moment(sibling.data().dateEndDate), 'year')) {
									sibling.datepicker('setEndDate', moment(e.currentTarget.value).endOf('year').format('YYYY-MM-DD'));
								}
							} else {
								if (moment(e.currentTarget.value).isBefore(moment(sibling.data().dateEndDate))) {
									sibling.datepicker('setEndDate', moment(e.currentTarget.value).format('YYYY-MM-DD'));
								}
							}
						})
						
						$(document).find('#panel-market-hour > .panel-container > .panel-content').find('.btn').on('click', function(e) {
							let dataIon = $(e.currentTarget).closest('.panel').find('#market-hour').data('ionRangeSlider');
							
							if ($(e.currentTarget).is('.btn-danger')) {
								oldFrom = dataIon.result.from;
								oldTo = dataIon.result.to;
								
								dataIon.update({
									from: dataIon.result.min,
									to: dataIon.result.min
								});
							} else {
								dataIon.update({
									from: oldFrom,
									to: oldTo
								});
							}
							
							dataIon.options.onFinish(dataIon.result);
							$(e.currentTarget).toggleClass('btn-danger');
							$(e.currentTarget).find('.fal').toggleClass('fa-times');
						})
						
						$(document).on('show.bs.collapse', '.panel-sales-deal .card-body .h4.fw-400', function(e) {
							e.currentTarget.innerHTML = parseFloat(e.currentTarget.dataset.text).toLocaleString('en-US', {
								maximumFractionDigits: e.currentTarget.dataset.text.split('.').slice(1).join().length
							});
						});

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('show.bs.modal', function(e) {
@if (request()->route()->named('sales-fx.index'))
							let countDownTime = moment().add(1, 'm');

							if (countDown) {
								window.clearInterval(countDown);
							}

							countDown = window.setInterval(() => {
								let seconds = moment(countDownTime.diff(moment())).format('ss');

								if (moment().isSameOrAfter(countDownTime)) {
									$(e.currentTarget).modal('hide');
									window.clearInterval(countDown);
								} else {
									$(e.currentTarget).find('.modal-header time').text('00' + ':' + seconds);
								}
							}, 500);

							$(e.currentTarget).find('input[name="encrypted-query-string"]').val(
								e.relatedTarget.closest('[class^=col-sm]').dataset.encryptedQueryString
							);

							$(e.currentTarget).find('input[name="interoffice-rate"]').next().val($(e.relatedTarget).prev().children('[data-text]')
                                .text().trim());
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().trigger('input');
@elseif (request()->route()->named('sales-special-rate-deal.index'))
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().val('');
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().trigger('input');
@endif
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
							
						})

                        $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').on('hidden.bs.modal', function(e) {
@if (request()->route()->named('sales-special-rate-deal.index'))
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().val('');
							$(e.currentTarget).find('input[name="interoffice-rate"]').next().trigger('input');
@endif
							$(e.currentTarget).find('input[name="customer-rate"]').next().val('');
                            $(e.currentTarget).find('input[name="customer-rate"]').next().trigger('input');

							$(e.currentTarget).find('input[name="amount"]').next().val('');
							$(e.currentTarget).find('input[name="amount"]').next().trigger('input');
                        })

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('[type="submit"]').on('focus', function(e) {
                            $(e.currentTarget).closest('form').find('[required]').each( function(key, element) {
								if (
									(
										($(element).is(':hidden') && $(element.nextElementSibling).is('[im-insert]')) || (
											$(element).is(':visible') && element.name
										)
									) && (
										!element.value
									)
								) {
									if ($(element).is(':hidden') || $(element).is('[data-select2-id]')) {
										element = element.nextElementSibling
									}

									$(element).tooltip({
										trigger: 'manual',
										placement: 'bottom',
										title: 'Alert! Please fill out this field.',
										template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner bg-danger-500 mw-100"></div></div>'
									})
									.tooltip('show')

									e.preventDefault()
								}
							})

							if (
								($(e.currentTarget).closest('form').find('[name="buy-sell"]').val().toLowerCase() === 'bank buy') &&
								parseFloat(
									$(e.currentTarget).closest('form').find('[name="customer-rate"]').val()
								) > parseFloat(
									$(e.currentTarget).closest('form').find('[name="interoffice-rate"]').val()
								)
							)
							{
								$(e.currentTarget).closest('form').find('[name="customer-rate"]').next().tooltip('dispose')
								$(e.currentTarget).closest('form').find('[name="customer-rate"]').next()
								.tooltip({
									trigger: 'manual',
									placement: 'bottom',
									title: 'Alert! The customer rate over interoffice rate.',
									template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner bg-danger-500 mw-100"></div></div>'
								})
								.tooltip('show')
								
							} else if (
								($(e.currentTarget).closest('form').find('[name="buy-sell"]').val().toLowerCase() === 'bank sell') &&
								parseFloat(
									$(e.currentTarget).closest('form').find('[name="customer-rate"]').val()
								) < parseFloat(
									$(e.currentTarget).closest('form').find('[name="interoffice-rate"]').val()
								)
							)
							{
								$(e.currentTarget).closest('form').find('[name="customer-rate"]').next().tooltip('dispose')
								$(e.currentTarget).closest('form').find('[name="customer-rate"]').next()
								.tooltip({
									trigger: 'manual',
									placement: 'bottom',
									title: 'Alert! The customer rate under interoffice rate.',
									template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner bg-danger-500 mw-100"></div></div>'
								})
								.tooltip('show')
							}
						})

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('form')
						.on('focus', '[required]', function(e) {
							$(e.currentTarget).tooltip('hide');
						})

						$(document).find('.modal:not(.js-modal-settings):not(.modal-alert)').find('form').on('submit', function(e) {
							if (
								$(e.currentTarget).find('[aria-describedby^="tooltip"]') && (
									document.getElementById($(e.currentTarget).find('[aria-describedby^="tooltip"]').attr('aria-describedby'))
									.children[1].classList.contains('bg-danger-500')
								)
							) {
								e.preventDefault()
							}
						})
						
					</script>
@endsection
