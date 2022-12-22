@extends('layouts.master')

@section('title', $sismontavarDeal->transaction_id.' - SISMONTAVAR')

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
                            <li class="breadcrumb-item"><a href="{{ route('sismontavar-deals.index') }}">SISMONTAVAR</a></li>
                            <li class="breadcrumb-item active">{{ $sismontavarDeal->transaction_id }}</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
						<div class="row">
							<div class="col-sm-12 col-md-6 d-flex justify-content-start">
								<div class="subheader">
									<h1 class="subheader-title">
										<i class='subheader-icon fal fa-th-list'></i> {{ $sismontavarDeal->transaction_id }}
									</h1>
								</div>
							</div>
							<div class="col-sm-12 col-md-6 d-flex justify-content-end">
@if (auth()->user()->can('create', get_class($sismontavarDeal::class)))
								<a href="javascript:document.querySelector('.modal:not(.js-modal-settings):not(.modal-alert)').modal();">
									<button class="btn btn-primary mr-1" type="button" title="Resend">
										<span class="fal fa-edit mr-1"></span>
										Resend
									</button>
								</a>
@endif
							</div>
						</div>
						<div class="row">
							<div class="col">
                                <div id="panel-sismontavar-show" class="panel">
									<div class="panel-container show">
                                        <div class="panel-content fs-xl">
                                            <div class="row">
                                                <div class="col">
                                                    <table class="table">
                                                        <tbody>
@foreach($sismontavarDeal->toArray() as $key => $value)
                                                            <tr>
                                                                <td class="fw-500 text-capitalize">{{
                                                                    \Illuminate\Support\Str::of($key)->replaceMatches('/_id$/', function($match) {
                                                                        return strtoupper($match[0]);
                                                                    })
                                                                    ->replace('_', ' ')
                                                                }}:</td>
                                                                <td>{{ $value }}</td>
                                                            </tr>
@endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col">
                                                    <table class="table">
                                                        <tbody>
@if($sismontavarDeal->status_code === 200)
                                                            <tr>
                                                                <td class="fw-500">Transaction Status:</td>
                                                                <td>Reported to BI</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-500">Response Status:</td>
                                                                <td>{{ json_decode($sismontavarDeal->status_text)->Message }}</td>
                                                            </tr>
@endif
                                                            <tr>
                                                                <td class="fw-500">Time:</td>
                                                                <td>{{ $sismontavarDeal->updated_at->toTimeString() }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
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
									
									<input type="hidden" name="account-number" value="{{ $sismontavarDeal->corporate_id }}">
									<input type="hidden" name="account-cif" value="{{ $sismontavarDeal->corporate_id }}">
									<input type="hidden" name="account-name" value="{{ $sismontavarDeal->corporate_name }}">
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
											<label class="form-label" for="transaction-id">Transaction ID</label>
											<input type="text" name="transaction-id" class="form-control" autocomplete="off" value="{{
                                                $sismontavarDeal->transaction_id
                                            }}" readonly>
										</div>
										<div class="form-group">
											<label class="form-label" for="account">Account</label>
											<select name="account" required>
                                                <option value="{{ $sismontavarDeal->corporate_id.' '.$sismontavarDeal->corporate_id.' '.$sismontavarDeal->corporate_name }}" selected>
                                                    {{ $sismontavarDeal->corporate_name }}
                                                </option>
                                            </select>
										</div>
										<div class="form-group">
											<label class="form-label" for="deal-type">Deal Type</label>
											<select name="deal-type" class="form-control" required>
												<option value>Choose</option>
@foreach (['TOD', 'TOM', 'Spot', 'Forward', 'SWAP'] as $value)
@if ($value === $sismontavarDeal->deal_type)
												<option value="{{ $value }}" selected>{{ $value }}</option>
@else
												<option value="{{ $value }}">{{ $value }}</option>
@endif
@endforeach
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="direction">Direction</label>
											<select name="direction" class="form-control" required>
												<option value>Choose</option>
@foreach (['Buy', 'Sell', 'Buy and Sell', 'Sell and Buy'] as $value)
@if ($value === $sismontavarDeal->direction)
												<option value="{{ $value }}" selected>{{ $value }}</option>
@else
												<option value="{{ $value }}">{{ $value }}</option>
@endif
@endforeach
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="base-currency">Base Currency</label>
											<select name="currency-pair" class="form-control" required>
												<option value>Choose</option>
@foreach ($currencyPair as $value)
@if ($value->baseCurrency->primary_code === $sismontavarDeal->base_currency)
												<option value="{{ $value->id }}" selected>
                                                    {{ ($value->baseCurrency->secondary_code ?: $value->baseCurrency->primary_code) }}/IDR
                                                </option>
@else
												<option value="{{ $value->id }}">
                                                    {{ ($value->baseCurrency->secondary_code ?: $value->baseCurrency->primary_code) }}/IDR
                                                </option>
@endif
@endforeach
											</select>
										</div>
										<div class="form-group">
											<label class="form-label" for="base-volume">Base Volume</label>
											<input type="hidden" name="base-volume" value="{{ $sismontavarDeal->base_volume }}" required>
											<input type="text" class="form-control" autocomplete="off" value="{{
                                                number_format($sismontavarDeal->base_volume, 2)
                                            }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="quote-volume">Quote Volume</label>
											<input type="hidden" name="quote-volume" value="{{ $sismontavarDeal->quote_volume }}" required>
											<input type="text" class="form-control" autocomplete="off" value="{{
                                                number_format($sismontavarDeal->quote_volume, 2)
                                            }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="periods">Periods</label>
											<input type="number" name="periods" class="form-control" autocomplete="off" value="{{ $sismontavarDeal->periods }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="near-rate">Near Rate</label>
											<input type="hidden" name="near-rate" value="{{ $sismontavarDeal->near_rate }}" required>
											<input type="text" class="form-control" autocomplete="off" value="{{
                                                number_format($sismontavarDeal->near_rate, 2)
                                            }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="far-rate">Far Rate</label>
											<input type="hidden" name="far-rate" value="{{ $sismontavarDeal->far_rate }}" required>
											<input type="text" class="form-control" autocomplete="off" value="{{
                                                number_format($sismontavarDeal->far_rate, 2)
                                            }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="transaction-purpose">Transaction Purpose</label>
											<input type="text" name="transaction-purpose" class="form-control" autocomplete="off" value="{{ $sismontavarDeal->transaction_purpose }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="transaction-date">Transaction Date</label>
											<input type="text" name="transaction-date" class="form-control" autocomplete="off" value="{{ $sismontavarDeal->transaction_date }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="near-value-date">Near Value Date</label>
											<input type="text" name="near-value-date" class="form-control" autocomplete="off" value="{{ $sismontavarDeal->near_value_date }}" required>
										</div>
										<div class="form-group">
											<label class="form-label" for="far-value-date">Far Value Date</label>
											<input type="text" name="far-value-date" class="form-control" autocomplete="off" value="{{ $sismontavarDeal->far_value_date }}" required>
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
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
@endsection
