<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CustomerPaymentMethod extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::checkAndCreateTable();
    }

    public static function checkAndCreateTable()
    {
        try {
            if (!Schema::hasTable('customer_payment_methods')) {
                Schema::create('customer_payment_methods', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                    $table->string('method_type')->default('card'); // card, upi, netbanking
                    $table->string('card_brand')->nullable(); // Visa, Mastercard, RuPay
                    $table->string('last_four')->nullable(); // 4242
                    $table->string('holder_name')->nullable();
                    $table->string('expiry_month')->nullable();
                    $table->string('expiry_year')->nullable();
                    $table->string('upi_id')->nullable(); // username@okaxis
                    $table->boolean('is_default')->default(false);
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Silence
        }
    }

    protected $fillable = [
        'user_id',
        'method_type',
        'card_brand',
        'last_four',
        'holder_name',
        'expiry_month',
        'expiry_year',
        'upi_id',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
