<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use HasFactory;

    // FIX: ganti guarded=[] dengan fillable eksplisit
    // Kolom sensitif seperti 'status' tidak boleh di-mass assign sembarangan
    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'subtotal',
        'diskon_item',
        'voucher',
        'potongan_voucher',
        'total',
        'dibayar',
        'kembalian',
        'payment_method',
        'pelanggan_name',
        'status',
        'paid_at',
        'terminal_id',
        'user_id',
        'tax',
        'tax_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * FIX: relasi ke Voucher.
     * Kolom 'voucher' adalah foreign key integer → harus belongsTo bukan hasOne.
     * Diberi nama voucherData() agar tidak bentrok dengan nama kolom $transaction->voucher
     */
    public function voucherData()
    {
        return $this->belongsTo(Voucher::class, 'voucher');
    }
}