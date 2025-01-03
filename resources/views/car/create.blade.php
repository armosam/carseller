<x-app-layout>
    <main>
        <div class="container-small">
            <h1 class="car-details-page-title">Add new car</h1>
            <form
                action="/car"
                method="POST"
                enctype="multipart/form-data"
                class="card add-new-car-form"
            >
                @csrf
                <input type="hidden" name="user_id" value="{{Auth()->id()}}">
                <div class="form-content">
                    <div class="form-details">
                        <div class="row">
                            <div class="col">
                                <x-form.dropdown label="Maker" name="maker_id" id="makerSelect" :value="old('maker_id')">
                                    <option value="">Maker</option>
                                    @foreach(\App\Models\Maker::query()->get() as $maker)
                                        <option
                                            value="{{$maker->id}}" @selected(old('maker_id') == $maker->id)>{{$maker->name}}</option>
                                    @endforeach
                                </x-form.dropdown>
                            </div>
                            <div class="col">
                                <x-form.dropdown label="Model" name="model_id" id="modelSelect">
                                    <option value="">Model</option>
                                    @foreach(\App\Models\Model::query()->get() as $model)
                                        <option
                                            value="{{$model->id}}" data-parent="{{$model->maker_id}}" style="display: none" @selected(old('model_id') == $model->id)>{{$model->name}}</option>
                                    @endforeach
                                </x-form.dropdown>
                            </div>
                            <div class="col">
                                <x-form.dropdown label="Year" name="year">
                                    <option value="">Year</option>
                                    @foreach(range(2025, 1990, -1) as $year)
                                        <option value="{{$year}}" @selected(old('year') == $year)>{{$year}}</option>
                                    @endforeach
                                </x-form.dropdown>
                            </div>
                        </div>
                        <x-form.radio-group name="car_type_id" label="Car Type">
                            <div class="row">
                                @foreach(\App\Models\CarType::query()->get() as $fuelType)
                                    <div class="col">
                                        <label class="inline-radio">
                                            <input type="radio" name="car_type_id"
                                                   value="{{$fuelType->id}}" @checked(old('car_type_id') == $fuelType->id) />
                                            {{$fuelType->name}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </x-form.radio-group>
                        <div class="row">
                            <div class="col">
                                <x-form.input type="number" label="Price" name="price" placeholder="Price"
                                              :value="old('price')"/>
                            </div>
                            <div class="col">
                                <x-form.input label="VIN Number" name="vin" placeholder="VIN Number"
                                              :value="old('vin')"/>
                            </div>
                            <div class="col">
                                <x-form.input type="number" label="Mileage" name="mileage" placeholder="Mileage"
                                              :value="old('mileage')"/>
                            </div>
                        </div>

                        <x-form.radio-group name="fuel_type_id" label="Fuel Type">
                            <div class="row">
                                @foreach(\App\Models\FuelType::query()->get() as $fuelType)
                                    <div class="col">
                                        <label class="inline-radio">
                                            <input type="radio" name="fuel_type_id"
                                                   value="{{$fuelType->id}}" @checked(old('fuel_type_id') == $fuelType->id) />
                                            {{$fuelType->name}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </x-form.radio-group>

                        <div class="row">
                            <div class="col">
                                <x-form.input label="Interior Color" name="interior_color" placeholder="Interior Color"
                                              :value="old('interior_color')"/>
                            </div>
                            <div class="col">
                                <x-form.input label="Exterior Color" name="exterior_color" placeholder="Exterior Color"
                                              :value="old('exterior_color')"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <x-form.dropdown label="State/Region" name="state_id" id="stateSelect">
                                    <option value="">State</option>
                                    @foreach(\App\Models\State::query()->get() as $state)
                                        <option value="{{$state->id}}" @selected(old('state_id') == $state->id)>{{$state->name}}</option>
                                    @endforeach
                                </x-form.dropdown>
                            </div>
                            <div class="col">
                                <x-form.dropdown label="City" name="city_id" id="citySelect">
                                    <option value="">City</option>
                                    @foreach(\App\Models\City::query()->get() as $city)
                                        <option value="{{$city->id}}" data-parent="{{$city->state_id}}" style="display: none" @selected(old('city_id') == $city->id)>{{$city->name}}</option>
                                    @endforeach
                                </x-form.dropdown>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <x-form.input type="text" label="Address" name="address" placeholder="Address"
                                              :value="old('address')"/>
                            </div>
                            <div class="col">
                                <x-form.input type="tel" label="Phone" name="phone" placeholder="Phone"
                                              :value="old('phone')"/>
                            </div>
                        </div>
                        <x-form.checkbox-group name="feature">
                            <div class="row">
                                <div class="col">
                                    <?php $features = \App\Models\CarFeature::featuresList(); $num = count($features) / 2; ?>
                                    @foreach($features as $feature_name => $feature_label)
                                        @if($num == 0) </div><div class="col"> @endif
                                        <label class="checkbox">
                                            <input type="checkbox" name="{{$feature_name}}" value="1" @checked(old($feature_name) == 1) />
                                            {{$feature_label}}
                                        </label>
                                    <?php $num--; ?>
                                    @endforeach
                                </div>
                            </div>
                        </x-form.checkbox-group>

                        <div class="form-group">
                            <label>Detailed Description</label>
                            <textarea name="description" rows="10">{{old('description')}}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="checkbox">
                                <input type="checkbox" name="published" value="1" @checked(old('published') == 1)/>
                                Published
                            </label>
                        </div>
                    </div>
                    <div class="form-images">
                        <div class="form-image-upload">
                            <div class="upload-placeholder">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    style="width: 48px"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                            </div>
                            <input id="carFormImageUpload" type="file" multiple/>
                        </div>
                        <div id="imagePreviews" class="car-form-images"></div>
                    </div>
                </div>
                <div class="p-medium" style="width: 100%">
                    <div class="flex justify-end gap-1">
                        <x-form.button type="reset" class="btn btn-default">Reset</x-form.button>
                        <x-form.button class="btn btn-primary">Submit</x-form.button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</x-app-layout>
