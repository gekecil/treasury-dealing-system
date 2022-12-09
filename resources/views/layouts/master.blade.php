<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title') - {{ config('app.name') }}
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
    <!-- Call App Mode on ios devices -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no">
@if (auth()->check() && auth()->user()->exists)
    <!-- API Token -->
    <meta name="api-token" content="{{ auth()->user()->token()->firstOrNew([], ['api_token' => null])->api_token }}">
@endif
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- base css -->
    <link rel="stylesheet" media="screen, print" href="/css/vendors.bundle.css">
    <link rel="stylesheet" media="screen, print" href="/css/app.bundle.css">
    <!-- Place favicon.ico in the root directory -->
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.ico">
    <link rel="icon" type="image/ico" sizes="32x32" href="/favicon.ico">
    <link rel="mask-icon" href="/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
@yield('stylesheet')

    <style type="text/css">

        .info-card-text
        {
            font-size: 1rem;
        }

        .info-card-text > span
        {
            font-size: .75rem;
        }

        .panel-content .btn-add a
        {
            color: inherit;
        }

        table .pointer
        {
            cursor: pointer;
        }

        .modal:not(.js-modal-settings):not(.modal-alert) .modal-header .modal-title
        {
            text-transform: capitalize;
        }

        .fit-image
        {
            object-fit: cover;
            object-position: top;
        }

        .panel-interbank-deal .btn:focus, .panel-interbank-deal .btn.focus, .panel-interbank-deal .btn:active, .panel-interbank-deal .btn.active,
        .panel-sales-deal .btn:focus, .panel-sales-deal .btn.focus, .panel-sales-deal .btn:active, .panel-sales-deal .btn.active
        {
            -webkit-box-shadow: none;
            box-shadow: none;
        }

        .panel-interbank-deal .card .card-title,
        .panel-sales-deal .card .card-title
        {
            font-size: 1.65rem;
        }

        #alert-dismissible .fade:not(.show)
        {
            display: none;
        }

    </style>

</head>
<body class="mod-bg-1">
<!-- DOC: script to save and load page settings -->
<script>
    /**
     *	This script should be placed right after the body tag for fast execution
     *	Note: the script is written in pure javascript and does not depend on thirdparty library
     **/
    'use strict';

    var themeURLDefault = '/css/themes/cust-theme-4.css';
    var themeOptionsDefault = 'mod-bg-1 header-function-fixed nav-function-fixed';

    var classHolder = document.getElementsByTagName("BODY")[0],
        /**
         * Load from localstorage
         **/
        themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) :
            {themeURL: themeURLDefault},

        themeURL = themeSettings.themeURL || '',
        themeOptions = themeSettings.themeOptions || '';
    /**
     * Load theme options
     **/
    if (themeSettings.themeOptions)
    {
        classHolder.className = themeSettings.themeOptions;
        console.log("%c✔ Theme settings loaded", "color: #148f32");
    }
    else
    {
        console.log("Heads up! Theme settings is empty or does not exist, loading default settings...");
    }
    if (themeSettings.themeURL && !document.getElementById('mytheme'))
    {
        var cssfile = document.createElement('link');
        cssfile.id = 'mytheme';
        cssfile.rel = 'stylesheet';
        cssfile.href = themeURL;
        document.getElementsByTagName('head')[0].appendChild(cssfile);
    }
    /**
     * Save to localstorage
     **/
    var saveSettings = function()
    {
        themeSettings.themeOptions = String(classHolder.className).split(/[^\w-]+/).filter(function(item)
        {
            return /^(nav|header|mod|display)-/i.test(item);
        }).join(' ');
        if (document.getElementById('mytheme'))
        {
            themeSettings.themeURL = document.getElementById('mytheme').getAttribute("href");
        };
        localStorage.setItem('themeSettings', JSON.stringify(themeSettings));
    }
    /**
     * Reset settings
     **/
    var resetSettings = function()
    {
        localStorage.setItem("themeSettings", "");
    }
    /**
     * Load default theme options
     **/
    if (!themeOptions || (themeOptions === 'mod-bg-1')) {
        classHolder.className = themeOptionsDefault;
        saveSettings();
        console.log("%c✔ Theme settings loaded", "color: #148f32");
    }

</script>
<!-- BEGIN Page Wrapper -->
<div class="page-wrapper">
    <div class="page-inner">
        <!-- BEGIN Left Aside -->
        <aside class="page-sidebar">
            <div class="page-logo">
                <a href="{{ route('dashboard') }}" class="press-scale-down d-flex align-items-center position-relative">
                    <img class="img-fluid" src="/img/logo.webp" alt="SmartAdmin WebApp" aria-roledescription="logo">
                </a>
            </div>
            <!-- BEGIN PRIMARY NAVIGATION -->
            <nav id="js-primary-nav" class="primary-nav" role="navigation">
                <div class="nav-filter">
                    <div class="position-relative">
                        <input type="text" id="nav_filter_input" placeholder="Filter menu" class="form-control" tabindex="0">
                        <a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
                            <i class="fal fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
@if (auth()->check() && auth()->user()->exists)
                    <div class="info-card">
                        <img src="/img/avatars/avatar-male.png" alt="profile photo" class="profile-image rounded">
                        <div class="info-card-text w-100 ml-0 text-center">
                            <a href="{{ route('profile') }}" class="text-white">
                                    <span class="text-truncate text-truncate-sm text-uppercase d-inline-block">
                                        {{ auth()->user()->first_name }}
                                    </span>
                            </a>
                            <span class="d-block">
@if (auth()->user()->role)
                                {{ ucwords(auth()->user()->role->name) }}
@else
                                {{ auth()->user()->branch()->first()->name }}
