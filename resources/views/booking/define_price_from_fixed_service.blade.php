<!-- Modal -->

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {{ Form::open(['route' => 'booking.define_price', 'method' => 'post', 'data-toggle' => 'validator']) }}

        {{-- {{ Form::model($bookingdata, ['method' => 'patch', 'route' => ['booking.update', $bookingdata->id], 'data-toggle' => 'validator', 'id' => 'booking']) }} --}}

        <div class="modal-body">
            {{ Form::hidden('id', $bookingdata->id) }}
            <div class="row">
                {{-- @Lee --}}
                {{-- Price --}}
                <div class="form-group col-12" id="fixed_price">
                    {{ Form::label(
                        'price',
                        __('messages.price') . ' <span class="text-danger">*</span>',
                        [
                            'class' => 'form-control-label',
                        ],
                        false,
                    ) }}
                    <span class="pl-2"> {{ __('messages.price_ranger') }}
                        ({{ $bookingdata->service->min_price_range }} -
                        {{ $bookingdata->service->max_price_range }}) </span>
                    {{ Form::number('price', $bookingdata->price ?? null, [
                        'min' => $bookingdata->service->min_price_range,
                        'max' => $bookingdata->service->max_price_range,
                        'step' => '500',
                        'placeholder' => __('messages.price'),
                        'class' => 'form-control',
                        'required',
                    ]) }}
                </div>
                {{-- End Price --}}

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-md btn-secondary"
                data-dismiss="modal">{{ trans('messages.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit"
                data-form="ajax">{{ trans('messages.save') }}</button>
        </div>
        {{ Form::close() }}
    </div>
</div>
