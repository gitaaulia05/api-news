<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'password' => ['nullable'],
            'alamat' => ['nullable'],
            'provinsi' => ['nullable'],
            'password' => ['nullable'],
            'kode_pos' => ['nullable', 'numeric'],
            'pendidikan_terakhir' => ['nullable'],
            'pekerjaan' => ['nullable'],
        ];
    }

    Protected function failedValidator(Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ],400));
    }
}