@endif
								</span>
                        </div>
                        <img src="/img/backgrounds/bg-3.png" class="cover" alt="cover">
                        <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                            <i class="fal fa-angle-down"></i>
                        </a>
                    </div>
                    <ul id="js-nav-menu" class="nav-menu">
                        <li>
                            <a href="{{ route('dashboard') }}" title="Dashboard" data-filter-tags="dashboard">
                                <i class="fal fa-chart-pie"></i>
                                <span class="nav-link-text" data-i18n="nav.dashboard">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#interbank" title="Interbank" data-filter-tags="interbank">
                                <i class="fal fa-building"></i>
                                <span class="nav-link-text" data-i18n="nav.interbank">Interbank</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('interbank-dealing.index') }}" title="Dealing" data-filter-tags="interbank dealing">
                                        <i class="fal fa-clipboard-check"></i>
                                        <span class="nav-link-text" data-i18n="nav.interbank_dealing">Dealing</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('interbank-nop.index') }}" title="NOP" data-filter-tags="interbank nop">
                                        <i class="ni ni-wallet"></i>
                                        <span class="nav-link-text" data-i18n="nav.interbank_nop">NOP</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#sales" title="Sales" data-filter-tags="sales">
                                <i class="fal fa-usd-square"></i>
                                <span class="nav-link-text" data-i18n="nav.sales">Sales</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('sales-fx.index') }}" title="FX" data-filter-tags="sales fx">
                                        <i class="ni ni-my-apps"></i>
                                        <span class="nav-link-text" data-i18n="nav.sales_fx">FX</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('sales-special-rate-deal.index') }}" title="Request for FX Deal" data-filter-tags="sales request for fx deal">
                                        <i class="fal fa-cut"></i>
                                        <span class="nav-link-text" data-i18n="nav.sales_special_rate_deal">Request for FX Deal</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('sales-cancellations.index') }}" title="Cancellations" data-filter-tags="sales cancellations">
                                        <i class="fal fa-times-square"></i>
                                        <span class="nav-link-text" data-i18n="nav.sales_cancellations">Cancellations</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('sales-rejections.index') }}" title="Rejections" data-filter-tags="sales rejections">
                                        <i class="fal fa-times-circle"></i>
                                        <span class="nav-link-text" data-i18n="nav.sales_cancellations">Rejections</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('sales-blotter.index') }}" title="Blotter" data-filter-tags="sales blotter">
                                        <i class="fal fa-archive"></i>
                                        <span class="nav-link-text" data-i18n="nav.sales_blotter">Blotter</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('sales-top-ten-obox.index') }}" title="Top Ten OBOX" data-filter-tags="sales top ten obox">
                                        <i class="fal fa-arrow-alt-to-top"></i>
                                        <span class="nav-link-text" data-i18n="nav.sales_top_ten_obox">Top Ten OBOX</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="{{ route('market') }}" title="Markets" data-filter-tags="markets">
                                <i class="fal fa-calendar-alt"></i>
                                <span class="nav-link-text" data-i18n="nav.markets">Markets</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('currencies.index') }}" title="Currencies" data-filter-tags="currencies">
                                <i class="fal fa-flag-checkered"></i>
                                <span class="nav-link-text" data-i18n="nav.currencies">Currencies</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('closing-rates.index') }}" title="Closing Rates" data-filter-tags="closing rates">
                                <i class="fal fa-exchange-alt"></i>
                                <span class="nav-link-text" data-i18n="nav.closing_rates">Closing Rates</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounts.index') }}" title="Accounts" data-filter-tags="accounts">
                                <i class="fal fa-id-card"></i>
                                <span class="nav-link-text" data-i18n="nav.accounts">Accounts</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('news.index') }}" title="News" data-filter-tags="news">
                                <i class="fal fa-newspaper"></i>
                                <span class="nav-link-text" data-i18n="nav.news">News</span>
                            </a>
                        </li>
                        <?php
                        if(in_array('TRS001', explode(',', auth()->user()->trustee))){
                        ?>
                        <li class="collapse show">
                            <a href="{{ route('fcd.index') }}" title="FTP Curve Data" data-filter-tags="ftp curve data">
                                <i class="fal fa-file-excel"></i>
                                <span class="nav-link-text" data-i18n="nav">FTP Curve Data</span>
                            </a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="{{ route('users.index') }}" title="Users" data-filter-tags="users">
                                <i class="fal fa-user"></i>
                                <span class="nav-link-text" data-i18n="nav.users">Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="#settings" title="Settings" data-filter-tags="settings">
                                <i class="fal fa-cogs"></i>
                                <span class="nav-link-text" data-i18n="nav.settings">Settings</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('settings-threshold.index') }}" title="Thresholds" data-filter-tags="settings threshold">
                                        <i class="fal fa-bullhorn"></i>
                                        <span class="nav-link-text" data-i18n="nav.settings_threshold">Thresholds</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('settings-dealer-limits.index') }}" title="Dealer Limits" data-filter-tags="settings dealer limits">
                                        <i class="fal fa-id-badge"></i>
                                        <span class="nav-link-text" data-i18n="nav.settings_dealer_limits">Dealer Limits</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
