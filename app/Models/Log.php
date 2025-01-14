<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * Atributos que se pueden asignar de manera masiva (mass assignment).
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'endpoint',
        'method',
        'ip_address',
    ];

    /**
     * Relación opcional: un Log pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
