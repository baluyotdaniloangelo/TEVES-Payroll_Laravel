<?php

namespace App\Models;

#use Illuminate\Contracts\Auth\MustVerifyEmail;
#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
#use Laravel\Sanctum\HasApiTokens;

class SalesSummaryChart extends Authenticatable
{
    //use HasApiTokens, Notifiable;
	#use HasApiTokens, HasFactory, Notifiable;
	
	protected $table = 'teves_sales_monthly_summary';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sales_month_year',
		'billing_total_sales',
		'sales_order_total_sales',
        'monthly_sales_total',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
    
    protected $hidden = [
        'user_password',
        'remember_token'
    ];
 */
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
	 /*
    protected $casts = [
        'InputUsername' => 'user_name',
    ];*/
	
}
