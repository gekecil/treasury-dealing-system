@extends('layouts.master')

@section('title', 'Edit - '.$salesDeal->account->name.' - Sales')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/formplugins/select2/select2.bundle.css">
@if ($salesDeal->can_upload_underlying)
		<link rel="stylesheet" media="screen, print" href="/css/formplugins/dropzone/dropzone.css">
		<link rel="stylesheet" media="screen, print" href="/css/notifications/sweetalert2/sweetalert2.bundle.css">
@endif
@endsection

@section('content')
					<main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb d-flex align-items-center">
                            <li class="breadcrumb-item">
								<ul class="pagination">
									<li class="page-item">
										<a class="page-link" href="#" aria-label="Previous">
											<span aria-hidden="true"><i class="fal fa-chevron-left"></i></span>
										</a>
									</li>
								</ul>
							</li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item"><a href="#sales">Sales</a></li>
@if (request()->route()->named('sales-fx.edit', ['salesDeal' => $salesDeal->id]))
							<li class="breadcrumb-item"><a href="{{ route('sales-fx.index') }}">FX</a></li>
							<li class="breadcrumb-item"><a href="{{ route('sales-fx.show', ['salesDeal' => $salesDeal->id]) }}">
								{{ $salesDeal->account->name }}
							</a></li>
@elseif (request()->route()->named('sales-special-rate-deal.edit', ['salesDeal' => $salesDeal->id]))
							<li class="breadcrumb-item"><a href="{{ route('sales-special-rate-deal.index') }}">Request for FX Deal</a></li>
							<li class="breadcrumb-item"><a href="{{ route('sales-special-rate-deal.show', ['salesDeal' => $salesDeal->id]) }}">
								{{ $salesDeal->account->name }}
							</a></li>
@elseif (request()->route()->named('sales-blotter.edit', ['salesDeal' => $salesDeal->id]))
							<li class="breadcrumb-item"><a href="{{ route('sales-blotter.index') }}">Blotter</a></li>
							<li class="breadcrumb-item"><a href="{{ route('sales-blotter.show', ['salesDeal' => $salesDeal->id]) }}">
								{{ $salesDeal->account->name }}
							</a></li>
@endif
                            <li class="breadcrumb-item active">Edit</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-edit'></i> {{ $salesDeal->account->name }}
                            </h1>
                        </div>
						<div class="row">
							<div class="col-12">
                                <div id="panel-sales-deal-edit" class="panel">
									<div class="panel-container show">
                                        <div class="panel-content">
@if (request()->route()->named('sales-fx.edit', ['salesDeal' => $salesDeal->id]))
											<form action="{{ route('sales-fx.update', ['salesDeal' => $salesDeal->id]) }}" method="post">
@elseif (request()->route()->named('sales-special-rate-deal.edit', ['salesDeal' => $salesDeal->id]))
											<form action="{{ route('sales-special-rate-deal.update', ['salesDeal' => $salesDeal->id]) }}" method="post">
@elseif (request()->route()->named('sales-blotter.edit', ['salesDeal' => $salesDeal->id]))
											<form action="{{ route('sales-blotter.update', ['salesDeal' => $salesDeal->id]) }}" method="post">
@endif
												@method(strtoupper('patch'))
												
												@csrf
												
												<input type="hidden" name="threshold" value="{{ $threshold }}">
												<input type="hidden" name="base-primary-code" value="{{ $salesDeal->currencyPair->baseCurrency->primary_code }}">
												<input type="hidden" name="counter-primary-code" value="{{
                                                    $salesDeal->currencyPair->counterCurrency()->firstOrNew([], ['primary_code', null])->primary_code
                                                }}">
												<input type="hidden" name="branch-name" value="{{ $salesDeal->branch->name }}">
@if (auth()->user()->is_branch_office_dealer)
												<input type="hidden" name="region" value="{{ $salesDeal->branch->region }}">
