<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DB;

class UniqueCliente implements Rule
{
    /**
     * Create a new rule instance.O
     *
     * @return void
     * 
     */
    protected $idRestaurant;


    public function __construct($x)
    {
        $this->idRestaurant = $x;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //$value es el dni
//        \Log::info('**********************');
//        \Log::info($attribute);
//        \Log::info($value);
//        \Log::info($this->idRestaurant);
        $x = DB::table('clientes as c')
                ->where('dni', '=', $value)
                ->where('id_restaurant', '=', $this->idRestaurant)
                ->get();
        if(sizeof($x)>0){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El dni ya existe';
    }
}
