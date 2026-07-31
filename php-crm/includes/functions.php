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

/** Fetch a URL's HTML (best-effort, short timeout, follows redirects). */
function fetchHtml(string $url): ?string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; LeadCRM/1.0)',
    ]);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html ?: null;
}

function extractFirstEmail(string $html): ?string
{
    // Prefer mailto: links (more reliable than free-form text).
    if (preg_match('/mailto:\s*([a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})/i', $html, $m)) {
        return strtolower($m[1]);
    }

    // Fallback: any email-shaped string. Filter out common junk.
    if (preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $html, $matches)) {
        $junkDomains = ['sentry.io', 'wixpress.com', 'godaddy.com', 'example.com', 'sentry-next.wixpress.com',
                        'domain.com', 'yourdomain.com', 'email.com', 'gmail.png', 'gmail.jpg'];
        foreach ($matches[0] as $email) {
            $lower = strtolower($email);
            if (preg_match('/\.(png|jpg|jpeg|gif|svg|webp|ico)$/i', $lower)) continue;
            if (preg_match('/^[a-f0-9]{16,}@/', $lower)) continue; // hex-only local part = tracking id
            $domain = substr($lower, strpos($lower, '@') + 1);
            $skip = false;
            foreach ($junkDomains as $j) {
                if ($domain === $j || strpos($domain, $j) !== false) { $skip = true; break; }
            }
            if (!$skip) return $lower;
        }
    }
    return null;
}

function extractSocialLinks(string $html): array
{
    $out = ['facebook_url' => null, 'linkedin_url' => null, 'instagram_url' => null,
            'twitter_url' => null, 'youtube_url' => null];
    $patterns = [
        'facebook_url'  => '#https?://(?:www\.|m\.)?facebook\.com/[a-zA-Z0-9._\-/?=&%]+#i',
        'linkedin_url'  => '#https?://(?:www\.)?linkedin\.com/(?:company|in)/[a-zA-Z0-9._\-/?=&%]+#i',
        'instagram_url' => '#https?://(?:www\.)?instagram\.com/[a-zA-Z0-9._\-/?=&%]+#i',
        'twitter_url'   => '#https?://(?:www\.)?(?:twitter|x)\.com/[a-zA-Z0-9._\-/?=&%]+#i',
        'youtube_url'   => '#https?://(?:www\.)?youtube\.com/(?:@|c/|channel/|user/)?[a-zA-Z0-9._\-/?=&%]+#i',
    ];
    foreach ($patterns as $key => $regex) {
        if (preg_match($regex, $html, $m)) {
            // Skip sharer/intent URLs (e.g. facebook.com/sharer/... , twitter.com/intent/...)
            if (preg_match('#/(sharer|share|intent|dialog)#i', $m[0])) continue;
            $out[$key] = rtrim($m[0], '/');
        }
    }
    return $out;
}

/**
 * Fetch a website + likely contact/about pages and combine the scraped
 * emails and social links. Businesses very often hide the email on a
 * "Contact Us" page rather than the homepage.
 */
function analyzeWebsite(string $url): array
{
    $result = [
        'email' => null,
        'facebook_url' => null, 'linkedin_url' => null, 'instagram_url' => null,
        'twitter_url' => null, 'youtube_url' => null,
    ];
    if (empty($url)) return $result;

    $base = rtrim($url, '/');
    // Homepage first, then common contact/about routes.
    $paths = ['', '/contact', '/contact-us', '/contact.html', '/about', '/about-us', '/about.html'];
    $combinedHtml = '';
    foreach ($paths as $path) {
        $html = fetchHtml($base . $path);
        if ($html) {
            $combinedHtml .= "\n" . $html;
            // Stop early if we already have email + at least one social.
            if ($result['email'] === null) {
                $email = extractFirstEmail($html);
                if ($email) $result['email'] = $email;
            }
            $social = extractSocialLinks($html);
            foreach ($social as $k => $v) {
                if ($v && !$result[$k]) $result[$k] = $v;
            }
            if ($result['email'] && ($result['facebook_url'] || $result['instagram_url'])) break;
        }
    }
    // Final sweep on everything we collected, in case pieces were split.
    if ($result['email'] === null && $combinedHtml) {
        $result['email'] = extractFirstEmail($combinedHtml);
    }
    return $result;
}

/**
 * Substitute {{placeholder}} tokens in a string. Unknown tokens are left
 * as-is so the user can spot them.
 */
function renderTemplate(string $text, array $vars): string
{
    return preg_replace_callback('/\{\{\s*([\w]+)\s*\}\}/', function ($m) use ($vars) {
        $key = $m[1];
        return array_key_exists($key, $vars) ? (string) $vars[$key] : $m[0];
    }, $text);
}

/**
 * Build the full placeholder map used by email templates - lead data +
 * gaps (as a bullet list) + user's own contact info from Settings.
 */
function buildTemplateVars(array $lead, array $gaps = []): array
{
    $gapsList = '';
    if (!empty($gaps)) {
        foreach ($gaps as $g) {
            $gapsList .= '- ' . $g['gap_detail'] . "\n";
        }
        $gapsList = rtrim($gapsList);
    }

    $website = $lead['website'] ?? '';
    $websiteLine = $website ? $website : 'no website found';
    $gmb = $lead['google_profile_url'] ?? '';

    return [
        'company_name' => $lead['company_name'] ?? '',
        'address' => $lead['address'] ?? '',
        'phone' => $lead['phone'] ?? '',
        'email' => $lead['email'] ?? '',
        'website' => $website,
        'website_line' => $websiteLine,
        'google_profile_url' => $gmb,
        'gmb_url' => $gmb,
        'rating' => $lead['rating'] !== null && $lead['rating'] !== '' ? (string) $lead['rating'] : 'no rating',
        'reviews_count' => (string) (int) ($lead['reviews_count'] ?? 0),
        'facebook_url' => $lead['facebook_url'] ?? '',
        'instagram_url' => $lead['instagram_url'] ?? '',
        'linkedin_url' => $lead['linkedin_url'] ?? '',
        'gaps_list' => $gapsList,
        'my_name' => getSetting('my_name', ''),
        'my_email' => getSetting('my_email', ''),
        'my_phone' => getSetting('my_phone', ''),
        'my_portfolio' => getSetting('my_portfolio', ''),
        'my_company' => getSetting('my_company', ''),
    ];
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
