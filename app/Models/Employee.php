<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name','last_name','city_id','department_id',
        'address','zip_code','date_of_birth','date_hired'
    ];

    public function city(){
        return $this->belongsTo(City::class, 'city_id');
    }

    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
