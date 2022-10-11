@extends('layouts.master')

@section('title', 'Dealer Limit')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/datagrid/datatables/datatables.bundle.css">
@endsection

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item"><a href="#settings">Settings</a></li>
                            <li class="breadcrumb-item active">Dealer Limit</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-id-badge'></i> Dealer Limit
                            </h1>
                        </div>
@if (session('status'))
							<div class="alert alert-success">
								{{ session('status') }}
							</div>
@endif
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-role-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
											Dealer Limit
											<span class="fw-300"><i>Head Office</i></span>
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
                                            <table class="table table-bordered table-hover table-striped w-100 dt-basic">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Position</th>
                                                        <th>is_interbank_dealer</th>
                                                        <th>commercial_bank_limit</th>
                                                        <th>Interbank Limit &lpar;Commercial&rpar;</th>
                                                        <th>sales_limit</th>
                                                        <th>Sales Limit</th>
														<th>Updated At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
@foreach ($role->where('is_sales_dealer', true) as $value)
													<tr>
                                                        <td class="text-capitalize">
															{{ $value->name }}
														</td>
                                                        <td>
															{{ $value->is_interbank_dealer }}
                                                        </td>
                                                        <td>
@if ($value->limit)
															{{	$value->limit->commercial_bank_limit }}
@endif
														</td>
														<td class="text-right">
@if ($value->limit && $value->limit->commercial_bank_limit)
															<span>&#36;</span>{{
																number_format($value->limit->commercial_bank_limit, 2, '.', ',' )
															}}
@endif
														</td>
                                                        <td>
@if ($value->limit)
															{{	$value->limit->sales_limit }}
@endif
														</td>
														<td class="text-right">
@if ($value->limit && $value->limit->sales_limit)
															<span>&#36;</span>{{
																number_format($value->limit->sales_limit, 2, '.', ',' )
															}}
@endif
														</td>
														<td class="text-center">
@if ($value->limit)
															{{ $value->limit->updated_at->toDayDateTimeString() }}
@endif
														</td>
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
                                <div id="panel-branch-index" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
											Dealer Limit
											<span class="fw-300"><i>Branch Office</i></span>
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
                                            <table class="table table-bordered table-hover table-striped w-100 dt-basic">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Branch Code</th>
                                                        <th>Branch Name</th>
                                                        <th>Region Name</th>
                                                        <th>sales_limit</th>
                                                        <th>Sales Limit</th>
														<th>Updated At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
@foreach ($branch as $value)
													<tr>
                                                        <td class="text-center">
															{{ $value->code }}
														</td>
                                                        <td>
															{{ $value->name }}
														</td>
                                                        <td>
															{{ $value->region }}
														</td>
                                                        <td>
															{{	$value->sales_limit }}
														</td>
														<td class="text-right">
@if ($value->sales_limit)
															<span>&#36;</span>{{
																number_format($value->sales_limit, 2, '.', ',' )
															}}
@endif
														</td>
														<td class="text-center">
@if ($value->updated_at)
															{{ $value->updated_at->toDayDateTimeString() }}
@endif
														</td>
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
					<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<form action="{{ route('settings-roles.store') }}" method="post">
									@csrf
									
									<div class="modal-header">
										<h4 class="modal-title">
											Dealer Limit
											<small class="m-0 text-muted">Head Office</small>
										</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true"><i class="fal fa-times"></i></span>
										</button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label class="form-label" for="name">Name</label>
											<input type="text" name="name" class="form-control text-capitalize" readonly>
										</div>
										<div class="form-group">
											<input type="hidden" name="is-interbank-dealer">
										</div>
										<div class="form-group">
											<label class="form-label" for="commercial-bank-limit">Interbank Limit &lpar;Commercial&rpar;</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">&#36;</span>
												</div>
												<input type="hidden" name="commercial-bank-limit" required>
												<input type="text" class="form-control" autocomplete="off" required>
											</div>
										</div>
										<div class="form-group">
											<label class="form-label" for="sales-limit">Sales Limit</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">&#36;</span>
												</div>
												<input type="hidden" name="sales-limit" required>
												<input type="text" class="form-control" autocomplete="off" required>
											</div>
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
					<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<form action="{{ route('settings-branches.store') }}" method="post">
									@csrf
									
									<div class="modal-header">
										<h4 class="modal-title">
											Dealer Limit
											<small class="m-0 text-muted">Branch Office</small>
										</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true"><i class="fal fa-times"></i></span>
										</button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label class="form-label" for="code">Branch Code</label>
											<input type="text" name="code" class="form-control" readonly>
										</div>
										<div class="form-group">
											<label class="form-label" for="name">Branch Name</label>
											<input type="text" name="name" class="form-control" readonly>
										</div>
										<div class="form-group">
											<label class="form-label" for="region">Region Name</label>
											<input type="text" name="region" class="form-control" readonly>
										</div>
										<div class="form-group">
											<label class="form-label" for="sales-limit">Sales Limit</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">&#36;</span>
												</div>
												<input type="hidden" name="sales-limit" required>
												<input type="text" class="form-control" autocomplete="off" required>
											</div>
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
					<script src="/js/formplugins/inputmask/inputmask.bundle.js"></script>
					<script type="text/javascript">
						$(document).ready( function() {
							initApp.destroyNavigation(myapp_config.navHooks);
							$('a[href="{!! url()->current() !!}"]').parent().attr('class', 'active');
							$('a[href="{!! url()->current() !!}"]').parent().parent().parent().attr('class', 'active open');
							initApp.buildNavigation(myapp_config.navHooks);
						})
					</script>
@endsection
