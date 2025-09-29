@props(['cars'])

<x-app-layout title="Manage Images for {{$car->getTitle()}}" bodyClass="page-my-cars">
    <main>
        <div>
            <div class="container">
                <h1 class="page-title">Manage Images for {{$car->getTitle()}}</h1>
                <div class="car-images-wrapper">
                    <form method="POST" action="{{route('car.updateImages', $car)}}" class="card p-medium form-update-images">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Position</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>

                            @forelse($car->images as $image)
                                <tr>
                                    <td>
                                        <img
                                            src="{{$image?->getUrl() ?: '/img/no_image.png'}}"
                                            alt="{{$image->position}}"
                                            class="my-cars-img-thumbnail"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="positions[{{$image->id}}]"
                                            value="{{old('positions.'.$image->id, $image->position)}}"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="delete_images[]"
                                            id="delete_image_{{$image->id}}"
                                            value="{{$image->id}}"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-large">
                                        There are no images for this car. <a href="{{ route('car.edit', $car) }}">Car Edit</a>
                                    </td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                    </div>
                        <div class="p-medium">
                            <div class="flex justify-end">
                                <button class="btn btn-primary">Update Images</button>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="{{route('car.addImages', $car)}}"
                          enctype="multipart/form-data"
                          class="card p-medium form-images mb-large">
                        @csrf
                        <div class="form-images">
                            @foreach($errors->get('images.*') as $imageErrors)
                                @foreach($imageErrors as $errorMessage)
                                    <div class="text-error mb-small">{{$errorMessage}}</div>
                                @endforeach
                            @endforeach
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
                                <input id="carFormImageUpload" type="file" name="images[]" multiple/>
                            </div>

                            <div id="imagePreviews" class="car-form-images"></div>

                            <div class="p-medium">
                                <div class="flex justify-end">
                                    <button class="btn btn-primary">Add Images</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
