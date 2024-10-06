<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPaymentModel extends Model
{
	/*
	`purchase_order_payment_details_id` INT(11) NOT NULL AUTO_INCREMENT,
	`purchase_order_payment_idx` INT(11) NULL DEFAULT NULL,
	`purchase_order_bank` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`purchase_order_date_of_payment` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`purchase_order_reference_no` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`purchase_order_payment_amount` DOUBLE NULL DEFAULT NULL,
	`created_at` DATETIME NULL DEFAULT NULL,
	`updated_at` DATETIME NULL DEFAULT NULL,
	*/
	
    //use HasFactory;
	protected $table = 'teves_purchase_order_payment_details';

	protected $fillable = [
        'purchase_order_payment_details_id',
		'purchase_order_idx',
        'purchase_order_bank',
		'purchase_order_date_of_payment',
		'purchase_order_reference_no',
        'purchase_order_payment_amount',
        'created_at',
		'updated_at'
    ];
    protected $primaryKey = 'purchase_order_payment_details_id';
}
