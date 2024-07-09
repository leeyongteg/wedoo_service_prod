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
            'status'                         => 'required',
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'integer|min:0',
            'type' => 'required|in:fixed,hourly,free',
            'duration' => $this->input('type') === 'hourly' ? 'required' : 'nullable',
            'status' => 'required',
            'min_price_range' => [
                $this->input('type') === 'fixed' ? 'required' : 'nullable',
                $this->input('type') === 'fixed' ? 'integer' : '',
                $this->input('type') === 'fixed' ? 'min:500' : 'min:0',
            ],
            'max_price_range' => [
                $this->input('type') === 'fixed' ? 'required' : 'nullable',
                $this->input('type') === 'fixed' ? 'integer' : '',
                $this->input('type') === 'fixed' ? 'min:1000' : 'min:0',

            ],
            'price' => [
                $this->input('type') === 'hourly' ? 'required' : 'nullable',
                $this->input('type') === 'hourly' ? 'integer' : '',
                $this->input('type') === 'hourly' ? 'min:500' : 'min:0',
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
