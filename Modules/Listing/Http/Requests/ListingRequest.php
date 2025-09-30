<?php

namespace Modules\Listing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->isMethod('post')) {
            $rules = [
                'category_id'=>'required',
                'sub_category_id'=>'nullable',
                'title'=>'required',
                'slug'=>'required|unique:listings',
                'short_description'=>'required|max:255',
                'description'=>'required',
                'thumb_image'=>'required',
            ];
        }

        if ($this->isMethod('put')) {
            if($this->request->get('lang_code') == admin_lang()){
                $rules = [
                    'title'=>'required',
                    'slug'=>'required|unique:listings,slug,'.$this->listing.',id',
                    'description'=>'required',
                    'short_description'=>'required|max:255',
                    'sub_category_id'=>'nullable',
                    'thumb_image'=>'sometimes|required',
                ];
            }else{
                $rules = [
                    'title'=>'required',
                    'description'=>'required',
                ];
            }
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'category_id.required' => trans('translate.Category is required'),
            'sub_category_id.required' => trans('translate.Sub Category is required'),
            'title.required' => trans('translate.Title is required'),
            'slug.required' => trans('translate.Slug is required'),
            'slug.unique' => trans('translate.Slug already exist'),
            'description.required' => trans('translate.Description is required'),
            'thumb_image.required' => trans('translate.Image is required'),
        ];
    }
}
