<?php

namespace App\Http\Requests\Car;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\PhoneRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreCarRequest extends FormRequest
{
    /*protected $stopOnFirstFailure = true;
    protected $redirect = '/';
    protected $redirectRoute = 'car.index';*/

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'maker_id' => 'Maker',
            'model_id' => 'Model',
            'year' => 'Year',
            'vin' => 'VIN Number',
            'price' => 'Price',
            'mileage' => 'Mileage',
            'interior_color' => 'Interior Color',
            'exterior_color' => 'Exterior Color',
            'car_type_id' => 'Car Type',
            'fuel_type_id' => 'Fuel Type',
            'user_id' => 'User',
            'state_id' => 'State',
            'city_id' => 'City',
            'address' => 'Address',
            'phone' => 'Phone',
            'description' => 'Description',
            'published_at' => 'Published At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At'
        ];
    }

    public function messages(): array
    {
        return [
            //'required' => ':attribute is required.',
            'maker_id.required' => ':attribute is required.',
            'maker_id.integer' => ':attribute must be an integer.',
            'vin.required' => ':attribute is required.',
        ];
    }

    /**
     * Prepares, converts data before validation
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'vin' => strtoupper($this->vin),
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Prepares, converts, adds a data to payload after validation passed
     * @return void
     */
    protected function passedValidation(): void
    {
        $this->merge([
            'vin' => strtoupper($this->vin),
            'user_id' => Auth::id()
        ]);
    }

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'maker_id' => ['required', 'integer', 'exists:App\Models\Maker,id'],
            'model_id' => ['required', 'integer', 'exists:App\Models\Model,id'],
            'year' => ['required', 'numeric', 'between:1990,2025'],
            'vin' => ['required', 'string', 'max:17'],
            'price' => ['required', 'numeric'],
            'mileage' => ['required', 'integer'],
            'interior_color' => ['required', 'string', 'max:10'],
            'exterior_color' => ['required', 'string', 'max:10'],
            'car_type_id' => ['required', 'integer', 'exists:App\Models\CarType,id'],
            'fuel_type_id' => ['required', 'integer', 'exists:App\Models\FuelType,id'],
            'user_id' => ['required', 'integer', 'exists:App\Models\User,id'],
            'state_id' => ['required', 'integer', 'exists:App\Models\State,id'],
            'city_id' => ['required', 'integer', 'exists:App\Models\City,id'],
            'address' => ['required', 'string', 'max:255'],
            //'phone' => ['required', 'string', 'max:10'],
            'phone' => new PhoneRule(), // Custom rule

            'features' => 'array',
            'features.*' => 'string',
            'images' => 'array',
            'images.*' => File::image()
                ->max(config('image.max_size')),
                /*->dimensions(Rule::dimensions()
                    ->maxWidth(config('image.max_width'))
                    ->maxHeight(config('image.max_height'))
                ),*/

            'description' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'created_at' => ['nullable', 'date'],
            'updated_at' => ['nullable', 'date'],
            'deleted_at' => ['nullable', 'date']
        ];
    }
}
