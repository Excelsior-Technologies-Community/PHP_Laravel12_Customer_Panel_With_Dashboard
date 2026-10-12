<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Order extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::checkAndAddShipmentColumns();
    }

    public static function checkAndAddShipmentColumns()
    {
        try {
            if (Schema::hasTable('orders')) {
                Schema::table('orders', function (Blueprint $table) {
                    if (!Schema::hasColumn('orders', 'carrier')) {
                        $table->string('carrier')->nullable()->default('BlueDart Express');
                    }
                    if (!Schema::hasColumn('orders', 'tracking_number')) {
                        $table->string('tracking_number')->nullable();
                    }
                    if (!Schema::hasColumn('orders', 'estimated_delivery')) {
                        $table->dateTime('estimated_delivery')->nullable();
                    }
                    if (!Schema::hasColumn('orders', 'current_stage')) {
                        $table->string('current_stage')->default('placed');
                    }
                });
            }
        } catch (\Throwable $e) {
            // Silence if already updated
        }
    }

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'notes',
        'carrier',
        'tracking_number',
        'estimated_delivery',
        'current_stage'
    ];

    protected $casts = [
        'estimated_delivery' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper: Tracking URL based on Carrier
    public function getTrackingUrlAttribute(): string
    {
        $tracking = $this->tracking_number ?? 'N/A';
        return match (strtolower($this->carrier ?? '')) {
            'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$tracking}",
            'dhl' => "https://www.dhl.com/en/express/tracking.html?AWB={$tracking}",
            'ups' => "https://www.ups.com/track?tracknum={$tracking}",
            default => "https://www.bluedart.com/tracking?track={$tracking}"
        };
    }

    // Stepper Stage Progress Percentage
    public function getProgressPercentAttribute(): int
    {
        if ($this->status === 'cancelled') return 0;
        return match (strtolower($this->status)) {
            'pending' => 20,
            'processing' => 45,
            'shipped' => 70,
            'out_for_delivery' => 85,
            'completed', 'delivered' => 100,
            default => 25
        };
    }
}