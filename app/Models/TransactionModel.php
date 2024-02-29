<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionModel extends Model
{
    use HasFactory;

    protected $table = 'voucher_transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_number',
        'campaign_id',
        'product_id',
        'product_name',
        'normal_price',
        'subsidy_price',
        'transaction_amount',
        'customer_name',
        'customer_phone',
        'customer_email',
        'is_auth_wa',
        'is_auth_gmail',
        'status',
        'notes',
        'midtrans_snap_token',
        'midtrans_status_code',
        'midtrans_status_message',
        'midtrans_payment_type',
        'midtrans_status',
        'midtrans_payment_time',
        'midtrans_response'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->transaction_number = static::generateTransactionNumber();
        });
    }

    protected static function generateTransactionNumber()
    {
        $prefix = 'LB';
        $datePart = now()->format('Ymd');
        $maxNumber = static::whereDate('created_at', now()->toDateString())->max('transaction_number');
        $sortNumber = $maxNumber ? (int)substr($maxNumber, -6) + 1 : 1;

        return $prefix . $datePart . str_pad($sortNumber, 6, '0', STR_PAD_LEFT);
    }

}
