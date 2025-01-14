<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * Atributos asignables en asignación masiva.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_number',
        'account_id',
        'amount',
        'type',
    ];

    /**
     * Relación: la transacción pertenece a una cuenta.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
