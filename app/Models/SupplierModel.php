<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SupplierModel extends Model
{

	protected $table = 'teves_supplier_table';
	
	protected $fillable = [
        'supplier_name',
		'supplier_address',
		'supplier_tin',
    ];
	
	protected $primaryKey = 'supplier_id';
    
}
