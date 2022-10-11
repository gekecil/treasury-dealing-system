@extends('layouts.master')

@section('title', 'Profile')

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
                            <li class="breadcrumb-item active">Profile</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-edit'></i> Profile
                            </h1>
                        </div>
						<div class="row">
                            <div class="col-xl-12">
                                <!-- profile summary -->
                                <div class="card mb-g rounded-top">
                                    <div class="row no-gutters row-grid">
                                        <div class="col-12">
                                            <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                                <img src="/img/avatars/avatar-male.png" class="rounded-circle shadow-2 profile-image img-thumbnail" alt="">
                                                <h5 class="mb-0 fw-700 text-center mt-3">
                                                    {{ $user->full_name }}
@if ($user->branch_code)
                                                    <small class="text-muted mb-0">{{ $user->branch->first()->name }}, {{
                                                        $user->branch->first()->region
                                                    }}</small>
@elseif ($user->role_id)
                                                    <small class="text-muted mb-0 text-capitalize">{{ $user->role->name }}</small>
@endif
                                                </h5>
                                            </div>
                                        </div>
										<div class="col-12">
											<div class="text-center py-3">
												<h5 class="mb-0 fw-700">
													{{ $user->token->api_token }}
													<small class="text-muted mb-0">Your Token</small>
												</h5>
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