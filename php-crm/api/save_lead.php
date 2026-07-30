<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiJson(['ok' => false, 'error' => 'POST required'], 405);
}
apiVerifyCsrf();

if (getSetting('google_places_api_enabled', '1') !== '1') {
    apiJson(['ok' => false, 'error' => 'Google Places API is turned OFF in Settings. Turn it on first.'], 403);
}

$placeId = trim($_POST['place_id'] ?? '');
$searchQuery = trim($_POST['search_query'] ?? '');
if ($placeId === '') {
    apiJson(['ok' => false, 'error' => 'place_id is required'], 400);
}

$apiKey = getSetting('google_places_api_key');
if ($apiKey === '') {
    apiJson(['ok' => false, 'error' => 'Google Places API key not set. Add it in Settings first.'], 400);
}

$fields = 'name,formatted_address,formatted_phone_number,international_phone_number,website,rating,user_ratings_total,url';
$url = 'https://maps.googleapis.com/maps/api/place/details/json'
    . '?place_id=' . urlencode($placeId)
    . '&fields=' . urlencode($fields)
    . '&key=' . urlencode($apiKey);

$data = httpGetJson($url);
if (!$data || ($data['status'] ?? '') !== 'OK') {
    apiJson(['ok' => false, 'error' => 'Could not fetch place details: ' . ($data['status'] ?? 'unknown error')], 502);
}

$details = $data['result'];
$website = $details['website'] ?? null;

$webInfo = $website ? analyzeWebsite($website) : ['email' => null, 'facebook_url' => null, 'linkedin_url' => null, 'instagram_url' => null];

$lead = [
    'place_id' => $placeId,
    'company_name' => $details['name'] ?? 'Unknown',
    'address' => $details['formatted_address'] ?? null,
    'phone' => $details['formatted_phone_number'] ?? ($details['international_phone_number'] ?? null),
    'website' => $website,
    'email' => $webInfo['email'],
    'facebook_url' => $webInfo['facebook_url'],
    'linkedin_url' => $webInfo['linkedin_url'],
    'instagram_url' => $webInfo['instagram_url'],
    'google_profile_url' => $details['url'] ?? null,
    'reviews_count' => $details['user_ratings_total'] ?? 0,
    'rating' => $details['rating'] ?? null,
    'search_query' => $searchQuery,
];

$gaps = computeGaps($lead);
$urgency = urgencyScoreFromGaps($gaps);

$pdo = getDb();
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('
        INSERT INTO leads (place_id, company_name, address, phone, website, email, facebook_url, linkedin_url,
            instagram_url, google_profile_url, reviews_count, rating, search_query, urgency_score, status)
        VALUES (:place_id, :company_name, :address, :phone, :website, :email, :facebook_url, :linkedin_url,
            :instagram_url, :google_profile_url, :reviews_count, :rating, :search_query, :urgency_score, "analyzed")
        ON DUPLICATE KEY UPDATE
            company_name = VALUES(company_name), address = VALUES(address), phone = VALUES(phone),
            website = VALUES(website), email = VALUES(email), facebook_url = VALUES(facebook_url),
            linkedin_url = VALUES(linkedin_url), instagram_url = VALUES(instagram_url),
            google_profile_url = VALUES(google_profile_url), reviews_count = VALUES(reviews_count),
            rating = VALUES(rating), urgency_score = VALUES(urgency_score), updated_at = NOW()
    ');
    $stmt->execute([
        ':place_id' => $lead['place_id'],
        ':company_name' => $lead['company_name'],
        ':address' => $lead['address'],
        ':phone' => $lead['phone'],
        ':website' => $lead['website'],
        ':email' => $lead['email'],
        ':facebook_url' => $lead['facebook_url'],
        ':linkedin_url' => $lead['linkedin_url'],
        ':instagram_url' => $lead['instagram_url'],
        ':google_profile_url' => $lead['google_profile_url'],
        ':reviews_count' => $lead['reviews_count'],
        ':rating' => $lead['rating'],
        ':search_query' => $lead['search_query'],
        ':urgency_score' => $urgency,
    ]);

    $leadId = (int) $pdo->query('SELECT id FROM leads WHERE place_id = ' . $pdo->quote($placeId))->fetch()['id'];

    $pdo->prepare('DELETE FROM lead_gaps WHERE lead_id = ?')->execute([$leadId]);
    $gapStmt = $pdo->prepare('INSERT INTO lead_gaps (lead_id, gap_type, gap_detail) VALUES (?, ?, ?)');
    foreach ($gaps as $gap) {
        $gapStmt->execute([$leadId, $gap['gap_type'], $gap['gap_detail']]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    apiJson(['ok' => false, 'error' => 'Failed to save lead.'], 500);
}

apiJson(['ok' => true, 'lead_id' => $leadId]);
