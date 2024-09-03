<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WeatherStatisticsController extends Controller
{
    public function exportStatisticsToExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Weather data");

        $headers = ["Day of Week", "Minimum Temperature (°C)", "Average Wind Speed (km/h)", "Top Precipitation (mm)", "Average H2O (g/kg)"];
        $sheet->fromArray($headers);

        $row = 2;
        $weatherData = $this->getWeatherStatistics();
        foreach ($weatherData as $day => $data){
            $sheet->setCellValue('A' . $row, $day);
            $sheet->setCellValue('B' . $row, $data['min_temperature']);
            $sheet->setCellValue('C' . $row, $data['average_wind_speed']);
            $sheet->setCellValue('D' . $row, $data['top_precipitation']);
            $sheet->setCellValue('E' . $row, $data['avg_grams_h2o']);

            $row++;
        }

        $this->formatSheet($sheet);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="weather_data.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    protected function formatSheet($sheet): void
    {
        // format the headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_DARKRED],
                'name' => 'Arial'
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // set up columns' width with automatic width calculation
        foreach (range('A', $sheet->getHighestColumn()) as $column){
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // set up the page orientation to landscape
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        // set up the page size to A4
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

        // center the columns' values
        $sheet->getStyle('B2:E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function getWeatherStatistics(): array
    {
        $weatherStatistics = DB::table('measurements')
            ->join('parameters', 'measurements.parameter_id', '=', 'parameters.id')
            ->join('measurement_days', 'measurements.measurement_day_id', '=', 'measurement_days.id')
            ->selectRaw('
            DAYNAME(measurement_days.date) as day_of_week,
            MIN(CASE WHEN parameters.name = "Temperature" THEN measurements.value END) as min_temperature,
            AVG(CASE WHEN parameters.name = "Wind Speed" THEN measurements.value END) as avg_wind_speed,
            MAX(CASE WHEN parameters.name = "Precipitation" THEN measurements.value END) as max_precipitation,
            AVG(CASE WHEN parameters.name = "Humidity" THEN measurements.value END) as avg_humidity
            ')
            ->groupBy('day_of_week')
            ->get();

        $weatherData = [];
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
            $dayData = $weatherStatistics->firstWhere('day_of_week', $day);
            $weatherData[$day] = [
                'min_temperature' => $dayData->min_temperature ?? null,
                'average_wind_speed' => $dayData->avg_wind_speed ?? null,
                'top_precipitation' => $dayData->max_precipitation ?? null,
                'avg_grams_h2o' => $this->calculateAverageH2OinAir($dayData->avg_humidity, $dayData->min_temperature) ?? null,
            ];
        }
        return $weatherData;
    }

    protected function calculateAverageH2OinAir(int $averageHumidity, int $temperature): float
    {
        return ($averageHumidity * 0.42 * pow(M_E, (10 * $temperature * 0.006235398))) * 0.1;
    }
}
