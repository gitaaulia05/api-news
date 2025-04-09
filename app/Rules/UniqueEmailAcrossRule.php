<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEmailAcrossRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
         if(
            DB::table('pengguna')->where('email' , $value)->exists() ||
            DB::table('administrators')->where('email' , $value)->exists() 
         ){
                $fail('Email Sudah di Gunakan, Gunakan Email yang Lain !');
         }
    }
}
