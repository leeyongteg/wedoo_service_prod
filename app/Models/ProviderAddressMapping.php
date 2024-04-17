<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderAddressMapping extends Model
{
  use HasFactory, SoftDeletes;
  protected $table = 'provider_address_mappings';
  protected $fillable = [
    'provider_id',
    'address',
    'latitude',
    'longitude',
    'status'
  ];

  protected $casts = [
    'provider_id'   => 'integer',
    'status'        => 'integer',
  ];

  public function providers()
  {
    return $this->belongsTo(User::class, 'provider_id', 'id');
  }

  public function getServiceAddress()
  {
    return $this->hasMany(ProviderServiceAddressMapping::class, 'provider_address_id', 'id');
  }

  public function scopeMyAddress($query)
  {
    $user = auth()->user();
    if ($user->hasRole('admin') || $user->hasRole('demo_admin')) {
      return $query;
    }

    if ($user->hasRole('provider')) {
      return $query->where('provider_id', $user->id);
    }

    return $query;
  }

  public function handyman()
  {
    return $this->hasMany(User::class, 'service_address_id', 'id');
  }

  // @author @Lee
  public function services()
  {
    return $this->belongsToMany(Service::class, 'provider_service_address_mappings', 'provider_address_id', 'service_id');
  }

  public function subscribeToService(Service $service, Collection $addresses)
  {
    // Dissociez l'adresse du service actuel
    $this->services()->detach($service);

    // Associez l'adresse au service sélectionné
    $this->services()->attach($service, ['provider_address_id' => $this->id]);

    // Associez les adresses sélectionnées au service
    $addresses->each(function ($address) use ($service) {
      if (!$address->services->contains($service)) {
        $address->services()->attach($service, ['provider_address_id' => $address->id]);
      }
    });

    // Dissociez les adresses des services actuels
    $this->services()->wherePivot('provider_address_id', '!=', $this->id)->get()->each(function ($address) use ($service) {
      $address->services()->detach($service);
    });
  }
}
