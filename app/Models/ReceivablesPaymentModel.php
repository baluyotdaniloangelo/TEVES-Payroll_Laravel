<?php
namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivablesPaymentModel extends Model
{
    //use HasFactory;
	protected $table = 'teves_receivable_payment';

	protected $fillable = [
        'receivable_payment_id',
		'receivable_idx',
        'client_idx',
		'receivable_date_of_payment',
		'receivable_mode_of_payment',
        'receivable_reference',
		'receivable_payment_amount',
        'created_at',
		'updated_at'
    ];
    protected $primaryKey = 'receivable_payment_id';
	
}
