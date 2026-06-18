<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We allow this request to proceed to validation
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'token'        => 'required',
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'password'     => 'required|string|min:8|confirmed',
            'national_id'  => 'required|string|unique:users,national_id',
            'phone_number' => 'required|string|max:20',
            // Making ward_id nullable for admins, but if provided, it must exist
            'ward_id'      => 'nullable|exists:wards,id', 
        ];
    }
}