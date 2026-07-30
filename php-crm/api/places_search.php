<?php
require_once __DIR__ . '/bootstrap.php';

if (getSetting('google_places_api_enabled', '1') !== '1') {
    apiJson(['ok' => false, 'error' => 'Google Places API is turned OFF in Settings. Turn it on first.'], 403);
}

$query = trim($_GET['q'] ?? '');
if ($query === '') {
    apiJson(['ok' => false, 'error' => 'Query is required'], 400);
}

$apiKey = getSetting('google_places_api_key');
if ($apiKey === '') {
    apiJson(['ok' => false, 'error' => 'Google Places API key not set. Add it in Settings first.'], 400);
}

// Uses the Places API (New) Text Search endpoint.
$url = 'https://places.googleapis.com/v1/places:searchText';
$headers = [
    'Content-Type: application/json',
    'X-Goog-Api-Key: ' . $apiKey,
    'X-Goog-FieldMask: places.id,places.displayName,places.formattedAddress,places.rating,places.userRatingCount',
];
$data = httpJsonRequest('POST', $url, $headers, ['textQuery' => $query]);

if ($data === null) {
    apiJson(['ok' => false, 'error' => 'Could not reach Google Places API.'], 502);
}

if (isset($data['error'])) {
    $status = $data['error']['status'] ?? 'ERROR';
    $message = $data['error']['message'] ?? 'Unknown error';
    apiJson(['ok' => false, 'error' => "Google Places error: $status - $message"], 502);
}

$results = [];
foreach ($data['places'] ?? [] as $r) {
    $results[] = [
        'place_id' => $r['id'] ?? null,
        'name' => $r['displayName']['text'] ?? '',
        'address' => $r['formattedAddress'] ?? '',
        'rating' => $r['rating'] ?? null,
        'reviews_count' => $r['userRatingCount'] ?? 0,
    ];
}

$pdo = getDb();
$stmt = $pdo->prepare('INSERT INTO search_history (query, results_count) VALUES (?, ?)');
$stmt->execute([$query, count($results)]);

apiJson(['ok' => true, 'results' => $results]);
