@extends('layouts.master')

@section('title', $interbankDeal->counterparty->name.' - Interbank')

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
						<div class="row">
							<div class="col-sm-12 col-md-6 d-flex justify-content-start">
								<div class="subheader">
									<h1 class="subheader-title">
										<i class='subheader-icon fal fa-edit'></i> {{ $interbankDeal->counterparty->name }}
									</h1>
								</div>
							</div>
@can ('update', $interbankDeal)
							<div class="col-sm-12 col-md-6 d-flex justify-content-end">
								<a href="{{ route('interbank-dealing.edit', ['interbankDeal' => $interbankDeal->id]) }}">
									<button class="btn btn-primary" type="button" title="Edit Interbank Deal">
										<span class="fal fa-edit mr-1"></span>
										Edit
									</button>
								</a>
							</div>
@endcan
						</div>
						<div class="row">
							<div class="col-12">
                                <div id="panel-interbank-deal-show" class="panel">
									<div class="panel-container show">
                                        <div class="panel-content">
											<div class="form-group">
												<label class="form-label" for="counterparty">
													Counterparty
												</label>
												<input type="text" class="form-control" value="{{ $interbankDeal->counterparty->name }}" readonly>
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
												<input type="text" class="form-control" value="{{ number_format($interbankDeal->interoffice_rate, 7) }}" readonly>
											</div>
@if ($interbankDeal->interbankDealRate)
											<div class="form-group">
												<label class="form-label" for="base-currency-rate">
                                                    Base Currency Rate
                                                </label>
												<input type="text" class="form-control" value="{{ number_format($interbankDeal->interbankDealRate->base_currency_rate, 2) }}" readonly>
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
												<input type="text" class="form-control" value="{{ number_format($interbankDeal->amount, 2) }}" readonly>
											</div>
											<div class="form-group">
												<label class="form-label" for="counter-amount">
													Counter Amount
												</label>
												<input type="text" class="form-control" value="{{ number_format(($interbankDeal->interoffice_rate * $interbankDeal->amount), 7) }}" readonly>
											</div>
											<div class="form-group">
												<label class="form-label" for="tod-tom-spot-forward">
													TOD/TOM/Spot/Forward
												</label>
												<input type="text" class="form-control" value="{{
                                                    ucfirst($interbankDeal->todOrTomOrSpotOrForward->name)
                                                }}" readonly>
											</div>
											<div class="form-group">
												<label class="form-label" for="remarks">
													Remarks
												</label>
												<textarea class="form-control mb-2" rows="5" readonly>{{
                                                    $interbankDeal->basic_remarks
                                                }}</textarea>
                                                <textarea class="form-control" rows="5" readonly>{{
                                                    $interbankDeal->additional_remarks
                                                }}</textarea>
											</div>
                                            <div class="form-group">
                                                <label class="form-label" for="value-date">
                                                    Value Date
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $interbankDeal->settlementDate()->firstOrNew([], ['value' => null])->value
                                                }}" readonly>
                                            </div>
											<div class="form-group">
												<label class="form-label" for="dealer">Dealer</label>
												<input type="text" class="form-control" value="{{ $interbankDeal->user->full_name }}" readonly>
											</div>
@if ($interbankDeal->modification)
											<div class="form-group">
												<label class="form-label" for="edited-by">Edited by</label>
												<input type="text" class="form-control" value="{{ $interbankDeal->modification->user->first_name.' '.$interbankDeal->modification->user->last_name }}" readonly>
											</div>
@endif
											<div class="form-group">
												<label class="form-label" for="created-at">Created At</label>
												<input type="text" class="form-control" value="{{
                                                    $interbankDeal->created_at->toDayDateTimeString()
                                                }}" readonly>
											</div>
											<div class="form-group">
												<label class="form-label" for="updated-at">Updated At</label>
												<input type="text" class="form-control" value="{{
                                                    $interbankDeal->updated_at->toDayDateTimeString()
                                                }}" readonly>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
					</main>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
@endsection
