<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model {
    protected $guarded = [];
    protected $casts = ['departure_time' => 'datetime', 'arrival_time' => 'datetime'];
    protected $appends = ['price'];

    public function getPriceAttribute() {
        return $this->base_price ?? $this->attributes['base_price'] ?? 140000;
    }

    public function route() { return $this->belongsTo(Route::class); }
    public function bus() { return $this->belongsTo(Bus::class); }
    public function driver() { return $this->belongsTo(Driver::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
}