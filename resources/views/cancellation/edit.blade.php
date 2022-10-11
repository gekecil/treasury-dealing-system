@extends('layouts.master')

@if (request()->get('is_rejection'))
@section('title', $cancellation->salesDeal->account->name.' - Rejections')
@else
@section('title', $cancellation->salesDeal->account->name.' - Cancellations')
@endif

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
@if (request()->get('is_rejection'))
							<li class="breadcrumb-item"><a href="{{ route('sales-cancellations.index') }}">Rejections</a></li>
@else
							<li class="breadcrumb-item"><a href="{{ route('sales-cancellations.index') }}">Cancellations</a></li>
@endif
                            <li class="breadcrumb-item active">{{ $cancellation->salesDeal->account->name }}</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-edit'></i>
                                {{ $cancellation->salesDeal->account->name }}
                                /
@if (request()->get('is_rejection'))
                                Rejections
@else
                                Cancellations
@endif
                            </h1>
                        </div>
						<div class="alert alert-success collapse"></div>
						<div class="panel-container collapse">
							<div class="panel-content">
								<div class="alert alert-danger alert-dismissible fade show" role="alert">
									<button type="button" class="close" aria-label="Close">
										<span aria-hidden="true"><i class="fal fa-times"></i></span>
									</button>
									<strong>Alert!</strong>
								</div>
							</div>
						</div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-cancellation-show" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form action="{{ route('sales-cancellations.update', ['cancellation' => $cancellation->id]) }}" method="post">
												@method(strtoupper('put'))
												
												@csrf
												
                                                <input type="hidden" name="is_rejection" value="{!! request()->get('is_rejection') !!}">
												<div class="form-group">
													<label class="form-label" for="customer-name">Customer Name</label>
													<input type="text" class="form-control" value="{{ $cancellation->salesDeal->account->name }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="region">
														Region
													</label>
													<input type="text" class="form-control" value="{{ $cancellation->salesDeal->branch->region }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="branch">
														Branch
													</label>
													<input type="text" class="form-control" value="{{ $cancellation->salesDeal->branch->name }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="currency">
														Currency Pairs
													</label>
                                                    <input type="text" class="form-control" value="{{
                                                        (
                                                            $cancellation->salesDeal->currencyPair->baseCurrency->secondary_code ?: (
                                                                $cancellation->salesDeal->currencyPair->baseCurrency->primary_code
                                                            )
                                                        ).(
                                                            '/'
                                                        ).(
                                                            $cancellation->salesDeal->currencyPair->counterCurrency()
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
													<input type="text" class="form-control" value="Bank {{ ucfirst($cancellation->salesDeal->buyOrSell->name) }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="interoffice-rate">
														Interoffice Rate
													</label>
													<input type="text" class="form-control" value="{{ number_format($cancellation->salesDeal->interoffice_rate, 4) }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="customer-rate">
														Customer Rate
													</label>
													<input type="text" class="form-control" value="{{ number_format($cancellation->salesDeal->customer_rate, 4) }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="base-amount">
														Base Amount
													</label>
													<input type="text" class="form-control" value="{{ number_format($cancellation->salesDeal->amount, 2) }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="counter-amount">
														Counter Amount
													</label>
													<input type="text" class="form-control" value="{{ number_format(($cancellation->salesDeal->customer_rate * $cancellation->salesDeal->amount), 4) }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="tod-tom-spot-forward">
														TOD/TOM/Spot/Forward
													</label>
													<input type="text" class="form-control" value="{{ $cancellation->salesDeal->todOrTomOrSpotOrForward->name }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="TT-BN">
														TT/BN
													</label>
													<input type="text" class="form-control" value="{{ $cancellation->salesDeal->ttOrBn->name }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="lhbu-remarks">
														LHBU Remarks
													</label>
													<input type="text" class="form-control mb-2" title="Kode Tujuan" value="{{
                                                        $cancellation->salesDeal->lhbuRemarksCode->name_id
                                                        .' '
                                                        .ucfirst($cancellation->salesDeal->lhbuRemarksCode->name)
                                                    }}" readonly>
													<input type="text" class="form-control" title="Jenis Dokumen" value="{{
                                                        $cancellation->salesDeal->lhbuRemarksKind->name_id
                                                        .' '
                                                        .ucfirst($cancellation->salesDeal->lhbuRemarksKind->name)
                                                    }}" readonly>
												</div>
												<div class="form-group">
													<label class="form-label" for="created-at">
														Created At
													</label>
													<input type="text" class="form-control" value="{{
														$cancellation->salesDeal->created_at->toDayDateTimeString()
													}}" readonly>
												</div>
												<div class="form-group">
                                                    <label class="form-label" for="note">Note</label>
                                                    <textarea name="note" class="form-control" rows="5" required></textarea>
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
