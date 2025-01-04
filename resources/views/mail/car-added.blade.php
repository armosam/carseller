<x-mail::message>
The new car was created successfully.

Please click on the link to open the car detail page.

<x-mail::button :url="'/car/{{$car->id}}'">
View Car
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
