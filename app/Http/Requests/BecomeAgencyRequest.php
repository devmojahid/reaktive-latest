<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BecomeAgencyRequest extends FormRequest
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
            'old_agency_logo' => 'nullable',
            'agency_logo' => 'required_without:old_agency_logo|mimes:jpg,jpeg,png|max:2048',
            'agency_name' => 'required',
            'agency_slug' => 'required|unique:users,agency_slug,' . auth()->user()->id,
            'about_me' => 'required',
            'country' => 'required|max:255',
            'state' => 'required|max:255',
            'city' => 'required|max:255',
            'address' => 'required|max:255',
        ];
    }


    public function messages(): array
    {
        return [
            'agency_logo.required' => trans('translate.Logo is required'),
            'agency_name.required' => trans('translate.Agency Name is required'),
            'agency_slug.required' => trans('translate.Agency Slug is required'),
            'agency_slug.unique' => trans('translate.Agency Slug already exist'),
            'about_me.required' => trans('translate.Agency Description is required'),
            'country.required' => trans('translate.Country is required'),
            'state.required' => trans('translate.State is required'),
            'city.required' => trans('translate.City is required'),
            'address.required' => trans('translate.Address is required'),
        ];
    }
}
