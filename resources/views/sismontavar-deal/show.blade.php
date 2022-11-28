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
                                            <table class="table table-responsive">
@foreach($sismontavarDeal->toArray() as $key => $value)
@if($value)
                                                <tr>
                                                    <td>{{ \Illuminate\Support\Str::of($key)->replace('_', ' ')->title()->replace('Id', 'ID') }}:</td>
                                                    <td>{{ $value }}</td>
                                                </tr>
@endif
@endforeach
@if($sismontavarDeal->status_code === 200)
                                                <tr>
                                                    <td>Transaction Status:</td>
                                                    <td>Reported to BI</td>
                                                </tr>
                                                <tr>
                                                    <td>Response Status:</td>
                                                    <td>Success Capture</td>
                                                </tr>
@endif
                                                <tr>
                                                    <td>Time:</td>
                                                    <td>{{ $sismontavarDeal->updated_at->toTimeString() }}</td>
                                                </tr>
                                            </table>
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
					</main>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
@endsection
