@extends('layouts.master')

@section('title', $interbankDeal->counterparty->name.' - Interbank')

@section('stylesheet')
        <link rel="stylesheet" media="screen, print" href="/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
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
                            <li class="breadcrumb-item"><a href="#interbank">Interbank</a></li>
							<li class="breadcrumb-item"><a href="{{ route('interbank-dealing.index') }}">Dealing</a></li>
                            <li class="breadcrumb-item active">{{ $interbankDeal->counterparty->name }}</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-edit'></i> {{ $interbankDeal->counterparty->name }}
                            </h1>
                        </div>
						<div class="row">
							<div class="col-12">
                                <div id="panel-interbank-deal-edit" class="panel">
									<div class="panel-container show">
                                        <div class="panel-content">
											<form action="{{ route('interbank-dealing.update', ['interbankDeal' => $interbankDeal->id]) }}" method="post">
												@method(strtoupper('patch'))
												
												@csrf
												
												<input type="hidden" name="base-currency-code" value="{{
                                                    $interbankDeal->currencyPair->baseCurrency->currency_code
                                                }}">
												<input type="hidden" name="counter-currency-code" value="{{
                                                    $interbankDeal->currencyPair->counterCurrency()
                                                    ->firstOrNew([], ['currency_code', null])->currency_code
                                                }}">
												<input type="hidden" name="commercial-bank-limit" value="{{
													$interbankDeal->user->commercial_bank_limit
												}}">
												<input type="hidden" name="base-currency-closing-rate" value="{{
													$interbankDeal->baseCurrencyClosingRate->mid_rate
												}}">
												<input type="hidden" name="world-currency-closing-rate" value="{{
													$interbankDeal->baseCurrencyClosingRate->world_currency_closing_mid_rate
												}}">
												<input type="hidden" name="world-currency-code" value="{{
                                                    $interbankDeal->currencyPair->baseCurrency->world_currency_code
                                                }}">
												<input type="hidden" name="created-at" value="{{ $interbankDeal->created_at->toDateString() }}">
												<div class="form-group">
													<label class="form-label" for="counterparty">
														Counterparty
													</label>
													<input type="text" name="counterparty" class="form-control" value="{{ $interbankDeal->counterparty->name }}" required>
												</div>
												<div class="form-group">
                                                    <label class="form-label" for="currency-pairs">
                                                        Currency Pairs
                                                    </label>
                                                    <input type="text" class="form-control" value="{{
                                                        (
                                                            $interbankDeal->currencyPair->baseCurrency->secondary_code ?: (
                                                                $interbankDeal->currencyPair->baseCurrency->primary_code
                                                            )
                                                        ).(
                                                            '/'
                                                        ).(
                                                            $interbankDeal->currencyPair->counterCurrency()
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
                                                    <input type="text" class="form-control" value="Bank {{ ucfirst($interbankDeal->buyOrSell->name) }}" readonly>
                                                </div>
												<div class="form-group">
													<label class="form-label" for="interoffice-rate">
														Interoffice Rate
													</label>
													<input type="hidden" name="interoffice-rate" value="{{ $interbankDeal->interoffice_rate }}" required>
													<input type="text" class="form-control" autocomplete="off" value="{{ $interbankDeal->interoffice_rate }}" required>
												</div>
@if ($interbankDeal->interbankDealRate)
												<div class="form-group">
													<label class="form-label" for="base-currency-rate">
                                                        Base Currency Rate
                                                    </label>
													<input type="hidden" name="base-currency-rate" value="{{ $interbankDeal->interbankDealRate->base_currency_rate }}" required>
													<input type="text" class="form-control" autocomplete="off" value="{{ $interbankDeal->interbankDealRate->base_currency_rate }}" required>
												</div>
                                                <div class="form-group">
													<label class="form-label" for="counter-currency-rate">
                                                        Counter Currency Rate
													</label>
													<input type="text" class="form-control" value="{{
                                                        number_format((
                                                            $interbankDeal->interbankDealRate->base_currency_rate / $interbankDeal->interoffice_rate
                                                        ), 2)
                                                    }}" readonly>
												</div>
@endif
												<div class="form-group">
													<label class="form-label" for="base-amount">
														Base Amount
													</label>
													<input type="hidden" name="amount" value="{{ $interbankDeal->amount }}" required>
													<input type="text" class="form-control" autocomplete="off" value="{{ $interbankDeal->amount }}" required>
												</div>
												<div class="form-group">
													<label class="form-label" for="counter-amount">
														Counter Amount
													</label>
													<input type="text" class="form-control" value="{{
                                                        number_format((
                                                            $interbankDeal->interoffice_rate * $interbankDeal->amount
                                                        ), (
                                                            (
                                                                (
                                                                    strpos((string) ($interbankDeal->interoffice_rate * $interbankDeal->amount), '.')
                                                                ) !== (
                                                                    false
                                                                )
                                                            ) ? (
                                                                strlen(
                                                                    substr(
                                                                        (string) ($interbankDeal->interoffice_rate * $interbankDeal->amount),
                                                                        strpos(
                                                                            (string) ($interbankDeal->interoffice_rate * $interbankDeal->amount),
                                                                            '.'
                                                                        ) + (
                                                                            2
                                                                        )
                                                                    )
                                                                )
                                                            ) : (
                                                                0
                                                            )
                                                        ))
                                                    }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="tod-tom-spot-forward">
														TOD/TOM/Spot/Forward
													</label>
													<select name="tod-tom-spot-forward" class="form-control text-capitalize" required>
														<option class="text-capitalize" value="{{ $interbankDeal->todOrTomOrSpotOrForward->name }}" selected>
															{{ $interbankDeal->todOrTomOrSpotOrForward->name }}
														</option>
@foreach (
	collect(['TOD', 'TOM', 'spot', 'forward'])->filter( function($item, $key) use($interbankDeal) {
		return $item !== $interbankDeal->todOrTomOrSpotOrForward->name;
	}) as $value
)
														<option class="text-capitalize" value="{{ $value }}">
															{{ $value }}
														</option>
@endforeach
													</select>
												</div>
												<div class="form-group">
													<label class="form-label" for="remarks">
														Remarks
													</label>
                                                    <textarea name="basic-remarks" class="form-control mb-2" rows="5">{{
                                                        $interbankDeal->basic_remarks
                                                    }}</textarea>
                                                    <textarea name="additional-remarks" class="form-control" rows="5">{{
                                                        $interbankDeal->additional_remarks
                                                    }}</textarea>
												</div>
                                                <div class="form-group collapse">
                                                    <label class="form-label" for="value-date">
                                                        Value Date
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" name="settlement-date" class="form-control" placeholder="Select date" id="datepicker" value="{{ $interbankDeal->settlementDate()->firstOrNew([], ['value' => null])->value }}" readonly>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text fs-xl">
                                                                <i class="fal fa-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
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
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
					<script src="/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
					<script src="/moment/min/moment.min.js"></script>

					<script type="text/javascript">
						$(document).ready( function() {
                            $(document).find('[name="tod-tom-spot-forward"]').trigger('change');
                        })
                    </script>
@endsection