@endif
												<input type="hidden" name="sales-limit" value="{{
													$salesDeal->user->sales_limit
												}}">
												<input type="hidden" name="base-currency-closing-rate" value="{{
													$salesDeal->baseCurrencyClosingRate->mid_rate
												}}">
												<input type="hidden" name="world-currency-closing-rate" value="{{
													$salesDeal->baseCurrencyClosingRate->world_currency_closing_mid_rate
												}}">
												<input type="hidden" name="world-currency-code" value="{{ $salesDeal->currencyPair->baseCurrency->world_currency_code }}">
												<input type="hidden" name="account-cif" data-account-cif="{{ $salesDeal->account->cif }}">
												<input type="hidden" name="account-number">
												<input type="hidden" name="account-name">
												<div class="form-group">
													<label class="form-label" for="account">
														Account
													</label>
													<select name="account" required>
														<option value="{{ $salesDeal->account->number.' '.$salesDeal->account->cif.' '.$salesDeal->account->name }}" selected>
															{{ $salesDeal->account->number.' '.$salesDeal->account->name }}
														</option>
													</select>
												</div>
@if ($salesDeal->can_upload_underlying)
                                                <div class="form-group">
                                                    <label class="form-label" for="monthly-usd-equivalent">Monthly USD Equivalent</label>
                                                    <input type="text" class="form-control" value="{{ number_format($salesDeal->monthly_usd_equivalent, 4) }}" data-monthly-usd-equivalent="{{
                                                        $salesDeal->monthly_usd_equivalent
                                                    }}" readonly>
                                                </div>
@endif
@if (auth()->user()->is_head_office_dealer || auth()->user()->is_super_administrator)
												
                                                <div class="form-group collapse show">
                                                    <label class="form-label" for="region">Region</label>
                                                    <select name="region" class="form-control">
                                                        <option value>Choose</option>
                                                        <option value="{{ $salesDeal->branch->region }}" selected>
                                                            {{ $salesDeal->branch->region }}
                                                        </option>
@foreach ($regions->where('region', '!=', $salesDeal->branch->region)->pluck('region')->unique()->sort() as $value)
														<option value="{{ $value }}">{{ $value }}</option>
@endforeach
                                                    </select>
                                                </div>
												<div class="form-group collapse">
                                                    <label class="form-label" for="branch">Branch</label>
                                                    <select name="branch-code" class="form-control" data-branch-code="{{
                                                        $salesDeal->branch->code
                                                    }}">
                                                        <option value>Choose</option>
													</select>
                                                </div>
@endif
												<div class="form-group">
													<label class="form-label" for="currency">
														Currency Pairs
													</label>
													<input type="text" class="form-control" value="{{
                                                        (
                                                            $salesDeal->currencyPair->baseCurrency->secondary_code ?: (
                                                                $salesDeal->currencyPair->baseCurrency->primary_code
                                                            )
                                                        ).(
                                                            '/'
                                                        ).(
                                                            $salesDeal->currencyPair->counterCurrency()
                                                            ->firstOrNew([], [
                                                                'primary_code' => 'IDR'
                                                            ])
                                                            ->primary_code
                                                        )
                                                    }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="buy-sell">
														Buy/Sell
													</label>
													<input type="text" name="buy-sell" class="form-control" value="Bank {{ ucfirst($salesDeal->buyOrSell->name) }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="interoffice-rate">
														Interoffice Rate
													</label>
													<input type="text" class="form-control" value="{{
                                                        number_format($salesDeal->interoffice_rate, 4)
                                                    }}" readonly>
												</div>
@if (auth()->user()->is_head_office_dealer || auth()->user()->is_super_administrator)
												<div class="form-group">
													<label class="form-label" for="customer-rate">
														Customer Rate
													</label>
													<input type="hidden" name="customer-rate" value="{{ $salesDeal->customer_rate }}" required>
													<input type="text" class="form-control" autocomplete="off" value="{{
														number_format($salesDeal->customer_rate, 4)
													}}" required>
												</div>
												<div class="form-group">
													<label class="form-label" for="base-amount">
														Base Amount
													</label>
													<input type="hidden" name="amount" value="{{ $salesDeal->amount }}" data-amount="{{ $salesDeal->amount }}" required>
													<input type="text" class="form-control" autocomplete="off" value="{{
                                                        number_format($salesDeal->amount, 2)
                                                    }}" required>
												</div>
