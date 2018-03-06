<?php

use GuzzleHttp\Client;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * GET request to get weather information of a day at a location
 */
Route::get('weather', function(Request $request) {
    $lat = $request->get('lat');
    $lng = $request->get('lng');
    $date = $request->get('day');
    if (!empty($date)) {
        $time = \Carbon\Carbon::parse($date)->timestamp;
        $response = current(DarkSky::location($lat, $lng)->atTime($time)->daily());
    } else {
        $response = DarkSky::location($lat, $lng)->currently();
    }

    return response()->json($response);
});

/**
 * POST request to send message to the IBM Conversation Bot, return the response from the Bot
 */
Route::post('send-message', function(Request $request) {
    // get IBM Conversation API configuration
    $workSpaceId = env('WATSON_CONVERSATION_WORKSPACE_ID', '');
    $baseUrl = env('WATSON_CONVERSATION_BASE_URL', '');
    $username = env('WATSON_CONVERSATION_API_CREDENTIAL_USERNAME', '');
    $password = env('WATSON_CONVERSATION_API_CREDENTIAL_PASSWORD', '');

    // get the inputted message
    $msg = $request->get('msg');

    // build params for the request
    $params = [
        'input' => [
            'text' => $msg
        ]
    ];

    $context = $request->get('context');
    if ($context) {
        $params['context'] = $context;
    }

    // send request
    $client = new Client();
    $response = $client->request('POST', $baseUrl . '/v1/workspaces/' . $workSpaceId . '/message/',
        [
            'json' => $params,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'query' => [
                'version' => date('Y-m-d') // get today version
            ],
            'auth' => [$username, $password]
        ]
    );

    // return the response from the API to client
    return response($response->getBody()->getContents(), $response->getStatusCode(),
        ['Content-Type' => $response->getHeader('Content-Type')]);
});

/**
 * GET request to get activities or celebrations in a city
 */
Route::get('activities-celebrations', function(Request $request) {
    // get IBM Conversation API configuration
    $baseUrl = env('WATSON_DISCOVERY_BASE_URL', '');
    $environmentId = env('WATSON_DISCOVERY_ENVIRONMENT', '');
    $collectionId = env('WATSON_DISCOVERY_COLLECTION_ID', '');
    $username = env('WATSON_DISCOVERY_API_CREDENTIAL_USERNAME', '');
    $password = env('WATSON_DISCOVERY_API_CREDENTIAL_PASSWORD', '');

    // get city name
    $city = $request->get('city');
    $tokens = explode(', ', $city);

    // able to search news based on category and keyword
    // ex: support user to search pop music news, then pop is the keyword, music is the category
    $category = $request->get('cat');
    $keyword = $request->get('keyword');

    $groups = [
        0 => [
            "enriched_text.concepts.text:\"celebration\"", // search news relating to celebration
            "enriched_text.entities.type:\"Location\"", // search news relating to a city
            "enriched_text.entities.disambiguation.name:\"$tokens[0]\""
        ],
        1 => [
            "enriched_text.concepts.text:\"activity\"", // search news relating to activity
            "enriched_text.entities.type:\"Location\"", // search news relating to a city
            "enriched_text.entities.disambiguation.name:\"$tokens[0]\""
        ]
    ];

    // add category and keyword to the query params
    if (!empty($category)) {
        foreach ($groups as $key => $group) {
            $groups[$key][] = "enriched_text.categories.label:\"$category\"";
        }
    }

    if (!empty($keyword)) {
        foreach ($groups as $key => $group) {
            $groups[$key][] = "enriched_text.keywords.text:\"$keyword\"";
        }
    }

    // build query string for the search news API
    $queryGroups = [];
    foreach ($groups as $group) {
        $queryGroups[] = '(' . implode(',', $group) . ')';
    }
    $query = implode('|', $queryGroups);

    $params = [
        'deduplicate' => 'true',
        'query' => $query,
        'highlight' => 'true',
        'version' => date('Y-m-d'), // get today version
        'similar' => 'false',
        'count' => 20
    ];

    // send request
    $client = new Client();
    $response = $client->get($baseUrl . '/v1/environments/' . $environmentId . '/collections/' . $collectionId . '/query',
        [
            'query' => $params,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [$username, $password]
        ]
    );

    // return the response from the API to client
    return response($response->getBody()->getContents(), $response->getStatusCode(),
        ['Content-Type' => $response->getHeader('Content-Type')]);
});

/**
 * GET request to search general documents when user input an out of scope question
 */
Route::get('general-articles', function(Request $request) {
    $query = $request->get('query');

    // get IBM Conversation API configuration
    $baseUrl = env('WATSON_DISCOVERY_BASE_URL', '');
    $environmentId = env('WATSON_DISCOVERY_ENVIRONMENT', '');
    $collectionId = env('WATSON_DISCOVERY_COLLECTION_ID', '');
    $username = env('WATSON_DISCOVERY_API_CREDENTIAL_USERNAME', '');
    $password = env('WATSON_DISCOVERY_API_CREDENTIAL_PASSWORD', '');

    // use natural language query
    $params = [
        'deduplicate' => 'true',
        'query' => $query,
        'highlight' => 'true',
        'version' => date('Y-m-d'), // get today version
        'similar' => 'false',
        'count' => 20
    ];

    // send request
    $client = new Client();
    $response = $client->get($baseUrl . '/v1/environments/' . $environmentId . '/collections/' . $collectionId . '/query',
        [
            'query' => $params,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [$username, $password]
        ]
    );

    // return the response from the API to client
    return response($response->getBody()->getContents(), $response->getStatusCode(),
        ['Content-Type' => $response->getHeader('Content-Type')]);
});