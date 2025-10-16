<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\State;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        CarType::factory()
            ->count(9)
            ->sequence(
                ['name' => 'Sedan'],
                ['name' => 'Hatchback'],
                ['name' => 'SUV'],
                ['name' => 'Pickup Truck'],
                ['name' => 'Minivan'],
                ['name' => 'Jeep'],
                ['name' => 'Coupe'],
                ['name' => 'Crossover'],
                ['name' => 'Sport Car']
            )
            ->create();

        FuelType::factory()
            ->count(4)
            ->sequence(
                ['name' => 'Gasoline'],
                ['name' => 'Diesel'],
                ['name' => 'Electric'],
                ['name' => 'Hybrid'],
            )
            ->create();

        $statesAndCities = [
            'Alabama' => ['Birmingham', 'Montgomery', 'Huntsville', 'Mobile'],
            'Alaska' => ['Anchorage', 'Fairbanks', 'Juneau', 'Sitka'],
            'Arizona' => ['Phoenix', 'Tucson', 'Mesa', 'Chandler'],
            'Arkansas' => ['Little Rock', 'Fort Smith', 'Fayetteville', 'Jonesboro'],
            'California' => ['Los Angeles', 'San Francisco', 'San Diego', 'Sacramento'],
            'Colorado' => ['Denver', 'Colorado Springs', 'Aurora', 'Fort Collins'],
            'Connecticut' => ['Hartford', 'Bridgeport', 'New Haven', 'Stamford'],
            'Delaware' => ['Wilmington', 'Dover', 'Newark', 'Middletown'],
            'Florida' => ['Miami', 'Orlando', 'Tampa', 'Jacksonville'],
            'Georgia' => ['Atlanta', 'Savannah', 'Augusta', 'Columbus'],
            'Hawaii' => ['Honolulu', 'Hilo', 'Kailua', 'Kaneohe'],
            'Idaho' => ['Boise', 'Nampa', 'Meridian', 'Idaho Falls'],
            'Illinois' => ['Chicago', 'Aurora', 'Naperville', 'Rockford'],
            'Indiana' => ['Indianapolis', 'Fort Wayne', 'Evansville', 'South Bend'],
            'Iowa' => ['Des Moines', 'Cedar Rapids', 'Davenport', 'Sioux City'],
            'Kansas' => ['Wichita', 'Overland Park', 'Kansas City', 'Topeka'],
            'Kentucky' => ['Louisville', 'Lexington', 'Bowling Green', 'Covington'],
            'Louisiana' => ['New Orleans', 'Baton Rouge', 'Shreveport', 'Lafayette'],
            'Maine' => ['Portland', 'Augusta', 'Bangor', 'South Portland'],
            'Maryland' => ['Baltimore', 'Annapolis', 'Frederick', 'Gaithersburg'],
            'Massachusetts' => ['Boston', 'Worcester', 'Springfield', 'Cambridge'],
            'Michigan' => ['Detroit', 'Grand Rapids', 'Warren', 'Sterling Heights'],
            'Minnesota' => ['Minneapolis', 'Saint Paul', 'Rochester', 'Bloomington'],
            'Mississippi' => ['Jackson', 'Gulfport', 'Southaven', 'Biloxi'],
            'Missouri' => ['Kansas City', 'St. Louis', 'Springfield', 'Columbia'],
            'Montana' => ['Billings', 'Missoula', 'Great Falls', 'Bozeman'],
            'Nebraska' => ['Omaha', 'Lincoln', 'Bellevue', 'Grand Island'],
            'Nevada' => ['Las Vegas', 'Henderson', 'Reno', 'North Las Vegas'],
            'New Hampshire' => ['Manchester', 'Nashua', 'Concord', 'Derry'],
            'New Jersey' => ['Newark', 'Jersey City', 'Paterson', 'Elizabeth'],
            'New Mexico' => ['Albuquerque', 'Santa Fe', 'Las Cruces', 'Rio Rancho'],
            'New York' => ['New York City', 'Buffalo', 'Rochester', 'Yonkers'],
            'North Carolina' => ['Charlotte', 'Raleigh', 'Greensboro', 'Durham'],
            'North Dakota' => ['Fargo', 'Bismarck', 'Grand Forks', 'Minot'],
            'Ohio' => ['Columbus', 'Cleveland', 'Cincinnati', 'Toledo'],
            'Oklahoma' => ['Oklahoma City', 'Tulsa', 'Norman', 'Broken Arrow'],
            'Oregon' => ['Portland', 'Salem', 'Eugene', 'Gresham'],
            'Pennsylvania' => ['Philadelphia', 'Pittsburgh', 'Allentown', 'Erie'],
            'Rhode Island' => ['Providence', 'Warwick', 'Cranston', 'Pawtucket'],
            'South Carolina' => ['Columbia', 'Charleston', 'Greenville', 'North Charleston'],
            'South Dakota' => ['Sioux Falls', 'Rapid City', 'Aberdeen', 'Brookings'],
            'Tennessee' => ['Nashville', 'Memphis', 'Knoxville', 'Chattanooga'],
            'Texas' => ['Houston', 'San Antonio', 'Dallas', 'Austin'],
            'Utah' => ['Salt Lake City', 'Provo', 'West Valley City', 'Sandy'],
            'Vermont' => ['Burlington', 'Essex', 'South Burlington', 'Rutland'],
            'Virginia' => ['Virginia Beach', 'Norfolk', 'Chesapeake', 'Richmond'],
            'Washington' => ['Seattle', 'Spokane', 'Tacoma', 'Vancouver'],
            'West Virginia' => ['Charleston', 'Huntington', 'Morgantown', 'Parkersburg'],
            'Wisconsin' => ['Milwaukee', 'Madison', 'Green Bay', 'Kenosha'],
            'Wyoming' => ['Cheyenne', 'Casper', 'Laramie', 'Gillette'],
        ];

        foreach ($statesAndCities as $state => $cities) {
            State::factory()
                ->state(['name' => $state])
                ->has(City::factory()
                    ->count(count($cities))
                    ->sequence(
                        ...array_map(fn ($city) => ['name' => $city], $cities)
                    )
                )
                ->create();
        }

        $carMakersAndModels = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Highlander'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot'],
            'Ford' => ['F-150', 'Mustang', 'Explorer', 'Escape'],
            'Chevrolet' => ['Silverado', 'Malibu', 'Equinox', 'Tahoe'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Murano'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe'],
            'Kia' => ['Optima', 'Sportage', 'Soul', 'Seltos'],
            'Volkswagen' => ['Jetta', 'Passat', 'Tiguan', 'Atlas'],
            'Subaru' => ['Outback', 'Forester', 'Impreza', 'Crosstrek'],
            'BMW' => ['3 Series', '5 Series', 'X5', 'X3'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'GLC', 'GLE'],
            'Audi' => ['A3', 'A4', 'Q5', 'Q7'],
            'Lexus' => ['ES', 'RX', 'NX', 'IS'],
            'Mazda' => ['Mazda3', 'Mazda6', 'CX-5', 'CX-30'],
            'Dodge' => ['Charger', 'Challenger', 'Durango', 'Journey'],
            'Chrysler' => ['300', 'Pacifica', 'Voyager', 'Aspen'],
            'Ram' => ['1500', '2500', '3500', 'ProMaster'],
            'Volvo' => ['S60', 'S90', 'XC60', 'XC90'],
            'Jaguar' => ['XE', 'XF', 'F-PACE', 'E-PACE'],
            'Land Rover' => ['Range Rover', 'Discovery', 'Evoque', 'Defender'],
        ];

        foreach ($carMakersAndModels as $maker => $models) {
            Maker::factory()
                ->state(['name' => $maker])
                ->has(Model::factory()
                    ->count(count($models))
                    ->sequence(
                        ...array_map(fn ($model) => ['name' => $model], $models)
                    )
                )
                ->create();
        }

        User::factory()
            ->count(3)
            ->create();

        User::factory()
            ->count(2)
            ->has(Car::factory()
                ->count(50)
                ->has(CarImage::factory()
                    ->count(5)
                    ->sequence(
                        fn (Sequence $sequence) => ['position' => $sequence->index % 5 + 1]
                    ),
                    /*->sequence(
                        ['position' => 1],
                        ['position' => 2],
                        ['position' => 3],
                        ['position' => 4],
                        ['position' => 5]
                    ),*/
                    'images'
                )
                ->hasFeatures(),
                'favoriteCars'
            )
            ->create();
    }
}