@else
												<div class="form-group">
													<label class="form-label" for="customer-rate">
														Customer Rate
													</label>
													<input type="hidden" name="customer-rate" value="{{ $salesDeal->customer_rate }}" required>
													<input type="text" class="form-control" value="{{
                                                        number_format($salesDeal->customer_rate, 4)
                                                    }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="base-amount">
														Base Amount
													</label>
													<input type="hidden" name="amount" value="{{ $salesDeal->amount }}" required>
													<input type="text" class="form-control" value="{{
                                                        number_format($salesDeal->amount, 2)
                                                    }}" readonly>
												</div>
@endif
												<div class="form-group">
													<label class="form-label" for="counter-amount">
														Counter Amount
													</label>
													<input type="text" class="form-control" value="{{
														number_format(($salesDeal->customer_rate * $salesDeal->amount), 4)
													}}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="tod-tom-spot-forward">
														TOD/TOM/Spot/Forward
													</label>
													<select name="tod-tom-spot-forward" class="form-control text-capitalize" required>
														<option class="text-capitalize" value="{{ $salesDeal->todOrTomOrSpotOrForward->name }}" selected>
															{{ $salesDeal->todOrTomOrSpotOrForward->name }}
														</option>
@foreach (
	collect(['TOD', 'TOM', 'spot'])->filter( function($item, $key) use($salesDeal) {
		return $item !== $salesDeal->todOrTomOrSpotOrForward->name;
	}) as $value
)
														<option class="text-capitalize" value="{{ $value }}">
															{{ $value }}
														</option>
@endforeach
													</select>
												</div>
												<div class="form-group">
													<label class="form-label" for="TT-BN">
														TT/BN
													</label>
													<select name="tt-bn" class="form-control" required>
														<option value="{{ $salesDeal->ttOrBn->name }}" selected>
															{{ $salesDeal->ttOrBn->name }}
														</option>
@foreach (
	collect(['TT', 'BN'])->filter( function($item, $key) use($salesDeal) {
		return $item !== $salesDeal->ttOrBn->name;
	}) as $value
)
														<option value="{{ $value }}">
															{{ $value }}
														</option>
@endforeach
													</select>
												</div>
												<div class="form-group">
													<label class="form-label" for="remarks">
														LHBU Remarks
													</label>
                                                    <select name="lhbu-remarks-code" required>
                                                        <option value="{{ $salesDeal->lhbu_remarks_code }}" selected>
															{{
                                                                $salesDeal->lhbu_remarks_code.' '.$salesDeal->lhbuRemarksCode->name
                                                            }}
														</option>
                                                    </select>
                                                    <select name="lhbu-remarks-kind" required>
                                                        <option value="{{ $salesDeal->lhbu_remarks_kind }}" selected>
															{{
                                                                $salesDeal->lhbu_remarks_kind.' '.$salesDeal->lhbuRemarksKind->name
                                                            }}
														</option>
                                                    </select>
                                                    <textarea name="other-lhbu-remarks-kind" class="form-control mt-2 collapse" rows="5">{{
                                                        $salesDeal->otherLhbuRemarksKind()->firstOrNew([], [
                                                            'value' => null
                                                        ])
                                                        ->value
                                                    }}</textarea>
												</div>
@if ($salesDeal->can_upload_underlying)
												<div class="form-group">
													<label class="form-label" for="document">Upload Underlying</label>
													<div id="dropzone">
														<div class="dz-message needsclick">
															<i class="fal fa-cloud-upload text-muted mb-3"></i> <br>
															<span class="text-uppercase">drop files here or click to upload.</span>
														</div>
													</div>
												</div>
@endif
												<button type="button" class="btn btn-lg btn-default" data-toggle="modal" data-target="#modal-alert">
													<span class="fal fa-check mr-1"></span>
													Submit
												</button>
											</form>
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
					</main>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
@endsection

@section('javascript')
					<script src="/js/formplugins/select2/select2.bundle.js"></script>
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
@if ($salesDeal->can_upload_underlying)
					<script src="/js/formplugins/dropzone/dropzone.js"></script>
					<script src="/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
