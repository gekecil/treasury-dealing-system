@extends('layouts.master')

@section('title', $salesDeal->account->name.' - Sales')

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
@if (request()->route()->named('sales-fx.show', ['salesDeal' => $salesDeal->id]))
                            <li class="breadcrumb-item"><a href="{{ route('sales-fx.index') }}">FX</a></li>
@elseif (request()->route()->named('sales-special-rate-deal.show', ['salesDeal' => $salesDeal->id]))
                            <li class="breadcrumb-item"><a href="{{ route('sales-special-rate-deal.index') }}">Request for FX Deal</a></li>
@elseif (request()->route()->named('sales-blotter.show', ['salesDeal' => $salesDeal->id]))
                            <li class="breadcrumb-item"><a href="{{ route('sales-blotter.index') }}">Blotter</a></li>
@endif
                            <li class="breadcrumb-item active">{{ $salesDeal->account->name }}</li>
                            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                        </ol>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 d-flex justify-content-start">
                                <div class="subheader">
                                    <h1 class="subheader-title">
                                        <i class='subheader-icon fal fa-edit'></i> {{ $salesDeal->account->name }}
                                    </h1>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 d-flex justify-content-end">
@if ($sismontavarDeal)
                                <a href="{{ route('sismontavar-deals.show', ['sismontavarDeal' => $sismontavarDeal->transaction_id]) }}">
                                    <button class="btn btn-primary mr-1" type="button" title="SISMONTAVAR Data">
                                        <span class="fal fa-th-list mr-1"></span>
                                        SISMONTAVAR Data
                                    </button>
                                </a>
@endif
@if (request()->route()->named('sales-fx.show', ['salesDeal' => $salesDeal->id]))
                                <a href="{{ route('sales-fx.edit', ['salesDeal' => $salesDeal->id]) }}">
@elseif (request()->route()->named('sales-special-rate-deal.show', ['salesDeal' => $salesDeal->id]))
                                <a href="{{ route('sales-special-rate-deal.edit', ['salesDeal' => $salesDeal->id]) }}">
@elseif (request()->route()->named('sales-blotter.show', ['salesDeal' => $salesDeal->id]))
                                <a href="{{ route('sales-blotter.edit', ['salesDeal' => $salesDeal->id]) }}">
@endif
                                    <button class="btn btn-secondary" type="button" title="Edit Sales Deal">
                                        <span class="fal fa-edit mr-1"></span>
                                        Edit
                                    </button>
                                </a>
@if (auth()->user()->can('create', 'App\Cancellation'))
                                <form action="{{ route('sales-cancellations.store') }}" method="post">
                                    @csrf
                                    
                                    <input type="hidden" name="deal-id" value="{{ $salesDeal->id }}">
@if ($salesDeal->specialRateDeal && !$salesDeal->specialRateDeal->confirmed)
                                    <input type="hidden" name="is_rejection" value="1">
                                    <button type="submit" class="btn btn-danger ml-2" title="Reject Sales Deal">
                                        <span class="fal fa-times-square mr-1"></span>
                                        Reject Transaction
                                    </button>
@else
                                    <input type="hidden" name="is_rejection" value="0">
                                    <button type="submit" class="btn btn-danger ml-2" title="Cancel Sales Deal">
                                        <span class="fal fa-times-square mr-1"></span>
                                        Cancel Transaction
                                    </button>
@endif
                                </form>
@endif
                            </div>
                        </div>
                        <div id="alert-dismissible" class="panel-container show">
                            <div class="panel-content">
@if ($salesDeal->specialRateDeal && !$salesDeal->specialRateDeal->confirmed)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                                    </button>
                                    <strong>Alert!</strong> This request for FX deal has not been authorized.
                                </div>
@endif
@if ($salesDeal->modificationUpdated && !$salesDeal->modificationUpdated->confirmed)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                                    </button>
                                    <strong>Alert!</strong> This dealing was updated and has not been authorized.
                                </div>
