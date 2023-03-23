<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize()
    {
        // Replace this with your authorization logic or return true if you don't need it
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
            'password' => 'required',
        ];
    }
}