@endif
					
					<script type="text/javascript">
						$(document).ready( function() {
                            $(document).find('select[name="lhbu-remarks-code"]').select2({
                                dropdownParent: $(document).find('select[name="lhbu-remarks-code"]').parent(),
                                containerCssClass: 'mb-2',
                                data: @json(
                                    $lhbuRemarksCode->toArray()
                                )
                            })

                            $(document).find('select[name="lhbu-remarks-kind"]').select2({
                                dropdownParent: $(document).find('select[name="lhbu-remarks-kind"]').parent(),
                                data: @json(
                                    $lhbuRemarksKind->toArray()
                                )
                            })
                            .on('select2:select', function(e) {
                                if (
                                    (
                                        e.params && (
                                            parseInt(e.params.data.id) === @json(
                                                $lhbuRemarksKind->firstWhere('name', 'dengan underlying lainnya')->id
                                            )
                                        )
                                    ) || (
                                        !e.params && (
                                            parseInt(
                                                @json($salesDeal->lhbu_remarks_kind)
                                            ) === (
                                                @json($lhbuRemarksKind->firstWhere('name', 'dengan underlying lainnya')->id)
                                            )
                                        )
                                    )
                                ) {
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

                            $(document).find('select[name="region"]').trigger('change');
                            $(document).find('select[name="lhbu-remarks-kind"]').trigger('select2:select');

@if ($salesDeal->can_upload_underlying)
							const buttonRemove = document.createElement('span');
							const iconRemove = document.createElement('i');
							buttonRemove.classList.add(
								'btn', 'btn-danger', 'btn-sm', 'btn-icon', 'rounded-circle', 'm-2', 'waves-effect', 'waves-themed'
							);
							iconRemove.classList.add('fal', 'fa-times', 'cursor-pointer');
							iconRemove.style.fontSize = '1.33333em';
							buttonRemove.appendChild(iconRemove);
							
							$('div#dropzone').dropzone({
								url: '/api/sales-deal-file',
								paramName: 'document',
								params: {
									api_token: $(document).find('meta[name="api-token"]').attr('content'),
									sales_deal_id: @json($salesDeal->id)
								},
								addRemoveLinks: true,
								dictRemoveFile: buttonRemove.outerHTML,
								dictCancelUpload: buttonRemove.outerHTML,
								maxFiles: 1,
								maxFilesize: 32,
								timeout: (5 * (60 * 1000)),
								removedfile: function(file) {
									let dropzone = this;
									
									dropzone.files = without(
										dropzone.files, dropzone.files.find(f => f.previewElement === file.previewElement)
									);
									
									dropzone.files.push(file);
									
									if (file.hasOwnProperty('xhr') && file.xhr.response) {
										Swal.fire({
											title: 'Are you sure?',
											text: "You won't be able to revert this!",
											type: 'warning',
											showCancelButton: true,
											confirmButtonText: 'Yes, delete it!'
										}).then( function(result) {
											if (result.value) {
												Swal.fire({
													title: 'Please wait...',
													showConfirmButton: false,
													timer: 60000,
													onBeforeOpen: function onBeforeOpen() {
														Swal.showLoading();
														
													},
													onOpen: function onOpen() {
														$.ajax({
															headers: { 'X-CSRF-TOKEN': $(document).find('meta[name="csrf-token"]').attr('content') },
															method: 'POST',
															url: @json(route('api.sales-deal-file.destroy', '')) + '/' + JSON.parse(file.xhr.response).data.id,
															data: {
																api_token: $(document).find('meta[name="api-token"]').attr('content'),
																_method: @json(strtoupper('delete'))												}
														}).done( function(response) {
															if (file.previewElement != null && file.previewElement.parentNode != null) {
																file.previewElement.parentNode.removeChild(file.previewElement);
															}
															
															dropzone.files = without(
																dropzone.files, dropzone.files.find(f => f.previewElement === file.previewElement)
															);
															
															dropzone._updateMaxFilesReachedClass();
															
															if (dropzone.files.length === 0) {
																dropzone.emit("reset");
															}
															
															Swal.close();
															Swal.fire({
																type: 'success',
																title: response.status,
																showConfirmButton: false,
																timer: 2000
															});
															
														}).fail( function(jqXHR, textStatus, errorThrown) {
															Swal.close();
															Swal.fire('Oops...', jqXHR.responseJSON.message, 'error')
																.then( function(result) {
																	window.location.reload();
																});
														})
													}
												})
											}
										})
									} else {
										if (file.previewElement != null && file.previewElement.parentNode != null) {
											file.previewElement.parentNode.removeChild(file.previewElement);
										}
										
										dropzone.files = without(
											dropzone.files, dropzone.files.find(f => f.previewElement === file.previewElement)
										);
									}
									
									return dropzone._updateMaxFilesReachedClass();
								},
								init: function() {
									Dropzone.dropzone = this;
									Dropzone.dropzone.element.classList.add('dropzone', 'needsclick');
									
									Dropzone.dropzone.on('maxfilesexceeded', function(file){
										Dropzone.dropzone.removeFile(file);
										
										Swal.fire('Oops...', 'No more files please!', 'error');
									})
									
									Dropzone.dropzone.on('addedfile', function(file) {
										Dropzone.confirm = function (question, accepted, rejected) {
											Swal.fire({
												title: 'Are you sure?',
												text: "You won't be able to revert this!",
												type: 'warning',
												showCancelButton: true,
												confirmButtonText: 'Yes, cancel it!'
											}).then( function(result) {
												if (result.value && (file.status === Dropzone.UPLOADING)) {
													return accepted();
												}
											})
										};
									})
									
									Dropzone.dropzone.on('success', function(file) {
										let anchor = document.createElement('a');
										anchor.className = 'cursor-pointer';
										anchor.href = @json(route('sales-deal-file.show', '')) + '/' + JSON.parse(file.xhr.response).data.id;
										anchor.innerHTML = JSON.parse(file.xhr.response).data.filename;
										
										file.previewElement.querySelector('[data-dz-name]').replaceChildren(anchor);
										
										if (!(file instanceof File)) {
											file.previewElement.querySelector('[data-dz-size]').remove();
										}
										
										file.previewElement.querySelector('.dz-image')
											.style.width = (file.previewElement.querySelector('[data-dz-name]').offsetWidth + 32) + 'px';
									})
									
									Dropzone.dropzone.on('error', function(file) {
										if (file.previewElement != null && file.previewElement.parentNode != null) {
											file.previewElement.parentNode.removeChild(file.previewElement);
										}
										
										Dropzone.dropzone.files = without(
											Dropzone.dropzone.files, Dropzone.dropzone.files.find(f => f.previewElement === file.previewElement)
										);
										
										Dropzone.dropzone._updateMaxFilesReachedClass();
										
										if (Dropzone.dropzone.files.length === 0) {
											Dropzone.dropzone.emit('reset');
										}
										
										if (file.hasOwnProperty('xhr')) {
											if (file.xhr.response && JSON.parse(file.xhr.response).hasOwnProperty('errors')) {
												if (JSON.parse(file.xhr.response).errors.hasOwnProperty('document')) {
													Swal.fire('Oops...', JSON.parse(file.xhr.response).errors.document.join(), 'error');
												}
											}
										}
									})
									
@if ($salesDeal->salesDealFile && $salesDeal->salesDealFile->filename)
									Dropzone.dropzone.options.addedfile.call(Dropzone.dropzone, {
										xhr: {
											response: JSON.stringify({
												data: @json($salesDeal->salesDealFile)
											})
										},
									});
									
									Dropzone.dropzone.options.complete.call(Dropzone.dropzone, {
										previewElement: Dropzone.dropzone.element.querySelector('.dz-preview')
									})
									
									Dropzone.dropzone.emit('success', {
										xhr: {
											response: JSON.stringify({
												data: @json($salesDeal->salesDealFile)
											})
										},
										previewElement: Dropzone.dropzone.element.querySelector('.dz-preview')
									});
									
									Dropzone.dropzone.files.push({
										previewElement: Dropzone.dropzone.element.querySelector('.dz-preview'),
										accepted: true
									});
									
									Dropzone.dropzone._updateMaxFilesReachedClass();
@endif
								}
								
							})
@endif
						})
					</script>
@endsection
