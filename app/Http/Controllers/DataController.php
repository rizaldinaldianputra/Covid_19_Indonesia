<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Charts\CovidChart;
use Illuminate\Support\Carbon;

class DataController extends Controller
{
    public function requestIndonesia()
    {
        $suspect = collect(Http::get('https://api.kawalcorona.com/indonesia/provinsi')->json());
        $suspectindonesia = collect(Http::get('https://api.kawalcorona.com/indonesia')->json());

        $suspectData = $suspect->flatten(1); // menghilangkan satu object

        $lables = $suspectData->pluck('Provinsi'); // fluck adalah mengambil key dri array tersebut
        $data = $suspectData->pluck('Kasus_Posi');

        $sembuh = $suspectindonesia->pluck('sembuh');
        $meninggal = $suspectindonesia->pluck('meninggal');
        $positif = $suspectindonesia->pluck('positif');

        $colors = $lables->map(function ($item) {
            return '#' . substr(md5(mt_rand()), 0, 6); // memberikan warna pada chart
        });

        $chart = new CovidChart;
        $chart->labels($lables);
        $chart->dataset('Data Kasus Covid-19', 'pie', $data)->backgroundColor($colors);

        return view('welcome', [
            'chart' => $chart,
            'lables' => $lables,
            'sembuh' => $sembuh,
            'meninggal' => $meninggal,
            'positif' => $positif,
        ]);
    }

    public function boot()
    {
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
    }
}