@endif
@if ($salesDeal->salesDealFile && !$salesDeal->salesDealFile->confirmed)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                                    </button>
                                    <strong>Alert!</strong> The document underlying has not been authorized.
                                </div>
@endif
                            </div>
                        </div>
                        <div class="row">
@if ($salesDeal->modificationUpdated && !$salesDeal->modificationUpdated->confirmed)
                            <div class="col-6">
                                <div id="panel-sales-deal-created-show" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <div class="form-group">
                                                <label class="form-label" for="account">
                                                    Account
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->account->number.
                                                    ' '.
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->account->name
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="cif">
                                                    CIF
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->account->cif
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="region">
                                                    Region
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->branch->region
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="branch">
                                                    Branch
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->branch->name
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="currency">
                                                    Currency Pairs
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    (
                                                        $salesDeal->modificationUpdated->salesDealCreated()->withoutGlobalScopes()->first()
                                                        ->currencyPair->baseCurrency->secondary_code ?: (
                                                            $salesDeal->modificationUpdated->salesDealCreated()->withoutGlobalScopes()->first()
                                                            ->currencyPair->baseCurrency->primary_code
                                                        )
                                                    ).(
                                                        '/'
                                                    ).(
                                                        $salesDeal->modificationUpdated->salesDealCreated()->withoutGlobalScopes()->first()
                                                        ->currencyPair->counterCurrency()
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
                                                <input type="text" class="form-control" value="Bank {{
                                                    ucfirst(
                                                        $salesDeal->modificationUpdated->salesDealCreated()
                                                        ->withoutGlobalScopes()->first()->buyOrSell->name
                                                    )
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="interoffice-rate">
                                                    Interoffice Rate
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    number_format(
                                                        $salesDeal->modificationUpdated->salesDealCreated()
                                                        ->withoutGlobalScopes()->first()->interoffice_rate,
                                                        4
                                                    )
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="customer-rate">
                                                    Customer Rate
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    number_format(
                                                        $salesDeal->modificationUpdated->salesDealCreated()
                                                        ->withoutGlobalScopes()->first()->customer_rate,
                                                        4
                                                    )
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="base-amount">
                                                    Base Amount
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    number_format(
                                                        $salesDeal->modificationUpdated->salesDealCreated()
                                                        ->withoutGlobalScopes()->first()->amount,
                                                        2
                                                    )
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="counter-amount">
                                                    Counter Amount
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    number_format(
                                                        (
                                                            $salesDeal->modificationUpdated->salesDealCreated()
                                                            ->withoutGlobalScopes()->first()->customer_rate *
                                                            $salesDeal->modificationUpdated->salesDealCreated()
                                                            ->withoutGlobalScopes()->first()->amount
                                                        ),
                                                        4
                                                    )
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="tod-tom-spot-forward">
                                                    TOD/TOM/Spot/Forward
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    ucfirst(
                                                        $salesDeal->modificationUpdated->salesDealCreated()
                                                        ->withoutGlobalScopes()->first()->todOrTomOrSpotOrForward->name
                                                    )
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="TT-BN">
                                                    TT/BN
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->ttOrBn->name
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="lhbu-remarks">
                                                    LHBU Remarks
                                                </label>
                                                <input type="text" class="form-control mb-2" title="Kode Tujuan" value="{{
                                                    substr(
                                                        '0'.(
                                                            (string) $salesDeal->modificationUpdated->salesDealCreated()
                                                                ->withoutGlobalScopes()->first()
                                                                ->lhbuRemarksCode->name_id
                                                        ),
                                                        -2
                                                    )
                                                    .' '
                                                    .ucfirst(
                                                        $salesDeal->modificationUpdated->salesDealCreated()
                                                        ->withoutGlobalScopes()->first()
                                                        ->lhbuRemarksCode->name
                                                    )
                                                }}" readonly>
                                                <input type="text" class="form-control" title="Jenis Dokumen" value="{{
                                                    substr(
                                                        '00'.(
                                                            (string) $salesDeal->modificationUpdated->salesDealCreated()
                                                                ->withoutGlobalScopes()->first()
                                                                ->lhbuRemarksKind->name_id
                                                        ),
                                                        -3
                                                    )
                                                    .' '
                                                    .ucfirst(
                                                        $salesDeal->modificationUpdated->salesDealCreated()
                                                        ->withoutGlobalScopes()->first()
                                                        ->lhbuRemarksKind->name
                                                    )
                                                }}" readonly>
