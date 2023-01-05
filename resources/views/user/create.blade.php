@extends('layouts.master')

@section('title', ucfirst(collect(request()->segments())->last()).' - Users')

@section('stylesheet')
		<link rel="stylesheet" media="screen, print" href="/css/notifications/sweetalert2/sweetalert2.bundle.css">
		<link rel="stylesheet" media="screen, print" href="/css/formplugins/select2/select2.bundle.css">
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
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active">{{ ucfirst(collect(request()->segments())->last()) }}</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-edit'></i> {{ ucfirst(collect(request()->segments())->last()) }} / Users
                            </h1>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-user-create" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form action="{{ route('api.users.store') }}" method="post">
												<input type="hidden" name="branch-name">
                                                <div class="form-group">
                                                    <label class="form-label" for="email">E-Mail</label>
                                                    <input type="email" name="email" class="form-control" pattern=".+@idn.ccb.com" required>
                                                </div>
												<div class="form-group collapse show">
                                                    <label class="form-label" for="region">Region</label>
                                                    <select name="region" class="form-control" onkeydown="event.preventDefault()">
                                                        <option value>Choose</option>
@foreach ($regions->sortBy('region') as $value)
														<option value="{{ $value->region }}">{{ $value->region }}</option>
@endforeach
                                                    </select>
                                                </div>
												<div class="form-group collapse">
                                                    <label class="form-label" for="branch">Branch</label>
                                                    <select name="branch-code" class="form-control" onkeydown="event.preventDefault()">
                                                        <option value>Choose</option>
                                                    </select>
                                                </div>
												<div class="form-group collapse show">
                                                    <label class="form-label" for="role-permission">Role Permission</label>
                                                    <select name="role-id" class="form-control text-capitalize" onkeydown="event.preventDefault()" required>
                                                        <option class="text-capitalize" value>choose</option>
@foreach ($role->where('name', '!=', 'super administrator') as $value)
														<option class="text-capitalize" value="{{ $value->id }}">{{ $value->name }}</option>
@endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="expires-at">Expires At</label>
                                                    <input type="date" name="expires-at" class="form-control" min="{{ \Carbon\Carbon::today()->toDateString() }}" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="trustee">Trustee :</label>
                                                    <select class="custom-select" id="trustee" name="trustee" multiple>
                                                        <option value="TRS001" >FTP Curve Data</option>
                                                    </select>
                                                </div>
												<button type="button" class="btn btn-lg btn-default">
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
					<script src="/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
                    <script src="/js/formplugins/select2/select2.bundle.js"></script>
					<script type="text/javascript">
                        $(document).ready( function() {
                        $('#trustee').select2({
                            placeholder: "-- Select Trustee --",
                            allowClear: true
                        });
                    });

                        $(document).find('#panel-user-create form button').on('click', function(e) {
							if ($(e.currentTarget).closest('form').get(0).checkValidity()) {
								Swal.fire({
									title: 'Are you sure?',
									type: 'warning',
									showCancelButton: true,
									confirmButtonText: 'Yes, submit it!'
								}).then( function(result) {
									if (result.value) {
										var data = {};
										data.api_token = $(document).find('meta[name="api-token"]').attr('content');
										$(e.currentTarget).closest('form').find('input, select').each( function(key, element) {
											data[$(element).attr('name')] = $(element).val();
										});

										Swal.fire({
											title: 'Please wait...',
											showConfirmButton: false,
											timer: 60000,
											onBeforeOpen: function onBeforeOpen() {
												Swal.showLoading();
											},
											onOpen: function onOpen() {
												$.ajax({
													headers: {
														'X-CSRF-TOKEN': $(document).find('meta[name="csrf-token"]').attr('content')
													},
													method: 'POST',
													url: $(e.currentTarget).closest('form').attr('action'),
													data: data

												}).done( function(response) {
													Swal.close();
													Swal.fire({
														type: 'success',
														title: response.status,
														showConfirmButton: false,
														timer: 2000
													});

													setTimeout(() => {
														window.location.href = response.redirect;
													}, 2000)

												}).fail( function(jqXHR, textStatus, errorThrown) {
													Swal.close();

													if (jqXHR.responseJSON.hasOwnProperty('errors')) {
														jqXHR.responseJSON.message = Object.keys(jqXHR.responseJSON.errors).map( function(value) {

															return jqXHR.responseJSON.errors[value].join();

														}).join(document.createElement('br').outerHTML);
													}

													Swal.fire('Oops...', jqXHR.responseJSON.message, 'error');
												})
											}
										})
									}
								})
							}
						})

						$('#panel-user-create form button').on('focus', function(e) {
							if (!$(e.currentTarget).closest('form').get(0).checkValidity()) {
								$(e.currentTarget).closest('form').get(0).reportValidity();
							}
						})

					</script>
@endsection
