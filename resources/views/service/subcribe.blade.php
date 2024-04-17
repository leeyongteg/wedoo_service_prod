<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            <a href="{{ route('service.index') }}" class="btn btn-sm btn-primary float-right"><i
                                    class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            {{-- @if ($auth_user->can('service list'))
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ Form::model($servicedata, ['method' => 'POST', 'route' => ['service.provider-subscribe'], 'data-toggle' => 'validator', 'id' => 'service']) }}
                        {{ Form::hidden('id') }}
                        <div class="row">

                            <div class="form-group col-12">
                                {{ Form::label('name', __('messages.select_name', ['select' => __('messages.provider_address')]), ['class' => 'form-control-label'], false) }}
                                <br />
                                {{ Form::select('provider_address_id[]', [], old('provider_address_id'), [
                                    'class' => 'select2js form-group provider_address_id',
                                    'id' => 'provider_address_id',
                                    'multiple' => 'multiple',
                                    'data-placeholder' => __('messages.select_name', ['select' => __('messages.provider_address')]),
                                ]) }}
                                <a href="{{ route('provideraddress.create') }}" class=""><i
                                        class="fa fa-plus-circle mt-2"></i>
                                    {{ trans('messages.add_form_title', ['form' => trans('messages.provider_address')]) }}</a>
                            </div>

                            @if (
                                !sizeof(
                                    $servicedata->providerAddressMappings()->where('provider_id', auth()->user()->id)->get()) <= 0)
                                <div class="form-group col-md-3">
                                    <div class="custom-control custom-switch">
                                        {{ Form::checkbox('enable_unsubscription_service', $servicedata->is_enable_advance_payment, null, [
                                            'class' => 'custom-control-input',
                                            'id' => 'enable_unsubscription_service',
                                        ]) }}
                                        <label class="custom-control-label"
                                            for="enable_unsubscription_service">{{ __('messages.enable_unsubscription_service') }}
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        {{ Form::submit(__('messages.save'), ['class' => 'btn btn-md btn-primary float-right']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
        <script type="text/javascript">
            (function($) {
                "use strict";
                $(document).ready(function() {
                    let provider_id = "{{ isset($auth_user) ? $auth_user->id : '' }}";
                    let provider_address_id =
                        "{{ isset($provider_address_mapping) ? $provider_address_mapping : [] }}";
                    let provider_service_adrress_mappings =
                        "{{ isset($provider_service_adrress_mappings) ? $provider_service_adrress_mappings : [] }}";
                    console.log(provider_id, provider_address_id)
                    providerAddress(provider_id, provider_address_id, provider_service_adrress_mappings)

                    $(document).on('change', '#provider_id', function() {
                        let provider_id = $(this).val();
                        $('#provider_address_id').empty();
                        providerAddress(provider_id, provider_address_id);
                    })
                })

                function providerAddress(provider_id, provider_address_id = "", provider_service_adrress_mappings = "") {
                    let provider_address_route =
                        "{{ route('ajax-list', ['type' => 'provider_address', 'provider_id' => '']) }}" + provider_id;
                    provider_address_route = provider_address_route.replace('amp;', '');
                    $.ajax({
                        url: provider_address_route,
                        success: function(result) {
                            $('#provider_address_id').select2({
                                width: '100%',
                                placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.provider_address')]) }}",
                                data: result.results
                            });
                            if (provider_service_adrress_mappings != "") {
                                $('#provider_address_id').val(provider_service_adrress_mappings.split(','))
                                    .trigger('change');
                            }
                        }
                    });
                }
            })(jQuery);
        </script>
    @endsection
</x-master-layout>
