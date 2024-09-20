{{-- @todo Voir comment retirer ceci de la vue --}}
@php
    $sitesetup = App\Models\Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
    $datetime = $sitesetup ? json_decode($sitesetup->value) : null;
@endphp
{{ Form::hidden('id', $bookingdata->id) }}

<div class="card-body p-0">
    <div class="border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 pb-3">
        <div>
            <h3 class="c1 mb-2">{{ __('messages.book_id') }} {{ '#' . $bookingdata->id ?? '-' }}</h3>
            <p class="fz-12 opacity-75">
                {{ __('messages.book_placed') }}
                {{ date("$datetime->date_format / $datetime->time_format", strtotime($bookingdata->created_at)) ?? '-' }}
            </p>
        </div>
        <div class="d-flex flex-xxl-nowrap flex-wrap gap-3" data-select2-id="select2-data-8-5c7s">
            {{-- Edit Bokking --}}
            @if ($bookingdata->provider_id != 0)
                @hasanyrole('admin|demo_admin|provider')
                    {{-- <a href="{{ route('booking.edit', $bookingdata->id) }}" --}}
                    <a href="{{ route('booking.define_price_form', ['id' => $bookingdata->id]) }}"
                        class="btn btn-sm btn-secondary loadRemoteModel float-right"><i class="las la-money-bill"></i></i>
                        Define Price</a>
                @endhasanyrole
            @endif
            {{-- Assign handyman --}}
            @if ($bookingdata->handymanAdded->count() == 0)
                @hasanyrole('admin|demo_admin|provider')
                    <a href="{{ route('booking.assign_form', ['id' => $bookingdata->id]) }}"
                        class="btn btn-sm btn-secondary loadRemoteModel float-right"><i class="lab la-telegram-plane"></i>
                        {{ __('messages.assign') }}</a>
                @endhasanyrole
            @endif
            {{-- Assign provider --}}
            @if ($bookingdata->providerAdded->count() == 0)
                @hasanyrole('admin|demo_admin')
                    <a href="{{ route('booking.assign_provider_form', ['id' => $bookingdata->id]) }}"
                        class="btn btn-sm btn-primary loadRemoteModel float-right mr-2"><i class="las la-user-shield"></i>
                        {{ __('messages.assign_provider') }}</a>
                @endhasanyrole
            @endif
        </div>
        @if ($bookingdata->payment_id !== null)
            <a href="{{ route('invoice_pdf', $bookingdata->id) }}" class="btn btn-primary" target="_blank">
                <i class="ri-file-text-line"></i>

                {{ __('messages.invoice') }}
            </a>
        @endif
    </div>
