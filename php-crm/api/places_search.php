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

// Ideal prospect = few reviews (weak online presence) BUT good rating
// (they do good work, just haven't collected reviews - easy to pitch GMB
// optimization / review-collection service).
$maxReviews = (int) getSetting('max_reviews_threshold', '10');
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

// Keep leads where reviews <= max AND rating >= min.
// If a place has no rating yet (null / 0), keep it - low reviews is enough.
$matches = array_values(array_filter($allResults, function ($r) use ($maxReviews, $minRating) {
    $reviewsOk = $r['reviews_count'] <= $maxReviews;
    $ratingVal = $r['rating'] !== null ? (float) $r['rating'] : 0.0;
    $ratingOk = $ratingVal === 0.0 || $ratingVal >= $minRating;
    return $reviewsOk && $ratingOk;
}));

// Best prospects first: fewest reviews on top, then highest rating.
usort($matches, function ($a, $b) {
    if ($a['reviews_count'] !== $b['reviews_count']) {
        return $a['reviews_count'] <=> $b['reviews_count'];
    }
    return (float) ($b['rating'] ?? 0) <=> (float) ($a['rating'] ?? 0);
});

$results = array_slice($matches, 0, $maxResults);

$pdo = getDb();
$stmt = $pdo->prepare('INSERT INTO search_history (query, results_count) VALUES (?, ?)');
$stmt->execute([$query, count($results)]);

apiJson([
    'ok' => true,
    'results' => $results,
    'stats' => [
        'total_from_google' => $totalFromGoogle,
        'matches' => count($matches),
        'shown' => count($results),
        'max_reviews' => $maxReviews,
        'min_rating' => $minRating,
    ],
]);
