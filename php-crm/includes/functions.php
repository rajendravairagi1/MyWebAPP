<?php
require_once __DIR__ . '/../db.php';

/** Escape for HTML output */
function h($str): string
{
    return htmlspecialchars((string) ($str ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $message = null)
{
    startSecureSession();
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}

// ---------- Settings ----------
function getSetting(string $key, string $default = ''): string
{
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function setSetting(string $key, string $value): void
{
    $pdo = getDb();
    $stmt = $pdo->prepare('INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}

// ---------- Status labels ----------
function statusLabel(string $status): string
{
    $labels = [
        'new' => 'New',
        'analyzed' => 'Analyzed',
        'contacted' => 'Contacted',
        'replied' => 'Replied',
        'follow_up' => 'Follow Up',
        'closed_won' => 'Closed (Won)',
        'closed_lost' => 'Closed (Lost)',
    ];
    return $labels[$status] ?? ucfirst($status);
}

function statusBadgeClass(string $status): string
{
    $classes = [
        'new' => 'badge-gray',
        'analyzed' => 'badge-blue',
        'contacted' => 'badge-orange',
        'replied' => 'badge-purple',
        'follow_up' => 'badge-yellow',
        'closed_won' => 'badge-green',
        'closed_lost' => 'badge-red',
    ];
    return $classes[$status] ?? 'badge-gray';
}

function allStatuses(): array
{
    return ['new', 'analyzed', 'contacted', 'replied', 'follow_up', 'closed_won', 'closed_lost'];
}

// ---------- Gap analysis ----------
/**
 * @param array $lead associative array with keys: website, email, reviews_count, rating,
 *                     facebook_url, linkedin_url, instagram_url
 * @return array list of ['gap_type' => ..., 'gap_detail' => ...]
 */
function computeGaps(array $lead): array
{
    $gaps = [];
    $minReviews = (int) getSetting('max_reviews_threshold', getSetting('min_reviews_threshold', '10'));
    $minRating = (float) getSetting('min_rating_threshold', '4.0');

    if (empty($lead['website'])) {
        $gaps[] = ['gap_type' => 'no_website', 'gap_detail' => 'No website listed'];
    }
    if (empty($lead['email'])) {
        $gaps[] = ['gap_type' => 'no_email', 'gap_detail' => 'No email found'];
    }
    if (isset($lead['reviews_count']) && (int) $lead['reviews_count'] < $minReviews) {
        $gaps[] = ['gap_type' => 'low_reviews', 'gap_detail' => 'Only ' . (int) $lead['reviews_count'] . ' reviews'];
    }
    if (isset($lead['rating']) && $lead['rating'] !== null && (float) $lead['rating'] < $minRating && (float) $lead['rating'] > 0) {
        $gaps[] = ['gap_type' => 'low_rating', 'gap_detail' => 'Rating is only ' . $lead['rating']];
    }
    if (empty($lead['facebook_url']) && empty($lead['linkedin_url']) && empty($lead['instagram_url'])) {
        $gaps[] = ['gap_type' => 'no_social', 'gap_detail' => 'No social media profiles found'];
    }

    return $gaps;
}

function urgencyScoreFromGaps(array $gaps): int
{
    return min(5, count($gaps));
}

// ---------- HTTP helper (cURL) ----------
function httpGetJson(string $url): ?array
{
    return httpJsonRequest('GET', $url);
}

/**
 * Generic JSON HTTP request, used for the Places API (New) which needs
 * custom headers (X-Goog-Api-Key / X-Goog-FieldMask) and POST bodies.
 */
function httpJsonRequest(string $method, string $url, array $headers = [], ?array $body = null): ?array
{
    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'LeadCRM/1.0',
        CURLOPT_HTTPHEADER => $headers,
    ];
    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        $opts[CURLOPT_POSTFIELDS] = json_encode($body ?? []);
    }
    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $error) {
        return null;
    }
    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

/**
 * Fetch a website's homepage HTML and try to extract an email address
 * and social media profile links. Best-effort only.
 */
function analyzeWebsite(string $url): array
{
    $result = ['email' => null, 'facebook_url' => null, 'linkedin_url' => null, 'instagram_url' => null];

    if (empty($url)) {
        return $result;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; LeadCRM/1.0)',
    ]);
    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) {
        return $result;
    }

    if (preg_match('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $html, $m)) {
        // Skip obvious placeholder/image-related matches
        if (!preg_match('/\.(png|jpg|jpeg|gif|svg|webp)$/i', $m[0])) {
            $result['email'] = $m[0];
        }
    }
    if (preg_match('#https?://(www\.)?facebook\.com/[^\s"\'<>]+#i', $html, $m)) {
        $result['facebook_url'] = $m[0];
    }
    if (preg_match('#https?://(www\.)?linkedin\.com/[^\s"\'<>]+#i', $html, $m)) {
        $result['linkedin_url'] = $m[0];
    }
    if (preg_match('#https?://(www\.)?instagram\.com/[^\s"\'<>]+#i', $html, $m)) {
        $result['instagram_url'] = $m[0];
    }

    return $result;
}

// ---------- CSV export ----------
function outputLeadsCsv(array $leads): void
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="leads_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Company Name', 'Phone', 'WhatsApp', 'Email', 'Website', 'Address', 'Reviews', 'Rating', 'Status', 'Urgency Score', 'Created At']);
    foreach ($leads as $lead) {
        fputcsv($out, [
            $lead['company_name'], $lead['phone'], $lead['whatsapp_number'], $lead['email'],
            $lead['website'], $lead['address'], $lead['reviews_count'], $lead['rating'],
            statusLabel($lead['status']), $lead['urgency_score'], $lead['created_at'],
        ]);
    }
    fclose($out);
    exit;
}
