<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenggunaUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'alpha'],
            'email' =>['required' , 'email:rfc,dns' ,'unique:pengguna,column,except,id'],
            'password' => ['required'],
            'alamat' => ['required'],
            'provinsi' => ['required'],
            'password' => ['required'],
            'kode_pos' => ['required', 'numeric'],
            'pendidikan_terakhir' => ['required'],
            'pekerjaan' => ['required'],
        ];
    }
}
