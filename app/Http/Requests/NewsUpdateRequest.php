<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class NewsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('administrator')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul_berita' => ['required'],
            'deks_berita' => ['required'],
            'kategori' => ['required' , 'string'],
            'gambar' => ['nullable' , 'image' , 'mimes:jpeg,png,jpg|max:2048'],
            'gambar2' => ['nullable' , 'image' , 'mimes:jpeg,png,jpg|max:2048'],
            'keterangan_gambar' => ['required'],
            'keterangan_gambar2' => ['nullable'],
        ];
    }


    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
