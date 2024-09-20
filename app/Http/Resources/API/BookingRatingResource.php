<?php

namespace App\Http\Resources\API;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Resources\Json\JsonResource;


class BookingRatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $locale = session()->get('locale') ?: Cookie::get('locale') ?: app()->getLocale();

        $name = (json_decode(optional($this->service)->name)->{$locale});

        if (optional($this->customer)->login_type != null) {
            $profile_image = optional($this->customer)->social_image;
        } else {
            $profile_image = getSingleMedia($this->customer, 'profile_image', null);
        }
        return [
            'id'            => $this->id,
            'rating'        => $this->rating,
            'review'        => $this->review,
            'service_id'    => $this->service_id,
            'service_name'    => $name,
            'attchments' =>  isset($this->service) ? getAttachments($this->service->getMedia('service_attachment')) : [],
            'booking_id'    => $this->booking_id,
            'created_at'    => date('Y-m-d', strtotime($this->created_at)),
            'customer_id'   => $this->customer_id,
            'customer_name' => optional($this->customer)->display_name,
            'profile_image' => $profile_image
        ];
    }
}
