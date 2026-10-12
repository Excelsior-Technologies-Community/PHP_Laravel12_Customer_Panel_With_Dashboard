<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CustomerAddress extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::checkAndCreateTable();
    }

    public static function checkAndCreateTable()
    {
        try {
            if (!Schema::hasTable('customer_addresses')) {
                Schema::create('customer_addresses', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                    $table->string('title')->default('Home'); // Home, Office, Other
                    $table->string('recipient_name');
                    $table->string('phone');
                    $table->string('address_line1');
                    $table->string('address_line2')->nullable();
                    $table->string('city');
                    $table->string('state');
                    $table->string('zip_code');
                    $table->string('country')->default('India');
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
        'title',
        'recipient_name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'country',
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
