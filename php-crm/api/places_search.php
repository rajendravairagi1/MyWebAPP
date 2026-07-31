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

// Filter thresholds (weak leads = better prospects for GMB optimization pitch)
$minReviews = (int) getSetting('min_reviews_threshold', '10');
$minRating = (float) getSetting('min_rating_threshold', '4.0');
$maxResults = (int) getSetting('max_search_results', '10');
if ($maxResults < 1) { $maxResults = 10; }

$allResults = [];
foreach ($data['places'] ?? [] as $r) {
    $allResults[] = [
        'place_id' => $r['id'] ?? null,
        'name' => $r['displayName']['text'] ?? '',
        'address' => $r['formattedAddress'] ?? '',
        'rating' => $r['rating'] ?? null,
        'reviews_count' => (int) ($r['userRatingCount'] ?? 0),
    ];
}
$totalFromGoogle = count($allResults);

// Keep only "weak" leads: reviews below threshold OR rating below threshold.
$weakResults = array_values(array_filter($allResults, function ($r) use ($minReviews, $minRating) {
    $lowReviews = $r['reviews_count'] < $minReviews;
    $lowRating = $r['rating'] !== null && (float) $r['rating'] > 0 && (float) $r['rating'] < $minRating;
    return $lowReviews || $lowRating;
}));

// Weakest first: lowest reviews on top, then lowest rating.
usort($weakResults, function ($a, $b) {
    if ($a['reviews_count'] !== $b['reviews_count']) {
        return $a['reviews_count'] <=> $b['reviews_count'];
    }
    return (float) ($a['rating'] ?? 5) <=> (float) ($b['rating'] ?? 5);
});

$results = array_slice($weakResults, 0, $maxResults);

$pdo = getDb();
$stmt = $pdo->prepare('INSERT INTO search_history (query, results_count) VALUES (?, ?)');
$stmt->execute([$query, count($results)]);

apiJson([
    'ok' => true,
    'results' => $results,
    'stats' => [
        'total_from_google' => $totalFromGoogle,
        'weak_matches' => count($weakResults),
        'shown' => count($results),
        'min_reviews' => $minReviews,
        'min_rating' => $minRating,
    ],
]);
