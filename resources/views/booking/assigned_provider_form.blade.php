<!-- Modal -->

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
       {{ Form::open(['route' => 'booking.assigned_provider','method' => 'post','data-toggle'=>"validator"]) }}
        <div class="modal-body">

           {{ Form::hidden('id',$bookingdata->id) }}
            <div class="row">

                <div class="col-md-12 form-group ">
                    {{ Form::label('provider_id', __('messages.select_name',[ 'select' => __('messages.handyman') ]).' <span class="text-danger">*</span>',['class'=>'form-control-label'],false) }}
                    <br />
                    @php
                        if($bookingdata->booking_address_id != null)
                        {
                            $route = route('ajax-list', ['type' => 'provider', 'provider_id' => $bookingdata->provider_id, 'booking_id' => $bookingdata->id ]);
                        } else {
                            $route = route('ajax-list', ['type' => 'provider', 'provider_id' => $bookingdata->provider_id ]);
                        }
                        $assigned_provider = $bookingdata->handymanAdded->mapWithKeys(function ($item) {
                            return [$item->provider_id => optional($item->handyman)->display_name];
                        });
                    @endphp
                    {{ Form::select('provider_id[]', $assigned_provider, $bookingdata->handymanAdded->pluck('provider_id'), [
                            'class' => 'select2js handyman',
                            'id' => 'provider_id',
                            'required',
                            'data-placeholder' => __('messages.select_name',[ 'select' => __('messages.handyman') ]),
                            'data-ajax--url' => $route,
                        ]) }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-md btn-secondary" data-dismiss="modal">{{ trans('messages.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit" data-form="ajax" >{{ trans('messages.save') }}</button>
        </div>
        {{ Form::close() }}
    </div>
</div>
<script>
    $('#provider_id').select2({
        width: '100%',
        placeholder: "{{ __('messages.select_name',['select' => __('messages.provider')]) }}",
    });
</script>
