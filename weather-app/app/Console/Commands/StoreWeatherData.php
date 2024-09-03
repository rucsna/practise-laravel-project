<?php

namespace App\Console\Commands;


use App\Models\Measurement;
use App\Models\MeasurementDay;
use App\Models\Parameter;
use Illuminate\Support\Facades\DB;

class StoreWeatherData extends OutputCommand
{
    protected $signature = 'store:weather:data';
    protected $description = 'Copy data from weather_data.json file into the database.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $this->output("Copying data from weather_data.json file...");

            $weatherData = $this->readDataFromJsonFile();

            foreach ($weatherData as $data) {
                $measurementDate = $data['measurement_date'];

                $measurementDay = MeasurementDay::firstOrCreate([
                    'date' => $measurementDate
                ]);

                foreach ($data as $key => $value) {
                    if ($key === 'measurement_date') continue;

                    $parameter = Parameter::firstOrCreate([
                        'name' => $this->getParameterName($key),
                        'unit' => $this->getParameterUnit($key)
                    ]);

                    Measurement::firstOrCreate([
                        'measurement_day_id' => $measurementDay->id,
                        'parameter_id' => $parameter->id,
                        'value' => $this->getNumericValue($value)
                    ]);
                }
            }
            DB::commit();
            $this->output("Weather data successfully copied to the database", self::MSG_SUCCESS);

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->output("An error occurred: " . $exception->getMessage(), self::MSG_ERROR);
        }
    }

    protected function readDataFromJsonFile()
    {
        $file_path = base_path('storage/data/weather_data.json');
        if(!file_exists($file_path) || !is_readable($file_path)){
            throw new \RuntimeException("Unable to read weather data file at $file_path.");
        }

        $json_data = json_decode(file_get_contents($file_path), true);
        if(!$json_data){
            throw new \RuntimeException("No available data");
        }
        return $json_data;
    }

    protected function getParameterName(string $key): string
    {
        $parameterMap = [
            'average_temperature_celsius' => 'Temperature',
            'average_precipitation_millimeter' => 'Precipitation',
            'average_humidity_percent' => 'Humidity',
            'average_wind_speed' => 'Wind Speed',
        ];

        return $parameterMap[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    protected function getParameterUnit(string $key): string
    {
        $unitMap = [
            'average_temperature_celsius' => '°C',
            'average_precipitation_millimeter' => 'mm',
            'average_humidity_percent' => '%',
            'average_wind_speed' => 'km/h',
        ];

        return $unitMap[$key] ?? '';
    }

    protected function getNumericValue($value): int
    {
        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}