@if ($salesDeal->modificationUpdated->salesDealCreated()->withoutGlobalScopes()->first()->otherLhbuRemarksKind)
                                                <textarea title="Keterangan Jenis Dokumen" class="form-control mt-2" rows="5" readonly>{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->otherLhbuRemarksKind()->firstOrNew([], [
                                                        'value' => null
                                                    ])
                                                    ->value
                                                }}</textarea>
@endif
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="dealer">
                                                    Dealer
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->user->full_name
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="created-at">
                                                    Created At
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->salesDealCreated()
                                                    ->withoutGlobalScopes()->first()->created_at->toDayDateTimeString()
                                                }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
@endif
                            <div class="col-12">
                                <div id="panel-sales-deal-show" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <div class="form-group">
                                                <label class="form-label" for="account">
                                                    Account
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->account->number.' '.$salesDeal->account->name
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="cif">
                                                    CIF
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->account->cif
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="region">
                                                    Region
                                                </label>
                                                <input type="text" class="form-control" value="{{ $salesDeal->branch->region }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="branch">
                                                    Branch
                                                </label>
                                                <input type="text" class="form-control" value="{{ $salesDeal->branch->name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="currency">
                                                    Currency Pairs
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    (
                                                        $salesDeal->currencyPair->baseCurrency->secondary_code ?: (
                                                            $salesDeal->currencyPair->baseCurrency->primary_code
                                                        )
                                                    ).(
                                                        '/'
                                                    ).(
                                                        $salesDeal->currencyPair->counterCurrency()
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
                                                <input type="text" class="form-control" value="Bank {{ ucfirst($salesDeal->buyOrSell->name) }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="interoffice-rate">
                                                    Interoffice Rate
                                                </label>
                                                <input type="text" class="form-control" value="{{ number_format($salesDeal->interoffice_rate, 4) }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="customer-rate">
                                                    Customer Rate
                                                </label>
                                                <input type="text" class="form-control" value="{{ number_format($salesDeal->customer_rate, 4) }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="base-amount">
                                                    Base Amount
                                                </label>
                                                <input type="text" class="form-control" value="{{ number_format($salesDeal->amount, 2) }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="counter-amount">
                                                    Counter Amount
                                                </label>
                                                <input type="text" class="form-control" value="{{ number_format(($salesDeal->customer_rate * $salesDeal->amount), 4) }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="tod-tom-spot-forward">
                                                    TOD/TOM/Spot/Forward
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    ucfirst($salesDeal->todOrTomOrSpotOrForward->name)
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="TT-BN">
                                                    TT/BN
                                                </label>
                                                <input type="text" class="form-control" value="{{ $salesDeal->ttOrBn->name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="lhbu-remarks">
                                                    LHBU Remarks
                                                </label>
                                                <input type="text" class="form-control mb-2" title="Kode Tujuan" value="{{
                                                    substr('0'.((string) $salesDeal->lhbuRemarksCode->name_id), -2)
                                                    .' '
                                                    .ucfirst($salesDeal->lhbuRemarksCode->name)
                                                }}" readonly>
                                                <input type="text" class="form-control" title="Jenis Dokumen" value="{{
                                                    substr('00'.((string) $salesDeal->lhbuRemarksKind->name_id), -3)
                                                    .' '
                                                    .ucfirst($salesDeal->lhbuRemarksKind->name)
                                                }}" readonly>
@if ($salesDeal->otherLhbuRemarksKind)
                                                <textarea title="Keterangan Jenis Dokumen" class="form-control mt-2" rows="5" readonly>{{
                                                    $salesDeal->otherLhbuRemarksKind()->firstOrNew([], [
                                                        'value' => null
                                                    ])
                                                    ->value
                                                }}</textarea>
