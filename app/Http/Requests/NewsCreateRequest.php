<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewsCreateRequest extends FormRequest
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
            'id_kategori_berita' => ['required'],
            'gambar' => ['required' , 'image' , 'mimes:jpeg,png,jpg' , 'max:2048'],
            'gambar2' => ['nullable' , 'image' , 'mimes:jpeg,png,jpg' , 'max:2048', 'required_with:keterangan_gambar2'],
            'keterangan_gambar' => ['required'],
            'keterangan_gambar2' => ['nullable', 'required_with:gambar2'],
        ];
        
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
