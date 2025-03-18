<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        // Initialize default timings in case of API failure
        $timings = [
            'Fajr' => '05:30 AM',
            'Dhuhr' => '12:00 PM',
            'Asr' => '03:30 PM',
            'Maghrib' => '06:00 PM',
            'Isha' => '07:30 PM',
        ];

        try {
            // Get user's IP address
            $ip = $request->ip();

            // For local development, you might want to use a test IP
            if ($ip === '127.0.0.1' || $ip === '::1') {
                $ip = '41.42.43.44'; // Sample IP for Cairo, Egypt (for testing)
            }

            // Fetch user's location based on IP
            $location = Location::get($ip);

            // Default to Cairo if location cannot be determined
            $city = $location ? $location->cityName : 'Cairo';
            $country = $location ? $location->countryName : 'Egypt';

            // Initialize Guzzle client
            $client = new Client();

            // Fetch prayer times from Aladhan API
            $response = $client->get('http://api.aladhan.com/v1/timingsByCity', [
                'query' => [
                    'city' => $city,
                    'country' => $country,
                    'method' => 8,
                ],
                'timeout' => 10,
            ]);

            // Decode API response
            $data = json_decode($response->getBody(), true);

            // Check if the API returned valid data
            if (isset($data['data']['timings'])) {
                $timings = $data['data']['timings'];

                // Format the timings to 12-hour format with AM/PM
                $timings = array_map(function ($time) {
                    return date('g:i A', strtotime($time));
                }, $timings);
            } else {
                throw new \Exception('Invalid prayer times data received from API.');
            }

            $locationData = [
                'city' => $city,
                'country' => $country,
            ];
        } catch (\Exception $e) {
            Log::error('Prayer times fetch error: ' . $e->getMessage());

            // Pass an error message to the view
            return view('home', [
                'timings' => $timings,
                'errors' => new \Illuminate\Support\MessageBag(['msg' => 'تعذر جلب أوقات الصلاة. يرجى المحاولة لاحقًا.'])
            ]);
        }

        return view('home', compact('timings', 'locationData'));
    }
}
