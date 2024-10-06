<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationFileModel extends Model
{
    //use HasFactory;
	protected $table = 'meter_configuration_file';
	
	protected $fillable = [
        'meter_model',
        'config_file'
    ];
    
}
