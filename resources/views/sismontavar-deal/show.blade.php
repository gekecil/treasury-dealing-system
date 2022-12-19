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
                            <li class="breadcrumb-item"><a href="#sismontavar">SISMONTAVAR</a></li>
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
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
@endsection
