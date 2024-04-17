<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model implements HasMedia
{
  use InteractsWithMedia, HasFactory, SoftDeletes;

  protected $table = 'services';
  protected $fillable = [
    'name',
    'category_id',
    'provider_id',
    'type',
    'is_slot',
    'discount',
    'duration',
    'description',
    'is_featured',
    'status',
    'price',
    'added_by',
    'subcategory_id',
    'service_type',
    'visit_type',
    'is_enable_advance_payment',
    'advance_payment_amount',
    'min_price_range',
    'max_price_range',
  ];

  protected $casts = [
    'category_id'               => 'integer',
    'subcategory_id'            => 'integer',
    'provider_id'               => 'integer',
    'price'                     => 'double',
    'discount'                  => 'double',
    'status'                    => 'integer',
    'is_featured'               => 'integer',
    'added_by'                  => 'integer',
    'is_slot'                   => 'integer',
    'is_enable_advance_payment' => 'integer',
    'advance_payment_amount'    => 'double',
    'min_price_range'           => 'double',
    'max_price_range'           => 'double',
  ];

  public function providers()
  {
    return $this->belongsTo('App\Models\User', 'provider_id', 'id')->withTrashed();
  }
  public function category()
  {
    return $this->belongsTo('App\Models\Category', 'category_id', 'id')->withTrashed();
  }
  public function subcategory()
  {
    return $this->belongsTo('App\Models\SubCategory', 'subcategory_id', 'id')->withTrashed();
  }
  public function serviceRating()
  {
    return $this->hasMany(BookingRating::class, 'service_id', 'id')->orderBy('created_at', 'desc');
  }
  public function serviceBooking()
  {
    return $this->hasMany(Booking::class, 'service_id', 'id');
  }
  public function serviceCoupons()
  {
    return $this->hasMany(CouponServiceMapping::class, 'service_id', 'id');
  }

  public function getUserFavouriteService()
  {
    return $this->hasMany(UserFavouriteService::class, 'service_id', 'id');
  }

  public function providerAddress()
  {
    return $this->hasMany(ProviderAddressMapping::class, 'provider_id', 'id');
  }

  /**
   * A pour but de traiter les addresses lies aux services auquels
   * un provider souscrit
   */
  public function providerServiceAddress()
  {
    return $this->hasMany(ProviderServiceAddressMapping::class, 'service_id', 'id')->with('providerAddressMapping');
    #return $this->belongsToMany(ProviderServiceAddressMapping::class, 'provider_service_address_mappings', 'service_id', 'provider_address_id');
  }

  protected static function boot()
  {
    parent::boot();
    static::deleted(function ($row) {
      $row->serviceBooking()->delete();
      $row->serviceCoupons()->delete();
      $row->serviceRating()->delete();
      $row->getUserFavouriteService()->delete();

      if ($row->forceDeleting === true) {
        $row->serviceRating()->forceDelete();
        $row->serviceCoupons()->forceDelete();
        $row->serviceBooking()->forceDelete();
        $row->getUserFavouriteService()->forceDelete();
      }
    });

    static::restoring(function ($row) {
      $row->serviceRating()->withTrashed()->restore();
      $row->serviceCoupons()->withTrashed()->restore();
      $row->serviceBooking()->withTrashed()->restore();
      $row->getUserFavouriteService()->withTrashed()->restore();
    });
  }
  // Reecrit par @Lee
  public function scopeMyService($query)
  {
    $user = auth()->user();

    if ($user->hasRole('admin') || session()->get('all_services') == true) {
      return $query->where('service_type', 'service')->withTrashed();
    }

    if ($user->hasRole('provider')) {
      return $query->whereHas('providerss', function ($providerQuery) use ($user) {
        $providerQuery->where('provider_service.provider_id', $user->id);
      });
    }

    return $query;
  }

  public function scopeLocationService($query, $latitude = '', $longitude = '', $radius = 50, $unit = 'km')
  {
    if (default_earning_type() === 'subscription') {
      $provider = User::where('user_type', 'provider')->where('status', 1)->where('is_subscribe', 1)->pluck('id');
    } else {
      $provider = User::where('user_type', 'provider')->where('status', 1)->pluck('id');
    }
    $unit_value = countUnitvalue($unit);
    $near_location_id = ProviderAddressMapping::selectRaw("id, provider_id, address, latitude, longitude,
                ( $unit_value * acos( cos( radians($latitude) ) *
                cos( radians( latitude ) )
                * cos( radians( longitude ) - radians($longitude)
                ) + sin( radians($latitude) ) *
                sin( radians( latitude ) ) )
                ) AS distance")
    ->where('status', 1)
      ->whereIn('provider_id', $provider)
      ->having("distance", "<=", $radius)
      ->orderBy("distance", 'asc')
      ->get()->pluck('id');
    return $near_location_id;
  }
  public function scopeList($query)
  {
    return $query->orderBy('updated_at', 'desc');
  }
  public function servicePackage()
  {
    return $this->hasMany(PackageServiceMapping::class, 'service_id', 'id');
  }
  public function postJobService()
  {
    return $this->hasMany(PostJobServiceMapping::class, 'service_id', 'id');
  }
  public function serviceAddon()
  {
    return $this->hasMany(ServiceAddon::class, 'service_id', 'id');
  }

  // @author Lee
  public function subscribeToService(Service $service, User $provider, ?Collection $addresses)
  {
    $service->providerAddressMappings()->sync($addresses->pluck('id')->toArray());

    if (sizeof($service->providerAddressMappings()->where('provider_id', $provider->id)->get()) <= 0)
      $service->providerss()->detach($provider->id);
    else
      $service->providerss()->attach($provider->id);
  }

  public function providerss()
  {
    return $this->belongsToMany(User::class, 'provider_service', 'service_id', 'provider_id');
  }

  /**
   * A pour but de traiter les addresses des providers
   */
  public function providerAddressMappings()
  {
    return $this->belongsToMany(ProviderAddressMapping::class, 'provider_service_address_mappings', 'service_id', 'provider_address_id');
  }
}
