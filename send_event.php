<?php
// Facebook CAPI Server-Side Implementation
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Replace with your credentials
    $pixel_id = '1257574952577599'; // Your Dataset ID/Pixel ID
    $access_token = 'EAAciLfTSZB5oBPGbIzx6e3MZAkumAEXqwvltrxCDAw7EQJe2QgB54QNiP3kZC3FmWJ6ZA9VnzHT59vORFHAVNhyf6sjNMjBAjPBm06eicTtK0nDifvnw11Jns7vph5AK1PTvrFK1uqU4s0LGiEwdxZC3z90lLiG9gayjZAHr1NPa5ySLUkQo4DabhUDhw0nrUy4gZDZD'; // Your access token

    // Extract POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($input['event_name']) || empty($input['page_url'])) {
        echo json_encode(['error' => 'Missing required fields: event_name or page_url']);
        exit;
    }

    // Prepare event data for Facebook
    $event_data = [
        'data' => [
            [
                'event_name' => $input['event_name'],
                'event_time' => time(),
                'event_source_url' => $input['page_url'],
                'user_data' => [
                    'client_ip_address' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'],
                    'client_user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'fbp' => $input['fbp'] ?? null, // Facebook Browser ID
                    'fbc' => $input['fbc'] ?? null   // Facebook Click ID
                ],
                'custom_data' => $input['custom_data'] ?? [] // Additional event data (e.g., value, currency)
            ]
        ]
    ];

    // Send to Facebook's API
    $url = "https://graph.facebook.com/v19.0/{$pixel_id}/events?access_token={$access_token}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($event_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response; // Return Facebook's response
}
?>