@endif
                                            </div>
@if ($salesDeal->salesDealFile && $salesDeal->salesDealFile->filename)
                                            <div class="form-group">
                                                <label class="form-label" for="document-underlying">
                                                    Document Underlying
                                                </label>
                                                <div class="form-control">
                                                    <a href="{{ route('sales-deal-file.show', ['salesDealFile' => $salesDeal->salesDealFile->id]) }}">
                                                        {{ $salesDeal->salesDealFile->filename }}
                                                    </a>
                                                </div>
                                            </div>
@endif
                                            <div class="form-group">
                                                <label class="form-label" for="dealer">
                                                    Dealer
                                                </label>
                                                <input type="text" class="form-control" value="{{ $salesDeal->user->full_name }}" readonly>
                                            </div>
@if ($salesDeal->modificationUpdated)
                                            <div class="form-group">
                                                <label class="form-label" for="edited-by">
                                                    Edited by
                                                </label>
                                                <input type="text" class="form-control" value="{{ $salesDeal->modificationUpdated->user->full_name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="created-at">
                                                    Created At
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->created_at->toDayDateTimeString()
                                                }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="updated-at">
                                                    Updated At
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->modificationUpdated->created_at->toDayDateTimeString()
                                                }}" readonly>
                                            </div>
@else
                                            <div class="form-group">
                                                <label class="form-label" for="created-at">
                                                    Created At
                                                </label>
                                                <input type="text" class="form-control" value="{{
                                                    $salesDeal->created_at->toDayDateTimeString()
                                                }}" readonly>
                                            </div>
@endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
@if (
    (
        $salesDeal->specialRateDeal && !$salesDeal->specialRateDeal->confirmed && auth()->user()->can('update', $salesDeal->specialRateDeal) && (
            !$salesDeal->salesDealRate || (
                $currencyPair->exists()
            )
        ) && $salesDeal->created_at->isToday()
    ) || (
        $salesDeal->modificationUpdated && !$salesDeal->modificationUpdated->confirmed && (
            auth()->user()->can('update', $salesDeal->modificationUpdated)
        )
    ) || (
        $salesDeal->salesDealFile && !$salesDeal->salesDealFile->confirmed && auth()->user()->can('update', $salesDeal->salesDealFile)
    )
)
                        <div class="row">
                            <div class="col-12">
                                <div id="panel-sales-deal-confirmation" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form action="{{ route('sales-deal-confirmation.update', ['salesDealConfirmation' => $salesDeal->id]) }}" method="post">
                                                @method(strtoupper('patch'))
                                                
                                                @csrf
                                                
                                                <input type="hidden" name="route-name" value="{{ request()->route()->getName() }}">
                                                <button type="button" class="btn btn-lg btn-default" data-toggle="modal" data-target="#modal-alert">
                                                    <span class="fal fa-check mr-1"></span>
                                                    Authorize
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
@endif
                    </main>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
@endsection

@section('javascript')
        <script type="text/javascript">
            $(document).ready( function() {

                if ($(document).has('#panel-sales-deal-created-show').length) {
                    $(document).find('#panel-sales-deal-show').parent().removeClass('col-12').addClass('col-6');
                    $(document).find('#panel-sales-deal-created-show').height($(document).find('#panel-sales-deal-show').height());

                    $(document).find('#panel-sales-deal-show')
                    .find('.form-group').has('label:not([for="edited-by"],[for="created-at"],[for="updated-at"])')
                    .find('input[readonly], textarea[readonly], a[download]').each( function(key, element) {
                        if (
                            $(document).find('#panel-sales-deal-created-show')
                            .find('input[value="' + element.value + '"], a[href="' + element.pathname + '"]')
                            .length < 1
                        )
                        {
                            element.classList.add('border-primary');
                        }
                    })
                }

            })
        </script>
@endsection
