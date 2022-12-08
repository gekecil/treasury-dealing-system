@extends('layouts.master')

@section('title', 'Thresholds')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
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
                            <li class="breadcrumb-item"><a href="#settings">Settings</a></li>
                            <li class="breadcrumb-item active">Thresholds</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-edit'></i> Thresholds
                            </h1>
                        </div>
@if (session('status'))
						<div class="alert alert-success">
							{{ session('status') }}
						</div>
@endif
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
                                <div id="panel-threshold-show" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form action="{{ route('settings-threshold.store') }}" method="post">
												@csrf
												
												<div class="form-group">
													<div class="input-group input-group-lg">
														<div class="input-group-prepend">
															<span class="input-group-text bg-transparent py-1 px-3">
																<span class="icon-stack" style="font-size: 2rem">
																	<i class="fal fa-dollar-sign"></i>
																</span>
															</span>
														</div>
														<input type="hidden" name="threshold" value="{{ $threshold->exists() ? $threshold->first()->threshold : 0 }}" required>
														<input id="input-group-lg-size" type="text" class="form-control" aria-describedby="input-group-lg-size" value="{{ $threshold->exists() ? number_format($threshold->first()->threshold, 2, '.', ',') : 0 }}" autocomplete="off" required readonly>
														<div class="input-group-append">
															<button class="btn btn-outline-default" type="button" data-toggle="collapse" data-target="#button-collapse" aria-expanded="false" aria-controls="button-collapse">
																<i class="fal fa-edit"></i>
															</button>
														</div>
													</div>
												</div>
												<button type="button" id="button-collapse" class="btn btn-lg btn-default collapse" data-toggle="modal" data-target="#modal-alert">
                                                    <span class="fal fa-check mr-1"></span>
                                                    Submit
                                                </button>
												<div class="spinner-border collapse" role="status"></div>
											</form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
                                <div id="panel-threshold-index" class="panel">
                                    <div class="panel-hdr">
										<h2>
											<span class="fw-300"><i>The Last Few FX Thresholds</i></span>
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
											<table id="dt-basic" class="table table-bordered table-hover table-striped w-100">
												<thead class="thead-dark">
													<tr>
@foreach(\DB::getSchemaBuilder()->getColumnListing($threshold->getModel()->getTable()) as $value)
@if ($value !== 'id')
														<th class="text-capitalize">{{
                                                            \Illuminate\Support\Str::of($value)->replaceMatches('/_id$/', function($match) {
                                                                return strtoupper($match[0]);
                                                            })
                                                            ->replace('_', ' ')
                                                        }}</th>
@endif
@endforeach
													</tr>
												</thead>
												<tbody>
@if ($threshold->exists())
@foreach ($threshold->take(10)->get() as $value)
													<tr>
@foreach(\DB::getSchemaBuilder()->getColumnListing($threshold->getModel()->getTable()) as $key)
@if ($key !== 'id')
@if ($value->{$key} instanceof \Carbon\Carbon)
														<td class="text-center">{{ $value->{$key}->toDayDateTimeString() }}</td>
@elseif (is_numeric($value->{$key}) && (floor($value->{$key}) != $value->{$key}))
														<td class="text-right">
															<span>&#36;</span>
															{{ number_format($value->{$key}, 2, '.', ',') }}
														</td>
@else
														<td>
															{{ $value->{$key} }}
														</td>
@endif
@endif
@endforeach
													</tr>
@endforeach
@endif
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
                                <div id="panel-sismontavar-show" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form action="{{ route('settings-sismontavar.store') }}" method="post">
												@csrf
												
												<div class="form-group">
													<div class="input-group input-group-lg">
														<div class="input-group-prepend">
															<span class="input-group-text bg-transparent py-1 px-3">
																<span class="icon-stack" style="font-size: 2rem">
																	<i class="fal fa-dollar-sign"></i>
																</span>
															</span>
														</div>
														<input type="hidden" name="threshold" value="{{ $sismontavarOption->exists() ? $sismontavarOption->first()->threshold : 0 }}" required>
														<input id="input-group-lg-size" type="text" class="form-control" aria-describedby="input-group-lg-size" value="{{ $sismontavarOption->exists() ? number_format($sismontavarOption->first()->threshold, 2, '.', ',') : 0 }}" autocomplete="off" required readonly>
														<div class="input-group-append">
															<button class="btn btn-outline-default" type="button" data-toggle="collapse" data-target="#button-collapse" aria-expanded="false" aria-controls="button-collapse">
																<i class="fal fa-edit"></i>
															</button>
														</div>
													</div>
												</div>
												<button type="button" id="button-collapse" class="btn btn-lg btn-default collapse" data-toggle="modal" data-target="#modal-alert">
                                                    <span class="fal fa-check mr-1"></span>
                                                    Submit
                                                </button>
												<div class="spinner-border collapse" role="status"></div>
											</form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
                                <div id="panel-sismontavar-index" class="panel">
                                    <div class="panel-hdr">
										<h2>
											<span class="fw-300"><i>The Last Few Sismontavar Options</i></span>
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
											<table id="dt-basic" class="table table-bordered table-hover table-striped w-100">
												<thead class="thead-dark">
													<tr>
@foreach(\DB::getSchemaBuilder()->getColumnListing($sismontavarOption->getModel()->getTable()) as $value)
@if ($value !== 'id')
														<th class="text-capitalize">{{
                                                            \Illuminate\Support\Str::of($value)->replaceMatches('/_id$/', function($match) {
                                                                return strtoupper($match[0]);
                                                            })
                                                            ->replace('_', ' ')
                                                        }}</th>
@endif
@endforeach
													</tr>
												</thead>
												<tbody>
@if ($sismontavarOption->exists())
@foreach ($sismontavarOption->take(10)->get() as $value)
													<tr>
@foreach(\DB::getSchemaBuilder()->getColumnListing($threshold->getModel()->getTable()) as $key)
@if ($key !== 'id')
@if ($value->{$key} instanceof \Carbon\Carbon)
														<td class="text-center">{{ $value->{$key}->toDayDateTimeString() }}</td>
@elseif (is_numeric($value->{$key}) && (floor($value->{$key}) != $value->{$key}))
														<td class="text-right">
															<span>&#36;</span>
															{{ number_format($value->{$key}, 2, '.', ',') }}
														</td>
@else
														<td>
															{{ $value->{$key} }}
														</td>
@endif
@endif
@endforeach
													</tr>
@endforeach
@endif
												</tbody>
											</table>
											<!-- datatable end -->
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
					<script src="/js/datagrid/datatables/datatables.bundle.js"></script>
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
					<script type="text/javascript">
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
							initApp.buildNavigation(myapp_config.navHooks);
						})
						
						$('form[method="post"] #button-collapse').on('show.bs.collapse', function(e) {
							$(e.target).prev().find('input[name="threshold"]').next().prop('readonly', false);
							$(e.target).prev().find('input[name="threshold"]').next().focus();
						})
						
						$('form[method="post"] #button-collapse').on('hide.bs.collapse', function(e) {
							$(e.target).prev().find('input[name="threshold"]').next().prop('readonly', true);
						})
						
					</script>
@endsection