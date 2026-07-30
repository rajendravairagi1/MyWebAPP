<?php
require_once __DIR__ . '/bootstrap.php';

$query = trim($_GET['q'] ?? '');
if ($query === '') {
    apiJson(['ok' => false, 'error' => 'Query is required'], 400);
}

$apiKey = getSetting('google_places_api_key');
if ($apiKey === '') {
    apiJson(['ok' => false, 'error' => 'Google Places API key not set. Add it in Settings first.'], 400);
}

$url = 'https://maps.googleapis.com/maps/api/place/textsearch/json'
    . '?query=' . urlencode($query)
    . '&key=' . urlencode($apiKey);

$data = httpGetJson($url);

if (!$data || !isset($data['status'])) {
    apiJson(['ok' => false, 'error' => 'Could not reach Google Places API.'], 502);
}

if ($data['status'] !== 'OK' && $data['status'] !== 'ZERO_RESULTS') {
    apiJson(['ok' => false, 'error' => 'Google Places error: ' . $data['status']], 502);
}

$results = [];
foreach ($data['results'] ?? [] as $r) {
    $results[] = [
        'place_id' => $r['place_id'] ?? null,
        'name' => $r['name'] ?? '',
        'address' => $r['formatted_address'] ?? '',
        'rating' => $r['rating'] ?? null,
        'reviews_count' => $r['user_ratings_total'] ?? 0,
    ];
}

$pdo = getDb();
$stmt = $pdo->prepare('INSERT INTO search_history (query, results_count) VALUES (?, ?)');
$stmt->execute([$query, count($results)]);

apiJson(['ok' => true, 'results' => $results]);
