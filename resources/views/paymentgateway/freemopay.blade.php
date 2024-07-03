{{ Form::model($payment_data, ['method' => 'POST', 'route' => ['paymentsettingsUpdates'], 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator']) }}

{{ Form::hidden('id', null, ['placeholder' => 'id', 'class' => 'form-control']) }}
{{ Form::hidden('type', $tabpage, ['placeholder' => 'id', 'class' => 'form-control']) }}
<div class="row">
    <div class="form-group col-md-12">
        <label for="enable_freemopay">{{ __('messages.payment_on', ['gateway' => __('messages.freemopay')]) }}</label>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" name="status" id="enable_freemopay"
                {{ !empty($payment_data) && $payment_data->status == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="enable_freemopay"></label>
        </div>
    </div>
</div>
<div class="row" id='enable_freemopay_payment'>
    <div class="form-group col-md-12">
        <label
            class="form-control-label">{{ __('messages.payment_option', ['gateway' => __('messages.freemopay')]) }}</label><br />
        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input is_test" value="on" name="is_test"
                    data-type="is_test_mode"
                    {{ !empty($payment_data) && $payment_data->is_test == 1 ? 'checked' : '' }}>{{ __('messages.is_test_mode') }}
            </label>
        </div>
        <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input is_test" value="off" name="is_test"
                    data-type="is_live_mode"
                    {{ !empty($payment_data) && $payment_data->is_test == 0 ? 'checked' : '' }}>{{ __('messages.is_live_mode') }}
            </label>
        </div>
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('title', trans('messages.gateway_name') . ' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}
        {{ Form::text('title', old('title'), ['id' => 'title', 'placeholder' => trans('messages.title'), 'class' => 'form-control', 'required']) }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('freemopay_link_api', trans('messages.freemopay_link_api') . ' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}
        {{ Form::text('freemopay_link_api', old('freemopay_link_api'), ['id' => 'freemopay_link_api', 'placeholder' => trans('messages.freemopay_link_api'), 'class' => 'form-control', 'required']) }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('freemopay_username_app', trans('messages.freemopay_username_app') . ' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}
        {{ Form::text('freemopay_username_app', old('freemopay_username_app'), ['id' => 'freemopay_username_app', 'placeholder' => trans('messages.freemopay_username_app'), 'class' => 'form-control', 'required']) }}
        <small class="help-block with-errors text-danger"></small>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('freemopay_password_app', trans('messages.freemopay_password_app') . ' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}
        {{ Form::text('freemopay_password_app', old('freemopay_password_app'), ['id' => 'freemopay_password_app', 'placeholder' => trans('messages.freemopay_password_app'), 'class' => 'form-control', 'required']) }}
        <small class="help-block with-errors text-danger"></small>
    </div>
</div>
{{ Form::submit(__('messages.save'), ['class' => 'btn btn-md btn-primary float-md-right']) }}
{{ Form::close() }}
<script>
    var enable_freemopay = $("input[name='status']").prop('checked');
    checkPaymentTabOption(enable_freemopay);

    $('#enable_freemopay').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkPaymentTabOption(value);
    });

    function checkPaymentTabOption(value) {
        if (value == true) {
            $('#enable_freemopay_payment').removeClass('d-none');
        } else {
            $('#enable_freemopay_payment').addClass('d-none');
        }
    }

    var get_value = $('input[name="is_test"]:checked').data("type");
    getConfig(get_value)
    $('.is_test').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        type = $(this).data("type");
        getConfig(type)

    });

    function getConfig(type) {
        var _token = $('meta[name="csrf-token"]').attr('content');
        var page = "{{ $tabpage }}";
        $.ajax({
            url: "/get_payment_config",
            type: "POST",
            data: {
                type: type,
                page: page,
                _token: _token
            },
            success: function(response) {
                var obj = '';
                var freemopay_link_api = freemopay_username_app = freemopay_password_app = title = '';

                if (response) {

                    if (response.data.type == 'is_test_mode') {
                        obj = JSON.parse(response.data.value);
                    } else {
                        obj = JSON.parse(response.data.live_value);
                    }

                    if (response.data.title != '') {
                        title = response.data.title
                    }

                    if (obj !== null) {
                        var freemopay_link_api = obj.freemopay_link_api;
                        var freemopay_username_app = obj.freemopay_username_app;
                        var freemopay_password_app = obj.freemopay_password_app;
                    }

                    $('#freemopay_link_api').val(freemopay_link_api)
                    $('#freemopay_username_app').val(freemopay_username_app)
                    $('#freemopay_password_app').val(freemopay_password_app)
                    $('#title').val(title)

                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>