</div>
<div class="pay-box">
    <div class="pay-method-details">
        <h4 class="mb-2">{{ __('messages.payment_method') }}</h4>
        <h5 class="c1 mb-2">{{ __('messages.cash_after') }}</h5>
        <p><span>{{ __('messages.amount') }} :
            </span>
            @if ($bookingdata->service->type != 'fixed' || $bookingdata->service->type != 'estimate')

                <strong>{{ !empty($bookingdata->total_amount) ? getPriceFormat($bookingdata->total_amount) : 0 }}</strong>
            @else
                @if ($bookingdata->price != 0)
                    <strong>{{ !empty($bookingdata->total_amount) ? getPriceFormat($bookingdata->total_amount) : 0 }}</strong>
                @else
                    <span class="text-danger"> {{ __('messages.amount_fixed_price') }} </span>
                @endif
            @endif
        </p>
    </div>
    <div class="pay-booking-details">
        <div class="row mb-2">
            <div class="col-sm-6"><span>{{ __('messages.booking_status') }} :</span></div>
            <div class="col-sm-6 align-text">
                <span class="c1"
                    id="booking_status__span">{{ App\Models\BookingStatus::bookingStatus($bookingdata->status) }}</span>
            </div>
            @if ($bookingdata->status === 'cancelled')
                <div class="col-sm-6"><span>{{ __('messages.reason') }} :</span></div>
                <div class="col-sm-6 align-text">
                    <span class="c1" id="booking_status__span">{{ $bookingdata->reason }}</span>
                </div>
            @endif
        </div>
        <div class="row mb-2">
            <div class="col-sm-6"> <span>{{ __('messages.payment_status') }} : </span></div>
            <div class="col-sm-6 align-text">
                <span id="payment_status__span"
                    class="{{ optional($bookingdata->payment)->payment_status == 'paid' ? 'text-success' : 'text-danger' }}">
                    {{ ucwords(str_replace('_', ' ', optional($bookingdata->payment)->payment_status ?: 'pending')) }}
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <h5>
                    {{ __('messages.booking_date') }} :
                </h5>
            </div>
            <div class="col-sm-6 align-text">
                <span
                    id="service_schedule__span">{{ date("$datetime->date_format / $datetime->time_format", strtotime($bookingdata->date)) ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>
<div class="d-flex customer-info-detail mb-2 flex-wrap gap-3 py-3">
    <div class="c1-light-bg radius-10 flex-grow-1 px-4 py-3">
        <h4 class="mb-2">{{ __('messages.customer_information') }}</h4>
        <h5 class="c1 mb-3">{{ optional($bookingdata->customer)->display_name ?? '-' }}</h5>
        <ul class="list-info">
            <li>
                <span class="material-icons customer-info-text">{{ __('messages.phone_information') }}</span>
                <a href="tel:{{ optional($bookingdata->customer)->contact_number }}" class="customer-info-value">
                    <p class="mb-0">{{ optional($bookingdata->customer)->contact_number ?? '-' }}</p>
                </a>
            </li>
            <li>
                <span class="material-icons customer-info-text">{{ __('messages.address') }}</span>
                <p class="customer-info-text">{{ optional($bookingdata->customer)->address ?? '-' }}</p>
            </li>
        </ul>
    </div>

    <div class="c1-light-bg radius-10 flex-grow-1 px-4 py-3">
        <h4 class="mb-2">{{ __('messages.provider_information') }}</h4>
        <h5 class="c1 mb-3">{{ optional($bookingdata->provider)->display_name ?? '-' }}</h5>
        <ul class="list-info">
            <li>
                <span class="material-icons customer-info-text">{{ __('messages.phone_information') }}</span>
                <a href="tel:{{ optional($bookingdata->provider)->contact_number }}" class="customer-info-value">
                    <p class="mb-0">{{ optional($bookingdata->provider)->contact_number ?? '-' }}</p>
                </a>
            </li>
            <li>
                <span class="material-icons customer-info-text">{{ __('messages.address') }}</span>
                <p class="customer-info-text">{{ optional($bookingdata->provider)->address ?? '-' }}</p>
            </li>
        </ul>
    </div>

    @if (count($bookingdata->handymanAdded) > 0)
        <div class="c1-light-bg radius-10 flex-grow-1 px-4 py-3">
            @foreach ($bookingdata->handymanAdded as $booking)
                <h4 class="mb-2">{{ __('messages.handyman_information') }}</h4>
                <h5 class="c1 mb-3">{{ optional($booking->handyman)->display_name ?? '-' }}</h5>
                <ul class="list-info">
                    <li>
                        <span class="material-icons customer-info-text">{{ __('messages.phone_information') }}</span>
                        <a href="" class="customer-info-value">
                            <p class="mb-0">{{ optional($booking->handyman)->contact_number ?? '-' }}</p>
                        </a>
                    </li>
                    <li>
                        <span class="material-icons customer-info-text">{{ __('messages.address') }}</span>
                        <p class="customer-info-value">{{ optional($booking->handyman)->address ?? '-' }}</p>
                    </li>
                </ul>
            @endforeach
        </div>
    @endif
</div>
@if ($bookingdata->bookingExtraCharge->count() > 0)
    <h3 class="mb-3 mt-3">{{ __('messages.extra_charge') }}</h3>
    <div class="table-responsive border-bottom">
        <table class="text-nowrap mb-0 table align-middle">
            <thead>
                <tr>
                    <th class="ps-lg-3">{{ __('messages.title') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th>{{ __('messages.quantity') }}</th>
                    <th class="text-end">{{ __('messages.total_amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookingdata->bookingExtraCharge as $chrage)
                    <tr>
                        <td class="text-wrap ps-lg-3">
                            <div class="d-flex flex-column">
                                <a href="" class="booking-service-link fw-bold">{{ $chrage->title }}</a>
                            </div>
                        </td>
                        <td>{{ getPriceFormat($chrage->price) }}</td>
                        <td>{{ $chrage->qty }}</td>
                        <td class="text-end">{{ getPriceFormat($chrage->price * $chrage->qty) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@php
    $addonTotalPrice = 0;
@endphp

@if ($bookingdata->bookingAddonService->count() > 0)
    <h3 class="mb-3 mt-3">{{ __('messages.service_addon') }}</h3>
    <div class="table-responsive border-bottom">
        <table class="text-nowrap mb-0 table align-middle">
            <thead>
                <tr>
                    <th class="ps-lg-3">{{ __('messages.title') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th class="text-end">{{ __('messages.total_amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookingdata->bookingAddonService as $addonservice)
                    @php
                        $addonTotalPrice += $addonservice->price;
                    @endphp
                    <tr>
                        <td class="text-wrap ps-lg-3">
                            <div class="d-flex flex-column">
                                <a href="" class="booking-service-link fw-bold">{{ $addonservice->name }}</a>
                            </div>
                        </td>
                        <td>{{ getPriceFormat($addonservice->price) }}</td>
                        <td class="text-end">{{ getPriceFormat($addonservice->price) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<h3 class="mb-3 mt-3">{{ __('messages.booking_summery') }}</h3>
<div class="table-responsive border-bottom">
    <table class="text-nowrap mb-0 table align-middle">
        <thead>
            <tr>
                <th class="ps-lg-3">{{ __('messages.service') }}</th>
                <th>{{ __('messages.price') }}</th>
                <th>{{ __('messages.quantity') }}</th>
                <th class="text-end">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $locale = session()->get('locale') ?: Cookie::get('locale') ?: app()->getLocale();
                $jsonName = json_decode(optional($bookingdata->service)->name);
                $name = $jsonName->{$locale};
            @endphp
            <tr>
                <td class="text-wrap ps-lg-3">
                    <div class="d-flex flex-column">

                        <a href="" class="booking-service-link fw-bold">{{ $name ?? '-' }}</a>
                    </div>
                </td>
                <td>
                    @if ($bookingdata->service->type == 'fixed' || $bookingdata->service->type == 'estimate')
                        @if ($bookingdata->price != 0)
                            {{ getPriceFormat($bookingdata->amount) }}
                        @else
                            <span class="text-danger"> {{ __('messages.fixed_price_not_define') }} </span>
                        @endif
                    @else
                        {{ isset($bookingdata->amount) ? getPriceFormat($bookingdata->amount) : 0 }}
                    @endif
                </td>
                <td>{{ !empty($bookingdata->quantity) ? $bookingdata->quantity : 0 }}</td>
                <td class="text-end">
                    @if ($bookingdata->service->type == 'fixed' || $bookingdata->service->type == 'estimate')
                        @if ($bookingdata->price != 0)
                            {{ getPriceFormat($bookingdata->final_total_service_price) }}
                        @else
                            <span class="text-danger"> {{ __('messages.fixed_price_not_define') }} </span>
                        @endif
                    @else
                        {{ getPriceFormat($bookingdata->final_total_service_price) }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
{{-- @Lee --}}
@php
    $price =
        $bookingdata->service->type == 'fixed' || $bookingdata->service->type == 'estimate'
            ? $bookingdata->price
            : $bookingdata->service->price;
@endphp

<div class="row justify-content-end mt-3">
    <div class="col-sm-10 col-md-6 col-xl-5">
        @if ($price == 0 && ($bookingdata->service->type == 'fixed' || $bookingdata->service->type == 'estimate'))
            <div class="table-responsive bk-summary-table">
                <table class="table-sm title-color align-right w-100">
                    <tbody>
                    <tbody>
                        <span class="text-danger text-center">{{ __('messages.amount_fixed_price') }}</span>
                    </tbody>
                </table>
            </div>
        @else
            <div class="table-responsive bk-summary-table">
                <table class="table-sm title-color align-right w-100">
                    <tbody>
                        <tr>
                            <td>{{ __('messages.price') }}</td>
                            <td class="bk-value">{{ getPriceFormat($price) }} *
                                {{ $bookingdata->quantity }} =
                                {{ getPriceFormat($bookingdata->final_total_service_price) }}</td>
                        </tr>
                        @if ($bookingdata->bookingPackage == null)
                            <tr>
                                <td>{{ __('messages.discount') }} ({{ $bookingdata->discount }}% off)</td>
                                <td class="bk-value text-success">
                                    -{{ getPriceFormat($bookingdata->final_discount_amount) }}</td>
                            </tr>
                        @endif
                        @if ($bookingdata->couponAdded != null)
                            <tr>
                                <td>{{ __('messages.coupon') }} ({{ $bookingdata->couponAdded->code }})</td>
                                <td class="bk-value text-success">
                                    -{{ getPriceFormat($bookingdata->final_coupon_discount_amount) }}</td>
                            </tr>
                        @endif
                        <tr class="grand-sub-total">
                            <td>{{ __('messages.subtotal_vat') }}</td>
                            <td class="bk-value">{{ getPriceFormat($bookingdata->final_sub_total) }}</td>
                        </tr>

                        @if ($bookingdata->bookingExtraCharge->count() > 0)
                            <tr>
                                <td>{{ __('messages.extra_charge') }} </td>
                                <td class="text-success text-right">
                                    +{{ getPriceFormat($bookingdata->getExtraChargeValue()) }}</td>
                            </tr>
                        @endif

                        @if ($bookingdata->bookingAddonService->count() > 0)
                            <tr>
                                <td>{{ __('messages.add_ons') }} </td>
                                <td class="text-success text-right">+{{ getPriceFormat($addonTotalPrice) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>{{ __('messages.tax') }}</td>
                            <td class="text-danger text-right">{{ getPriceFormat($bookingdata->final_total_tax) }}
                            </td>
                        </tr>
                        <tr class="grand-total">
                            <td><strong>{{ __('messages.grand_total') }}</strong></td>
                            <td class="bk-value">
                                <h3>{{ isset($bookingdata->bookingAddonService) ? getPriceFormat($bookingdata->total_amount + $addonTotalPrice) : getPriceFormat($bookingdata->total_amount) }}
                                </h3>
                            </td>
                        </tr>
                        @if ($bookingdata->service->is_enable_advance_payment == 1)
                            <tr>
                                <td>{{ __('messages.advance_payment_amount') }}
                                    ({{ $bookingdata->service->advance_payment_amount }}%)</td>
                                <td class="text-right">{{ getPriceFormat($bookingdata->advance_paid_amount) }}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ __('messages.remaining_amount') }}</td>
                                <td class="text-right">
                                    {{ getPriceFormat($bookingdata->total_amount - $bookingdata->advance_paid_amount) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
        {{-- End --}}
    </div>
</div>
</div>

<script>
    $(document).on('change', '.bookingstatus', function() {

        var status = $(this).val();

        var id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('bookingStatus.update') }}",
            data: {
                'status': status,
                'bookingId': id
            },
            success: function(data) {}
        });
    })

    $(document).on('change', '.paymentStatus', function() {

        var status = $(this).val();

        var id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('bookingStatus.update') }}",
            data: {
                'status': status,
                'bookingId': id
            },
            success: function(data) {}
        });
    })
</script>
