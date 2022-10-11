@extends('layouts.master')

@section('title', '500 Internal Server Error')

@section('content')
					<!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                        <ol class="breadcrumb page-breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ config('app.name') }}</a></li>
                            <li class="breadcrumb-item active">500 Internal Server Error</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="subheader">
                        </div>
                        <div class="h-alt-hf d-flex flex-column align-items-center justify-content-center text-center">
                            <h1 class="page-error color-fusion-500">
                                ERROR <span class="text-gradient">500</span>
                                <small class="fw-500">
                                    Something <u>went</u> wrong!
                                </small>
                            </h1>
                            <h3 class="fw-500 mb-5">
                                You have experienced a technical error. We apologize.
                            </h3>
                            <h4>
                                We are working hard to correct this issue. Please wait a few moments and try your search again.
                                <br>In the meantime, check out whats new on {{ config('app.name') }}:
                            </h4>
                        </div>
                    </main>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
                    <!-- END Page Content -->
@endsection
