<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'customer_id',
        'employee_id',
        'payment_method_id',
        'payment_type',
        'currency_id',
        'price',
        'status',
        'payment_status',
        'form_data',
        'note'
    ];

    protected $casts = [
        'form_data' => 'array',
        'price' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The customer (user) who placed the order.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * The employee (user) assigned to fulfill the order.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * The employee (user) assigned to fulfill the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * The payment method used (optional, if online).
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Is this a cash on delivery order?
     */
    public function isCOD()
    {
        return $this->payment_type === 'cod';
    }

    /**
     * Is this an online payment order?
     */
    public function isOnline()
    {
        return $this->payment_type === 'online';
    }

    /**
     * Accessor: Human-readable payment type.
     */
    public function getPaymentTypeLabelAttribute()
    {
        return $this->isCOD() ? 'Cash on Delivery' : ucfirst($this->paymentMethod->type ?? 'Online');
    }

    public function getTranslations(string $attribute): array
    {
        return $this->{$attribute} ?? [];
    }
}