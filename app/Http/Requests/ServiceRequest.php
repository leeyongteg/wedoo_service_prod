<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->id;
        return [
            'name'                           => 'required|unique:services,name,' . $id,
            'category_id'                    => 'required',
            'type'                           => 'required',
            'price'                          => 'required|min:0',
            'status'                         => 'required',
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|integer|min:0',
            'type' => 'required|in:fixed,hourly,free',
            'duration' => $this->input('type') === 'hourly' ? 'required' : 'nullable',
            'status' => 'required',
            'min_price_range' => [
                $this->input('type') === 'fixed' ? 'required' : 'nullable',
                'integer',
                'min:3500'
            ],
            'max_price_range' => [
                $this->input('type') === 'fixed' ? 'required' : 'nullable',
                'integer',
                'min:5000'
            ],
            'price' => [
                $this->input('type') === 'hourly' ? 'required' : 'nullable',
                'integer',
                'min:3500'
            ],
        ];
    }
    public function messages()
    {
        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        if (request()->is('api*')) {
            $data = [
                'status' => 'false',
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];

            throw new HttpResponseException(response()->json($data, 422));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}