@endif
                <div class="filter-message js-filter-message bg-success-600"></div>
            </nav>
            <!-- END PRIMARY NAVIGATION -->

        </aside>
        <!-- END Left Aside -->
        <div class="page-content-wrapper">
            <!-- BEGIN Page Header -->
            <header class="page-header" role="banner">
                <!-- we need this logo when user switches to nav-function-top -->

                <!-- DOC: nav menu layout change shortcut -->
                <div class="hidden-md-down dropdown-icon-menu position-relative">
                    <a href="#" class="header-btn btn js-waves-off" data-action="toggle" data-class="nav-function-hidden" title="Hide Navigation">
                        <i class="ni ni-menu"></i>
                    </a>
                </div>
                <!-- DOC: mobile button appears during mobile width -->
                <div class="hidden-lg-up">
                    <a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on">
                        <i class="ni ni-menu"></i>
                    </a>
                </div>
                <div class="search">
                    <form class="app-forms hidden-xs-down" role="search" action="/search" autocomplete="off" method="get">
                        <input type="text" id="search-field" name="query" placeholder="Search for anything" class="form-control" tabindex="1">
                    </form>
                </div>
                <div class="ml-auto d-flex">
                    <!-- activate app search icon (mobile) -->
                    <div class="hidden-sm-up">
                        <a href="#" class="header-icon" data-action="toggle" data-class="mobile-search-on" data-focus="search-field" title="Search">
                            <i class="fal fa-search"></i>
                        </a>
                    </div>
                    <!-- app settings -->
                    <div class="hidden-md-down">
                        <a href="#" class="header-icon" data-toggle="modal" data-target=".js-modal-settings">
                            <i class="fal fa-cog"></i>
                        </a>
                    </div>
                    <!-- app notification -->
                    <div>
                        <a href="#" class="header-icon" data-toggle="dropdown" title="You got {{ App\Notification::where('confirmed', false)->count() }} notifications">
                            <i class="fal fa-bell"></i>
                            <span class="badge badge-icon">{{ App\Notification::where('confirmed', false)->count() }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-animated dropdown-xl">
                            <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center rounded-top mb-2">
                                <h4 class="m-0 text-center color-white">
                                    <a class="text-light" href="{{ route('dashboard') }}">
                                        {{ App\Notification::where('confirmed', false)->count() }} New
                                        <small class="mb-0 opacity-80">Notifications</small>
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </div>
@if (auth()->check() && auth()->user()->exists)
                    <!-- app user menu -->
                        <div>
                            <a href="#" data-toggle="dropdown" title="{{ auth()->user()->email }}" class="header-icon d-flex align-items-center justify-content-center ml-2">
                                <img src="/img/avatars/avatar-male.png" width="32" height="32" class="profile-image rounded-circle fit-image" alt="User Avatar">
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                    <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                            <span class="mr-2">
                                                <img src="/img/avatars/avatar-male.png" class="rounded-circle profile-image" alt="User Avatar">
                                            </span>
                                        <div class="info-card-text">
                                            <div class="fs-lg text-truncate text-truncate-lg">
                                                {{
                                                    auth()->user()->full_name
                                                }}
                                            </div>
                                            <span class="text-truncate text-truncate-md opacity-80">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-action="app-reset">
                                    <span data-i18n="drpdwn.reset_layout">Reset Layout</span>
                                </a>
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target=".js-modal-settings">
                                    <span data-i18n="drpdwn.settings">Settings</span>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-action="app-fullscreen">
                                    <span data-i18n="drpdwn.fullscreen">Fullscreen</span>
                                    <i class="float-right text-muted fw-n">F11</i>
                                </a>
                                <a href="#" class="dropdown-item" data-action="app-print">
                                    <span data-i18n="drpdwn.print">Print</span>
                                    <i class="float-right text-muted fw-n">Ctrl + P</i>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a class="dropdown-item fw-500 pt-3 pb-3" href="{{ route('sign-out') }}">
                                    <span data-i18n="drpdwn.page-sign-out">Sign Out</span>
                                    <span class="float-right fw-n">&commat;idn.ccb.com</span>
                                </a>
                            </div>
                        </div>
@endif
                </div>
            </header>
            <!-- END Page Header -->
            <!-- BEGIN Page Content -->

        @yield('content')

            <!-- END Page Content -->
            <!-- BEGIN Modal Alert -->
            <div class="modal modal-alert fade" id="modal-alert" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Are you sure?
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><i class="fal fa-times"></i></span>
                            </button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Modal Alert -->
            <!-- BEGIN Page Footer -->
            <footer class="page-footer" role="contentinfo">
                <div class="d-flex align-items-center flex-1 text-muted">
                    <span class="hidden-md-down fw-700">
						2021 © {{ config('app.name') }}. Developed by IT System Development CCBI.
					</span>
                </div>
                <div>
                    <ul class="list-table m-0">
                        <li><a href="#about" class="text-secondary fw-700">About</a></li>
                        <li class="pl-3"><a href="#license" class="text-secondary fw-700">License</a></li>
                        <li class="pl-3"><a href="#documentation" class="text-secondary fw-700">Documentation</a></li>
                        <li class="pl-3 fs-xl"><a href="#help" class="text-secondary" target="_blank"><i class="fal fa-question-circle" aria-hidden="true"></i></a></li>
                    </ul>
                </div>
            </footer>
            <!-- END Page Footer -->
            <!-- BEGIN Color profile -->
            <!-- this area is hidden and will not be seen on screens or screen readers -->
            <!-- we use this only for CSS color refernce for JS stuff -->
            <p id="js-color-profile" class="d-none">
                <span class="color-primary-50"></span>
                <span class="color-primary-100"></span>
                <span class="color-primary-200"></span>
                <span class="color-primary-300"></span>
                <span class="color-primary-400"></span>
                <span class="color-primary-500"></span>
                <span class="color-primary-600"></span>
                <span class="color-primary-700"></span>
                <span class="color-primary-800"></span>
                <span class="color-primary-900"></span>
                <span class="color-info-50"></span>
                <span class="color-info-100"></span>
                <span class="color-info-200"></span>
                <span class="color-info-300"></span>
                <span class="color-info-400"></span>
                <span class="color-info-500"></span>
                <span class="color-info-600"></span>
                <span class="color-info-700"></span>
                <span class="color-info-800"></span>
                <span class="color-info-900"></span>
                <span class="color-danger-50"></span>
                <span class="color-danger-100"></span>
                <span class="color-danger-200"></span>
                <span class="color-danger-300"></span>
                <span class="color-danger-400"></span>
                <span class="color-danger-500"></span>
                <span class="color-danger-600"></span>
                <span class="color-danger-700"></span>
                <span class="color-danger-800"></span>
                <span class="color-danger-900"></span>
                <span class="color-warning-50"></span>
                <span class="color-warning-100"></span>
                <span class="color-warning-200"></span>
                <span class="color-warning-300"></span>
                <span class="color-warning-400"></span>
                <span class="color-warning-500"></span>
                <span class="color-warning-600"></span>
                <span class="color-warning-700"></span>
                <span class="color-warning-800"></span>
                <span class="color-warning-900"></span>
                <span class="color-success-50"></span>
                <span class="color-success-100"></span>
                <span class="color-success-200"></span>
                <span class="color-success-300"></span>
                <span class="color-success-400"></span>
                <span class="color-success-500"></span>
                <span class="color-success-600"></span>
                <span class="color-success-700"></span>
                <span class="color-success-800"></span>
                <span class="color-success-900"></span>
                <span class="color-fusion-50"></span>
                <span class="color-fusion-100"></span>
                <span class="color-fusion-200"></span>
                <span class="color-fusion-300"></span>
                <span class="color-fusion-400"></span>
                <span class="color-fusion-500"></span>
                <span class="color-fusion-600"></span>
                <span class="color-fusion-700"></span>
                <span class="color-fusion-800"></span>
                <span class="color-fusion-900"></span>
            </p>
            <!-- END Color profile -->
        </div>
    </div>
</div>
<!-- END Page Wrapper -->
<!-- BEGIN Quick Menu -->
<!-- to add more items, please make sure to change the variable '$menu-items: number;' in your _page-components-shortcut.scss -->
<nav class="shortcut-menu d-none d-sm-block">
    <input type="checkbox" class="menu-open" name="menu-open" id="menu_open" />
    <label for="menu_open" class="menu-open-button ">
        <span class="app-shortcut-icon d-block"></span>
    </label>
    <a href="#" class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Scroll Top">
        <i class="fal fa-arrow-up"></i>
    </a>
    <a href="{{ route('sign-out') }}" class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Sign out">
        <i class="fal fa-sign-out"></i>
    </a>
    <a href="#" class="menu-item btn" data-action="app-fullscreen" data-toggle="tooltip" data-placement="left" title="Full Screen">
        <i class="fal fa-expand"></i>
    </a>
    <a href="#" class="menu-item btn" data-action="app-print" data-toggle="tooltip" data-placement="left" title="Print page">
        <i class="fal fa-print"></i>
    </a>
    <a href="#" class="menu-item btn" data-action="app-voice" data-toggle="tooltip" data-placement="left" title="Voice command">
        <i class="fal fa-microphone"></i>
    </a>
</nav>
<!-- END Quick Menu -->
<!-- BEGIN Page Settings -->
<div class="modal fade js-modal-settings modal-backdrop-transparent" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-right modal-md">
        <div class="modal-content">
            <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100">
                <h4 class="m-0 text-center color-white">
                    Layout Settings
                    <small class="mb-0 opacity-80">User Interface Settings</small>
                </h4>
                <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="settings-panel">
                    <div class="mt-4 d-table w-100 px-5">
                        <div class="d-table-cell align-middle">
                            <h5 class="p-0">
                                App Layout
                            </h5>
                        </div>
                    </div>
                    <div class="list" id="fh">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="header-function-fixed"></a>
                        <span class="onoffswitch-title">Fixed Header</span>
                        <span class="onoffswitch-title-desc">header is in a fixed at all times</span>
                    </div>
                    <div class="list" id="nff">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-fixed"></a>
                        <span class="onoffswitch-title">Fixed Navigation</span>
                        <span class="onoffswitch-title-desc">left panel is fixed</span>
                    </div>
                    <div class="list" id="nfm">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-minify"></a>
                        <span class="onoffswitch-title">Minify Navigation</span>
                        <span class="onoffswitch-title-desc">Skew nav to maximize space</span>
                    </div>
                    <div class="list" id="nfh">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-hidden"></a>
                        <span class="onoffswitch-title">Hide Navigation</span>
                        <span class="onoffswitch-title-desc">roll mouse on edge to reveal</span>
                    </div>
                    <div class="list" id="nft">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-top"></a>
                        <span class="onoffswitch-title">Top Navigation</span>
                        <span class="onoffswitch-title-desc">Relocate left pane to top</span>
                    </div>
                    <div class="list" id="mmb">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-main-boxed"></a>
                        <span class="onoffswitch-title">Boxed Layout</span>
                        <span class="onoffswitch-title-desc">Encapsulates to a container</span>
                    </div>
                    <div class="expanded">
                        <ul class="">
                            <li>
                                <div class="bg-fusion-50" data-action="toggle" data-class="mod-bg-1"></div>
                            </li>
                            <li>
                                <div class="bg-warning-200" data-action="toggle" data-class="mod-bg-2"></div>
                            </li>
                            <li>
                                <div class="bg-primary-200" data-action="toggle" data-class="mod-bg-3"></div>
                            </li>
                            <li>
                                <div class="bg-success-300" data-action="toggle" data-class="mod-bg-4"></div>
                            </li>
                        </ul>
                        <div class="list" id="mbgf">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-fixed-bg"></a>
                            <span class="onoffswitch-title">Fixed Background</span>
                        </div>
                    </div>
                    <div class="mt-4 d-table w-100 px-5">
                        <div class="d-table-cell align-middle">
                            <h5 class="p-0">
                                Mobile Menu
                            </h5>
                        </div>
                    </div>
                    <div class="list" id="nmp">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-mobile-push"></a>
                        <span class="onoffswitch-title">Push Content</span>
                        <span class="onoffswitch-title-desc">Content pushed on menu reveal</span>
                    </div>
                    <div class="list" id="nmno">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-mobile-no-overlay"></a>
                        <span class="onoffswitch-title">No Overlay</span>
                        <span class="onoffswitch-title-desc">Removes mesh on menu reveal</span>
                    </div>
                    <div class="list" id="sldo">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-mobile-slide-out"></a>
                        <span class="onoffswitch-title">Off-Canvas <sup>(beta)</sup></span>
                        <span class="onoffswitch-title-desc">Content overlaps menu</span>
                    </div>
                    <div class="mt-4 d-table w-100 px-5">
                        <div class="d-table-cell align-middle">
                            <h5 class="p-0">
                                Accessibility
                            </h5>
                        </div>
                    </div>
                    <div class="list" id="mbf">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-bigger-font"></a>
                        <span class="onoffswitch-title">Bigger Content Font</span>
                        <span class="onoffswitch-title-desc">content fonts are bigger for readability</span>
                    </div>
                    <div class="list" id="mhc">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-high-contrast"></a>
                        <span class="onoffswitch-title">High Contrast Text (WCAG 2 AA)</span>
                        <span class="onoffswitch-title-desc">4.5:1 text contrast ratio</span>
                    </div>
                    <div class="list" id="mcb">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-color-blind"></a>
                        <span class="onoffswitch-title">Daltonism <sup>(beta)</sup> </span>
                        <span class="onoffswitch-title-desc">color vision deficiency</span>
                    </div>
                    <div class="list" id="mpc">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-pace-custom"></a>
                        <span class="onoffswitch-title">Preloader Inside</span>
                        <span class="onoffswitch-title-desc">preloader will be inside content</span>
                    </div>
                    <div class="mt-4 d-table w-100 px-5">
                        <div class="d-table-cell align-middle">
                            <h5 class="p-0">
                                Global Modifications
                            </h5>
                        </div>
                    </div>
                    <div class="list" id="mcbg">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-clean-page-bg"></a>
                        <span class="onoffswitch-title">Clean Page Background</span>
                        <span class="onoffswitch-title-desc">adds more whitespace</span>
                    </div>
                    <div class="list" id="mhni">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-hide-nav-icons"></a>
                        <span class="onoffswitch-title">Hide Navigation Icons</span>
                        <span class="onoffswitch-title-desc">invisible navigation icons</span>
                    </div>
                    <div class="list" id="dan">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-disable-animation"></a>
                        <span class="onoffswitch-title">Disable CSS Animation</span>
                        <span class="onoffswitch-title-desc">Disables CSS based animations</span>
                    </div>
                    <div class="list" id="mhic">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-hide-info-card"></a>
                        <span class="onoffswitch-title">Hide Info Card</span>
                        <span class="onoffswitch-title-desc">Hides info card from left panel</span>
                    </div>
                    <div class="list" id="mlph">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-lean-subheader"></a>
                        <span class="onoffswitch-title">Lean Subheader</span>
                        <span class="onoffswitch-title-desc">distinguished page header</span>
                    </div>
                    <div class="list" id="mnl">
                        <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-nav-link"></a>
                        <span class="onoffswitch-title">Hierarchical Navigation</span>
                        <span class="onoffswitch-title-desc">Clear breakdown of nav links</span>
                    </div>
                    <div class="list mt-1">
                        <span class="onoffswitch-title">Global Font Size <small>(RESETS ON REFRESH)</small> </span>
                        <div class="btn-group btn-group-sm btn-group-toggle my-2" data-toggle="buttons">
                            <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-sm" data-target="html">
                                <input type="radio" name="changeFrontSize"> SM
                            </label>
                            <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text" data-target="html">
                                <input type="radio" name="changeFrontSize" checked=""> MD
                            </label>
                            <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-lg" data-target="html">
                                <input type="radio" name="changeFrontSize"> LG
                            </label>
                            <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-xl" data-target="html">
                                <input type="radio" name="changeFrontSize"> XL
                            </label>
                        </div>
                        <span class="onoffswitch-title-desc d-block mb-0">Change <strong>root</strong> font size to effect rem
                                    values</span>
                    </div>
                    <hr class="mb-0 mt-4">
                    <div class="mt-2 d-table w-100 pl-5 pr-3">
                        <div class="fs-xs text-muted p-2 alert alert-warning mt-3 mb-2">
                            <i class="fal fa-exclamation-triangle text-warning mr-2"></i>The settings below uses localStorage to load
                            the external CSS file as an overlap to the base css. Due to network latency and CPU utilization, you may
                            experience a brief flickering effect on page load which may show the intial applied theme for a split
                            second. Setting the prefered style/theme in the header will prevent this from happening.
                        </div>
                    </div>
                    <div class="mt-2 d-table w-100 pl-5 pr-3">
                        <div class="d-table-cell align-middle">
                            <h5 class="p-0">
                                Theme colors
                            </h5>
                        </div>
                    </div>
                    <div class="expanded theme-colors pl-5 pr-3">
                        <ul class="m-0">
                            <li>
                                <a href="#" id="myapp-0" data-action="theme-update" data-themesave data-theme="" data-toggle="tooltip" data-placement="top" title="Wisteria (base css)" data-original-title="Wisteria (base css)"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-1" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-1.css" data-toggle="tooltip" data-placement="top" title="Tapestry" data-original-title="Tapestry"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-2" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-2.css" data-toggle="tooltip" data-placement="top" title="Atlantis" data-original-title="Atlantis"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-3" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-3.css" data-toggle="tooltip" data-placement="top" title="Indigo" data-original-title="Indigo"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-4" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-4.css" data-toggle="tooltip" data-placement="top" title="Dodger Blue" data-original-title="Dodger Blue"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-5" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-5.css" data-toggle="tooltip" data-placement="top" title="Tradewind" data-original-title="Tradewind"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-6" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-6.css" data-toggle="tooltip" data-placement="top" title="Cranberry" data-original-title="Cranberry"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-7" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-7.css" data-toggle="tooltip" data-placement="top" title="Oslo Gray" data-original-title="Oslo Gray"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-8" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-8.css" data-toggle="tooltip" data-placement="top" title="Chetwode Blue" data-original-title="Chetwode Blue"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-9" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-9.css" data-toggle="tooltip" data-placement="top" title="Apricot" data-original-title="Apricot"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-10" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-10.css" data-toggle="tooltip" data-placement="top" title="Blue Smoke" data-original-title="Blue Smoke"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-11" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-11.css" data-toggle="tooltip" data-placement="top" title="Green Smoke" data-original-title="Green Smoke"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-12" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-12.css" data-toggle="tooltip" data-placement="top" title="Wild Blue Yonder" data-original-title="Wild Blue Yonder"></a>
                            </li>
                            <li>
                                <a href="#" id="myapp-13" data-action="theme-update" data-themesave data-theme="/css/themes/cust-theme-13.css" data-toggle="tooltip" data-placement="top" title="Emerald" data-original-title="Emerald"></a>
                            </li>
                        </ul>
                    </div>
                    <hr class="mb-0 mt-4">
                    <div class="pl-5 pr-3 py-3 bg-faded">
                        <div class="row no-gutters">
                            <div class="col-6 pr-1">
                                <a href="#" class="btn btn-outline-danger fw-500 btn-block" data-action="app-reset">Reset Settings</a>
                            </div>
                            <div class="col-6 pl-1">
                                <a href="#" class="btn btn-danger fw-500 btn-block" data-action="factory-reset">Factory Reset</a>
                            </div>
                        </div>
                    </div>
                </div> <span id="saving"></span>
            </div>
        </div>
    </div>
</div>
<!-- END Page Settings -->

@if (auth()->check() && auth()->user()->exists)
    <script src="/js/vendors.bundle.js"></script>
    <script src="/js/app.bundle.js"></script>

    <script type="text/javascript">
        var dtAdvance;

        const select2Account = {
            dropdownParent: $(document).find('select[name="account"]').parent(),
            placeholder: 'Search by name or number',
            minimumInputLength: 2,
            ajax: {
                url: @json(route('api.accounts.index')),
                dataType: 'json',
                type: 'GET',
                data: function (params) {
                    params.query = {
                        api_token: $(document).find('meta[name="api-token"]').attr('content'),
                        query : params.term

                    }

                    return params.query;
                },
                processResults: function (response) {
                    response.results = response.data;

                    response.results = $.map(response.results, function(value, key) {
                        value.id = String(value.number).concat(' ').concat(value.cif).concat(' ').concat(value.name);
                        value.text = String(value.number).concat(' ').concat(value.name);

                        return value;
                    })

                    return response;
                }
            }
        };

        const select2Dealer = {
            dropdownParent: $('select[name="dealer-id"]').parent(),
            placeholder: 'Search by name',
            minimumInputLength: 2,
            ajax: {
                url: @json(route('api.users.index')),
                dataType: 'json',
                type: 'GET',
                data: function (params) {
                    params.query = {
                        api_token: $(document).find('meta[name="api-token"]').attr('content'),
                        @if (request()->route()->named('interbank-dealing.index'))
                        is_interbank_deal: true,
                        @endif
                        query : params.term
                    }

                    return params.query;
                },
                processResults: function (response) {
                    response.results = $.map(response.data, function(value, key) {
                        value.text = value.full_name;

                        if (!value.text.trim()) {
                            value.text = value.email;
                        }

                        return value;
                    })

                    return response;
                }
            }
        };

    </script>

@yield('javascript')

    <script type="text/javascript">
        $(document).ready(function()
        {
            var nav = [
@can('viewAny', 'App\SalesDeal')
                'nav.dashboard',
@endcan
@can('viewAny', 'App\InterbankDeal')
                'nav.interbank_dealing',
@endcan
@can('viewAny', 'App\NopAdjustment')
                'nav.interbank_nop',
@endcan
@can('viewAny', 'App\SalesDeal')
                'nav.sales_fx',
@endcan
@can('viewAny', 'App\SpecialRateDeal')
                'nav.sales_special_rate_deal',
@endcan
@can('viewAny', 'App\Cancellation')
                'nav.sales_cancellations',
                'nav.rejections',
@endcan
@can('viewAny', 'App\SalesDeal')
                'nav.sales_blotter',
@endcan
@can('update', new App\SalesDeal)
                'nav.sales_top_ten_obox',
@endcan
@can('create', 'App\Market')
                'nav.markets',
@endcan
@can('viewAny', 'App\Currency')
                'nav.currencies',
@endcan
@can('viewAny', 'App\ClosingRate')
                'nav.closing_rates',
@endcan
@can('viewAny', 'App\Account')
                'nav.accounts',
@endif
@can('create', 'App\News')
                'nav.news',
@endcan
@can('viewAny', 'App\User')
                'nav.users',
@endcan
@can('viewAny', 'App\Threshold')
                'nav.settings_threshold',
@endcan
@can('viewAny', 'App\Role')
                'nav.settings_dealer_limits',
@endcan
            ];

            $('#js-nav-menu span[data-i18n]').each(function(key, element) {
                $(element).closest('li').addClass('collapse');

                if ($.inArray(element.getAttribute('data-i18n'), nav) >= 0) {
                    $(element).closest('li').addClass('show');

                    if ($(element).has('ul')) {
                        $(element).closest('ul').closest('li').addClass('show');
                    }

                }
            })

            const profileImage = new Image;

            profileImage.onload = function() {
                $(document).find('img.profile-image').each( function(key, element) {
                    element.src = profileImage.src;
                })
            }

            profileImage.onerror = function() {
                profileImage.onerror = null;

                profileImage.src = ('/img/avatars/avatar-')
                    .concat(@json(strtolower(trim(auth()->user()->gender))))
                    .concat('.png');

                profileImage.onload = function() {
                    $(document).find('img.profile-image').each( function(key, element) {
                        element.src = profileImage.src;
                    })
                }
            }

            profileImage.src = @json(auth()->user()->photo);

            if ($('table').is('#dt-basic, .dt-basic')) {
                $('table#dt-basic, table.dt-basic').DataTable({
                    responsive: true,
                    fixedHeader: {
                        headerOffset: $(document.body).hasClass('header-function-fixed') ? $('header.page-header').outerHeight() : 0
                    },
                    paging: false,
                    bInfo: false,
@if (request()->route()->named('settings-threshold.index'))
                    dom: "<'row mb-3'" +
                        "<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f>" +
                        "<'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>" +
                        ">" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        {
                            text: '<span class="fal fa-edit mr-1"></span>Update',
                            titleAttr: 'Update Threshold',
                            className: 'btn btn-outline-primary waves-effect waves-themed mr-1',
                            action: function ( e, dt, node, config ) {
                                $('.modal').has('#sismontavar-form').modal();
                            }
                        }
                    ],
@else
                    select: true,
@endif
                    searching: false,
                    ordering: false,
                    language: {
                        emptyTable: 'No data available'
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (this.fnSettings().oInit.select) {
                            $(row).addClass('pointer');
                        }
                    },
                    initComplete: function(settings, json) {
                        settings.oInstance.api().columns().header().to$().addClass('text-center');

                        if (
                            $(settings.oInstance.api().table().container().closest('.panel'))
                                .is('#panel-currency-index, #panel-cross-currency-index, #panel-special-currency-index')
                        ) {
                            settings.oInstance.api().column(-4).visible(false);

                            if (!settings.oInstance.api().column(0).init().select) {
                                settings.oInstance.api().column(0).visible(false);
                            }

                        } else if ($(settings.oInstance.api().table().container().closest('.panel')).is('#panel-role-index')) {
                            settings.oInstance.api().columns([1, 2, 4]).visible(false);

                        } else if ($(settings.oInstance.api().table().container().closest('.panel')).is('#panel-branch-index')) {
                            settings.oInstance.api().column(3).visible(false);
                        }

@if (request()->route()->named('settings-threshold.index'))
                        if (settings.oInstance.api().button().container().is($('.dt-buttons').eq(0))) {
                            settings.oInstance.api().button().container().remove();
                        }
@endif
                    }

                }).on('select', function(e, dt, type, indexes) {
                    var panel = $(e.currentTarget).closest('.panel');
                    var modal = $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)');

                    if (modal.length > 1) {
                        modal = modal.eq(dt.table().node().id.substring(dt.table().node().id.length - 1));
                    }

                    if (panel.is('#panel-role-index, #panel-branch-index')) {
                        var dataTable = dt.row(indexes).data();
                        modal.modal('show');

                        modal.find('form').find('input')
                            .not('[name="_method"], [name="_token"]')
                            .each( function(index, element) {
                                if (element.name) {
                                    element.value = dataTable[index];
                                } else {
                                    element.value = dataTable[index - 1] ? parseFloat(dataTable[index - 1]).toLocaleString('en-US') : '';
                                }
                            })

                        if (panel.is('#panel-role-index')) {
                            if (dataTable[1]) {
                                $('[name="commercial-bank-limit"]').closest('.form-group').show();
                            } else {
                                $('[name="commercial-bank-limit"]').closest('.form-group').hide();
                            }
                        }
                    }

                    modal.on('hidden.bs.modal', function(e) {
                        dt.row(indexes).deselect().draw();
                    })
                })



            } else if ($('table').is('#dt-advance, .dt-advance')) {
                dtAdvance.on('select', function(e, dt, type, indexes) {
                    var panel = $(e.target).closest('.panel');
                    var url = window.location.pathname + '/';
                    var dataTable = dt.rows(indexes).data();

                    if (type === 'cell') {
                        $(e.target).DataTable().cell(indexes).deselect();

                        if ((indexes[0].column === 0) && ($(dt.cell(indexes).node().parentElement).is('.selected'))) {
                            $(e.target).DataTable().row(indexes[0].row).deselect();

                        } else if ($(dt.cell(indexes).node().parentElement).is(':not(.selected)')) {
                            if ((indexes[0].column === 0) || (dt.rows('.selected').count() === 0)) {
                                $(e.target).DataTable().row(indexes[0].row).select();

                                if ((indexes[0].column > 0) && (dt.rows('.selected').count() === 1)) {
                                    url += dt.row(indexes[0].row).data().id + '/edit';
                                }
                            }
                        }

                    } else if ($(e.target).find('tbody').find('tr').children().first().is('.select-checkbox')) {
                        dt.button(1).node().collapse('show');
                        dt.column(0).header().childNodes[0].replaceWith(document.createElement('i'));
                        dt.column(0).header().childNodes[0].classList.add('fal', 'fa-times');

                    } else {
                        dataTable = dataTable[0];

                        if (panel.is('#panel-nop-adjustment-index, #panel-closing-rate-index')) {
                            dataTable.modal = $(document).find('.modal:not(.js-modal-settings):not(.modal-alert)');

                            if (dataTable.modal.length > 1) {
                                dataTable.modal = dataTable.modal.eq(1);
                            }

                            dataTable.modal.modal();
                        } else {
                            url += dataTable.id;
                        }
                    }

                    if (url !== window.location.pathname + '/') {
                        window.location.href = url;
                    }
                })

                dtAdvance.on('deselect', function(e, dt, type, indexes) {
                    if (type === 'row' && $(e.target).find('tbody').find('tr').children().first().is('.select-checkbox')) {
                        if ((indexes.length === dt.rows().count()) || (dt.rows('.selected').count() < 1)) {
                            dt.button(1).node().collapse('hide');
                            dt.column(0).header().childNodes[0].replaceWith(document.createElement('i'));
                            dt.column(0).header().childNodes[0].classList.add('fal', 'fa-check');

                        } else {
                            dt.button(1).node().collapse('show');
                            dt.column(0).header().childNodes[0].replaceWith(document.createElement('i'));
                            dt.column(0).header().childNodes[0].classList.add('fal', 'fa-times');
                        }
                    }
                })
            }

            if ($(document).find('select').is('[name="account"]')) {
                $(document).find('select[name="account"]').select2(select2Account)
                    .on('select2:select', function(e) {
                        if (!e.params.data.cif && $(e.currentTarget).closest('form').find('[name="account-cif"]').get(0).dataset.accountCif) {
                            e.params.data.cif = $(e.currentTarget).closest('form').find('[name="account-cif"]').get(0).dataset.accountCif;
                        }

                        $(e.currentTarget).closest('form').find('[name="account-number"]').val(e.params.data.number);
                        $(e.currentTarget).closest('form').find('[name="account-cif"]').val(e.params.data.cif);
                        $(e.currentTarget).closest('form').find('[name="account-name"]').val(e.params.data.name);

                        e.currentTarget.threshold = $(e.currentTarget).closest('form').find('[name=threshold]').val();
                        e.currentTarget.base_currency_code = $(e.currentTarget).closest('form').find('[name="base-currency-code"]').val();
                        e.currentTarget.counter_currency_code = $(e.currentTarget).closest('form').find('[name="counter-currency-code"]').val();
                        e.currentTarget.world_currency_code = $(e.currentTarget).closest('form').find('[name="world-currency-code"]').val();
                        e.currentTarget.buy_sell = $(e.currentTarget).closest('form').find('[name="buy-sell"]').val().trim().toLowerCase();
                        e.currentTarget.base_currency_closing_rate = $(e.currentTarget).closest('form').find('[name="base-currency-closing-rate"]').val();
                        e.currentTarget.world_currency_closing_rate = $(e.currentTarget).closest('form').find('[name="world-currency-closing-rate"]').val();
                        e.currentTarget.amount = $(e.currentTarget).closest('form').find('[name="amount"]');
                        e.currentTarget.account_cif = $(e.currentTarget).closest('form').find('[name="account-cif"]');

                        if (!e.currentTarget.counter_currency_code && (e.currentTarget.buy_sell !== 'bank buy')) {
                            e.params.data.final_monthly_usd_equivalent = 0;

                            if (e.params._type === 'select') {
                                if (!e.params.data.monthly_usd_equivalent) {
                                    e.params.data.monthly_usd_equivalent = 0;
                                }

                                if ($(document).find('label[for=monthly-usd-equivalent]').parent().children().get(1).dataset.monthlyUsdEquivalent) {
                                    $(document).find('label[for=monthly-usd-equivalent]').parent().children().get(1).dataset.monthlyUsdEquivalent = (
                                        e.params.data.monthly_usd_equivalent
                                    )
                                }

                            } else {
                                e.params.data.monthly_usd_equivalent = 0;
                                
                                if ($(document).find('label[for=monthly-usd-equivalent]').parent().children().get(1).dataset.monthlyUsdEquivalent) {
                                    e.params.data.monthly_usd_equivalent += (
                                        parseFloat(
                                            $(document).find('label[for=monthly-usd-equivalent]').parent().children().get(1)
                                            .dataset
                                            .monthlyUsdEquivalent
                                        )
                                    );
                                }
                            }

                            if (e.currentTarget.amount.val()) {
                                e.params.data.final_monthly_usd_equivalent += (
                                    e.params.data.monthly_usd_equivalent + (
                                        (e.currentTarget.base_currency_code === e.currentTarget.world_currency_code) ? (
                                            Math.abs(parseFloat(e.currentTarget.amount.val()))
                                        ) : (
                                            Math.abs(
                                                parseFloat(e.currentTarget.base_currency_closing_rate) * (
                                                    parseFloat(e.currentTarget.amount.val()) / parseFloat(
                                                        e.currentTarget.world_currency_closing_rate
                                                    )
                                                )
                                            )
                                        )
                                    )
                                );
                            }

                            if (
                                e.currentTarget.account_cif.get(0).dataset.accountCif &&
                                (parseInt(e.currentTarget.account_cif.get(0).dataset.accountCif) === parseInt(e.params.data.cif))
                            )
                            {
                                e.params.data.final_monthly_usd_equivalent -= (
                                    (e.currentTarget.base_currency_code === e.currentTarget.world_currency_code) ? (
                                        Math.abs(parseFloat(e.currentTarget.amount.parent().children().get(1).dataset.amount))
                                    ) : (
                                        Math.abs(
                                            parseFloat(e.currentTarget.base_currency_closing_rate) * (
                                                parseFloat(e.currentTarget.amount.parent().children().get(1).dataset.amount) / (
                                                    parseFloat(e.currentTarget.world_currency_closing_rate)
                                                )
                                            )
                                        )
                                    )
                                );
                            }

                            $(document).find('label[for=monthly-usd-equivalent]').next().val(
                                e.params.data.final_monthly_usd_equivalent.toLocaleString('en-US')
                            );

                            if (e.params.data.final_monthly_usd_equivalent > parseFloat(e.currentTarget.threshold)) {
                                $(e.currentTarget).next().tooltip('dispose');

                                $(e.currentTarget).next().tooltip({
                                    trigger: 'manual',
                                    placement: 'bottom',
                                    title: ('Warning! The monthly USD equivalent over the threshold. The monthly USD equivalent: $').concat(
                                        e.params.data.final_monthly_usd_equivalent.toLocaleString('en-US')
                                    ),

                                    template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner bg-warning-500 mw-100"></div></div>'
                                }).tooltip('show');

                            } else {
                                $(e.currentTarget).next().tooltip('hide');
                            }

                        }

                    });
            }

            if ($(document).find('select').is('[name="dealer-id"]')) {
                $(document).find('select[name="dealer-id"]').select2(select2Dealer)
                    .on('select2:select', function (e) {
                        if ($(e.currentTarget).closest('.modal').find('[name="sales-limit"]').length) {
                            $(e.currentTarget).closest('.modal').find('[name="sales-limit"]').val(
                                e.params.data.sales_limit
                            );

                        } else if ($(e.currentTarget).closest('.modal').find('[name="commercial-bank-limit"]').length) {
                            $(e.currentTarget).closest('.modal').find('[name="commercial-bank-limit"]').val(
                                e.params.data.commercial_bank_limit
                            );
                        }

                        $(e.currentTarget).closest('.modal').find('[name="amount"]').next().trigger('input');
                    });
            }

            if ($(document).find('input[type="hidden"][name]').next().is('input[type="text"]')) {
                $(document).find('input[type="hidden"][name]').not('[name="interoffice-rate"], [name="customer-rate"], [name="amount"]')
                    .siblings('input[type="text"]').inputmask('currency', {
                    rightAlign: false,
                    prefix: ''
                })

                $(document).find('input[type="hidden"]').filter('[name="amount"]')
                    .siblings('input[type="text"]').inputmask('currency', {
                    rightAlign: false,
                    prefix: '',
                    allowPlus: true,
                    allowMinus: ($(document).has('#panel-interbank-deal-index, #panel-sales-deal-index').length ? (
                            false
                        ) : (
                            true
                        )
                    )
                })

                $(document).has('#panel-interbank-deal-index, #panel-interbank-deal-edit')
                    .find('input[type="hidden"]').filter('[name="interoffice-rate"]')
                    .siblings('input[type="text"]').inputmask('currency', {
                    rightAlign: false,
                    digits: 12,
                    prefix: '',
                    allowPlus: true,
                    allowMinus: false
                })

                $(document.body).not(':has(#panel-interbank-deal-index, #panel-interbank-deal-edit)')
                    .find('input[type="hidden"]').filter('[name="interoffice-rate"], [name="customer-rate"]')
                    .siblings('input[type="text"]').inputmask('currency', {
                    rightAlign: false,
                    digits: 4,
                    prefix: '',
                    allowPlus: true,
                    allowMinus: false
                })

            }

            if ($(document).find('#datepicker, .datepicker').not('div').length) {
                $('#datepicker, .datepicker').not('div').datepicker(
                {
                    todayHighlight: true,
                    clearBtn: true,
                    orientation: "bottom left",
                    templates: controls,
                    format: "yyyy-mm-dd",
                    daysOfWeekDisabled: [0,6],
                    container: (
                        $('#datepicker, .datepicker').closest('.modal').length
                        ? $('#datepicker, .datepicker').closest('.modal')
                        : $('#datepicker, .datepicker').closest(document.body)
                    )

                }).on('show.bs.modal', function(e) {
                    e.stopPropagation(); 
                });
            }

        });

        var controls = {
            leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
            rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
        }

    </script>

    <script type="text/javascript">

        $(document).find('.page-link[aria-label="Previous"]').on('click', function() {
            window.history.back();
        })

        $(document).find('#dt-advance, .dt-advance').find('thead').on('click', 'th.pointer', function(e) {
            var checkSelected = true;
            $(e.target).closest('#dt-advance, .dt-advance').find('tbody tr').each( function(index, element) {
                if (!$(element).hasClass('selected')) {
                    checkSelected = false;
                }
            })

            if (checkSelected === true) {
                $(e.target).closest('#dt-advance, .dt-advance').DataTable().rows().deselect();

            } else {
                $(e.target).closest('#dt-advance, .dt-advance').DataTable().rows().select();
            }
        })

        $(document).find('main form button[data-toggle="modal"]').on('click', function(e) {
            if (!e.target.closest('form').checkValidity()) {
                e.target.closest('form').reportValidity();
                e.stopPropagation();
            }
        })

        $(document).find('#modal-alert').find('button').not('[data-dismiss="modal"]').on('click', function(e) {
            $(e.target).closest('.modal.modal-alert').modal('toggle');

            var id = $(e.target).closest('.modal.modal-alert').attr('id');
            var button = $('button[data-target="#' + id + '"]');

            if (button.length > 1) {
                button = button.filter('.show');
            }

            button.closest('form').submit();

            $(e.target).closest('.modal.modal-alert').on('hidden.bs.modal', function(e) {
                if (button.closest('form').attr('target') === '_blank') {
                    window.location.reload();
                }
            })
        })

        $(document).has('#panel-interbank-deal-index, #panel-interbank-deal-edit')
            .on('input', '[im-insert]', function(e) {
                e.currentTarget.previousElementSibling.value = e.currentTarget.value.replace(/\,/g, '');
                $(e.currentTarget.previousElementSibling).trigger('input');

                if ($(e.currentTarget).closest('form').find('label[for=counter-currency-rate]').length) {
                    $(e.currentTarget).closest('form').find('label[for=counter-currency-rate]').next().val(
                        (
                            $(e.currentTarget).closest('form').find('input[name="base-currency-rate"]').val() / (
                                $(e.currentTarget).closest('form').find('input[name="interoffice-rate"]').val() ? (
                                    $(e.currentTarget).closest('form').find('input[name="interoffice-rate"]').val()
                                ) : (
                                    1
                                )
                            )
                        )
                            .toLocaleString('en-US')
                    );
                }

                $(e.currentTarget).closest('form').find('label[for=counter-amount]').next().val(
                    (
                        (
                            $(e.currentTarget).closest('form').find('input[name="customer-rate"]').length ? (
                                $(e.currentTarget).closest('form').find('input[name="customer-rate"]')
                            ) : (
                                $(e.currentTarget).closest('form').find('input[name="interoffice-rate"]')
                            )
                        )
                            .val() * (
                            $(e.currentTarget).closest('form').find('input[name="amount"]').val()
                        )
                    )
                        .toLocaleString('en-US')
                );

                e.currentTarget.commercial_bank_limit = parseFloat($('[name="commercial-bank-limit"]').val());
                e.currentTarget.amount = parseFloat($('[name="amount"]').val());
                e.currentTarget.base_currency_closing_rate = parseFloat($('[name="base-currency-closing-rate"]').val());
                e.currentTarget.world_currency_closing_rate = parseFloat($('[name="world-currency-closing-rate"]').val());
                e.currentTarget.world_currency_code = $('[name="world-currency-code"]').val();

                e.currentTarget.usd_equivalent = (
                    ($('[name="base-currency-code"]').val() === e.currentTarget.world_currency_code) ? (
                        e.currentTarget.amount
                    ) : (
                        Math.abs(
                            e.currentTarget.base_currency_closing_rate * (e.currentTarget.amount / e.currentTarget.world_currency_closing_rate)
                        )
                    ));

                if (e.currentTarget.commercial_bank_limit && (parseFloat(e.currentTarget.usd_equivalent) > e.currentTarget.commercial_bank_limit)) {
                    $('[name="amount"]').next().tooltip({
                        trigger: 'manual',
                        placement: 'bottom',
                        title: 'Alert! The base amount over your limit.',
                        template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner bg-danger-500 mw-100"></div></div>'
                    }).tooltip('show');

                    $(e.currentTarget).closest('form').find('button').filter('[type=submit], [data-target="#modal-alert"]').prop('disabled', true);

                } else {
                    $('[name="amount"]').next().tooltip('hide');
                    $(e.currentTarget).closest('form').find('button').filter('[type=submit], [data-target="#modal-alert"]').prop('disabled', false);
                }
            })

        $(document).has('#panel-sales-deal-index, #panel-sales-deal-edit')
            .on('input', '[im-insert]', function(e) {
                e.currentTarget.previousElementSibling.value = e.currentTarget.value.replace(/\,/g, '');
                $(e.currentTarget.previousElementSibling).trigger('input');

                $('label[for=counter-amount]').next().val(
                    (
                        (
                            $(e.currentTarget).closest('form').find('input[name="customer-rate"]').length ? (
                                $(e.currentTarget).closest('form').find('input[name="customer-rate"]')
                            ) : (
                                $('input[name="interoffice-rate"]')
                            )
                        ).val() *
                        $(e.currentTarget).closest('form').find('input[name="amount"]').val())
                        .toLocaleString('en-US')
                );

                if (
                    $(e.currentTarget).closest('form').find('[name="account"]').select2('data').length &&
                    $(e.currentTarget).prev().is('[name="amount"]')
                )
                {
                    $(e.currentTarget).closest('form').find('[name="account"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: $(e.currentTarget).closest('form').find('[name="account"]').first().select2('data').find(data => data.id)
                        }
                    })
                }

                e.currentTarget.sales_limit = $(e.currentTarget).closest('form').find('[name="sales-limit"]').val();
                e.currentTarget.amount = $(e.currentTarget).closest('form').find('[name="amount"]').val();
                e.currentTarget.base_currency_closing_rate = $(e.currentTarget).closest('form').find('[name="base-currency-closing-rate"]').val();
                e.currentTarget.world_currency_closing_rate = $(e.currentTarget).closest('form').find('[name="world-currency-closing-rate"]').val();
                e.currentTarget.base_currency_code = $(e.currentTarget).closest('form').find('[name="base-currency-code"]').val();
                e.currentTarget.world_currency_code = $(e.currentTarget).closest('form').find('[name="world-currency-code"]').val();

                e.currentTarget.usd_equivalent = (
                    e.currentTarget.amount ? (
                        (e.currentTarget.base_currency_code === e.currentTarget.world_currency_code) ? (
                            parseFloat(e.currentTarget.amount)
                        ) : (
                            Math.abs(
                                parseFloat(e.currentTarget.base_currency_closing_rate) * (
                                    parseFloat(e.currentTarget.amount) / parseFloat(e.currentTarget.world_currency_closing_rate)
                                )
                            )
                        )
                    ) : (0)
                );

                if (e.currentTarget.sales_limit && (parseFloat(e.currentTarget.usd_equivalent) > parseFloat(e.currentTarget.sales_limit))) {
                    $(e.currentTarget).closest('form').find('[name="amount"]').next().tooltip('dispose');

                    $(e.currentTarget).closest('form').find('[name="amount"]').next().tooltip({
                        trigger: 'manual',
                        placement: 'bottom',
                        title: 'Alert! The base amount over your limit.',
                        template: String('<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner bg-')
                            .concat(
                                $(e.currentTarget).closest(document.body).has('.panel-sales-deal .col-sm-4').length ? (
                                    'danger'
                                ) : (
                                    'warning'
                                )
                            )
                            .concat('-500 mw-100"></div></div>')
                    }).tooltip('show');

                } else {
                    $(e.currentTarget).closest('form').find('[name="amount"]').next().tooltip('hide');
                }

            })

        $(document.body).not(':has(#panel-sales-deal-index, #panel-sales-deal-edit, #panel-interbank-deal-index, #panel-interbank-deal-edit)')
            .on('input', '[im-insert]', function(e) {
                e.target.previousElementSibling.value = e.target.value.replace(/\,/g, '');
                $(e.target.previousElementSibling).trigger('input');
            })

        $(document).find('[name="tod-tom-spot-forward"]').on('change', function(e) {
            if (e.currentTarget.value && e.currentTarget.value.toLowerCase() !== 'tod') {
                $(e.currentTarget).closest('form').find('[name="settlement-date"]').prop('required', true);
                $(e.currentTarget).closest('form').find('[name="settlement-date"]').closest('.collapse').collapse('show');

                if (e.currentTarget.value.toLowerCase() === 'tom') {
                    $('#datepicker').datepicker(
                        'setStartDate',
                        moment($(e.currentTarget).closest('form').find('[name="created-at"]').val()).add(1, 'days').format('YYYY-MM-DD')
                    );

                } else if (e.currentTarget.value.toLowerCase() === 'spot') {
                    $('#datepicker').datepicker(
                        'setStartDate',
                        moment($(e.currentTarget).closest('form').find('[name="created-at"]').val()).add(2, 'days').format('YYYY-MM-DD')
                    );

                } else if (e.currentTarget.value.toLowerCase() === 'forward') {
                    $('#datepicker').datepicker(
                        'setStartDate',
                        moment($(e.currentTarget).closest('form').find('[name="created-at"]').val()).add(3, 'days').format('YYYY-MM-DD')
                    );
                }

            } else {
                $(e.currentTarget).closest('form').find('[name="settlement-date"]').prop('required', false);
                $(e.currentTarget).closest('form').find('[name="settlement-date"]').closest('.collapse').collapse('hide');
            }
        })

        $(document).find('textarea[name="description"]').on('input', function(e) {
            if ($(e.target).val().length > parseInt($(e.target).attr('maxLength'))) {
                $(e.target).val($(e.target).val().substr(0, maxLength));
            }
        })

        $(document).find('select[name="region"]').on('change', function(e) {
            if(e.currentTarget.options[e.currentTarget.selectedIndex].value) {
                $(e.currentTarget).closest('form').find('[name="branch-code"]').closest('.form-group').collapse('show');
                $(e.currentTarget).closest('form').find('[name="branch-code"] option').not(':first').remove();
                $(e.currentTarget).closest('form').find('[name="branch-code"] option').filter(':first').text('Loading...');
                $(e.currentTarget).closest('form').find('[name="branch-code"]').trigger('change');
                $(e.currentTarget).closest('form').find('[name="branch-code"]').prop('required', true);
            }

            $.ajax({
                method: 'GET',
                url: @json(route('api.branches.index')),
                data: {
                    api_token: $(document).find('meta[name="api-token"]').attr('content'),
                    region: $(e.target).val()
                }
            }).done( function(response) {
                $(e.currentTarget).closest('form').find('[name="branch-code"] option').filter(':first').text('Choose');

                $.each(response.data, function(index, value) {
                    var elem = document.createElement('option');
                    elem.value = value.code;

                    if (elem.value === document.querySelector('select[name="branch-code"]').dataset.branchCode) {
                        elem.selected = true;
                    }

                    elem.innerHTML = value.name;
                    document.querySelector('select[name="branch-code"]').appendChild(elem);
                })
            });

            if ($(e.currentTarget).closest('form').find('[name="role-id"]').length) {
                $(e.currentTarget).closest('form').find('[name="role-id"]').children(':selected').prop('selected', false);

                switch (e.currentTarget.value) {
                    case ('kantor pusat').toUpperCase():
                        $(e.currentTarget).closest('form').find('[name="role-id"]').closest('.form-group').collapse('show');
                        $(e.currentTarget).closest('form').find('[name="role-id"]').prop('required', true);
                        break;

                    case '':
                        $(e.currentTarget).closest('form').find('[name="role-id"]').closest('.form-group').collapse('show');
                        $(e.currentTarget).closest('form').find('[name="role-id"]').prop('required', true);
                        break;

                    default:
                        $(e.currentTarget).closest('form').find('[name="role-id"]').closest('.form-group').collapse('hide');
                        $(e.currentTarget).closest('form').find('[name="role-id"]').prop('required', false);
                }
            }
        })

        $(document).find('select[name="branch-code"]').on('change', function(e) {
            if ($(e.currentTarget).closest('form').find('input[name="branch-name"]').length) {
                $(e.currentTarget).closest('form').find('input[name="branch-name"]')
                    .val(
                        e.currentTarget.value ? (
                            e.currentTarget.options[e.currentTarget.selectedIndex].text
                        ) : (
                            ''
                        )
                    );
            }
        })

        $(document).find('select[name="role-id"]').on('change', function(e) {
            if (
                $.inArray(
                    e.currentTarget.options[e.currentTarget.selectedIndex].text.trim().toLowerCase(), ['administrator', 'it security', 'it development']
                ) >= 0
            ) {
                $(e.currentTarget).closest('form').find('[name="region"]').children(':selected').prop('selected', false);
                $(e.currentTarget).closest('form').find('[name="branch-code"]').children(':selected').prop('selected', false);
                $(e.currentTarget).closest('form').find('[name="branch-code"]').trigger('change');
                $(e.currentTarget).closest('form').find('[name="region"]').closest('.form-group').collapse('hide');
                $(e.currentTarget).closest('form').find('[name="branch-code"]').closest('.form-group').collapse('hide');
                $(e.currentTarget).closest('form').find('[name="branch-code"]').prop('required', false);

            } else {
                $(e.currentTarget).closest('form').find('[name="region"]').closest('.form-group').collapse('show');
                $(e.currentTarget).closest('form').find('[name="branch-code"]').closest('.form-group').collapse('show');
                $(e.currentTarget).closest('form').find('[name="branch-code"]').prop('required', true);
            }

        })

        $(document).find('#modal-alert').on('show.bs.modal', function(e) {
            var title = 'Yes, ';
            var description = '';

            if ($(e.relatedTarget).hasClass('btn-delete')) {
                title += 'delete it!';
                description += "You won't be able to revert this!";

                if ($('.collapse.btn-delete').not(e.relatedTarget).length > 0) {
                    $('.collapse.btn-delete').not(e.relatedTarget).collapse('hide');
                    $('.dt-advance').not($(e.relatedTarget).closest('.panel').find('table')).DataTable().rows().deselect();
                }

            } else {
                title += 'submit it!';
            }

            if ($(e.relatedTarget).closest('.panel').is('#panel-authorize')) {
                $(e.relatedTarget).closest('.panel').find('button[data-target]').not($(e.relatedTarget)).collapse('hide');
            }

            $(e.target).find('button').not('[data-dismiss="modal"]').text(title);
            $(e.target).find('.modal-body').text(description);
        })

        $(document).find('#modal-alert').on('hide.bs.modal', function(e) {
            if ($('button[data-target="#' + e.target.id + '"]').closest('.panel').is('#panel-authorize')) {
                $('button[data-target="#' + e.target.id + '"]').collapse('show');
            }
        })

    </script>
@endif

</body>
</html>
