<?php
require_once __DIR__ . '/functions.php';

/**
 * Extract a clean domain (e.g. "example.com") from a full URL.
 * Returns null if the input can't be parsed.
 */
function extractDomain(?string $url): ?string
{
    if (!$url) return null;
    if (!preg_match('#^https?://#i', $url)) $url = 'http://' . $url;
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) return null;
    return preg_replace('/^www\./i', '', strtolower($host));
}

/**
 * Search Apollo for people at a given domain/company. Free search endpoint;
 * some emails may come back as null (locked - need reveal credits to unlock).
 */
function apolloFindContacts(?string $domain, string $companyName, string $apiKey): array
{
    if (empty($apiKey)) return ['ok' => false, 'error' => 'Apollo API key not set', 'contacts' => []];

    $ch = curl_init('https://api.apollo.io/api/v1/mixed_people/search');
    $body = [
        'person_titles' => ['owner', 'founder', 'co-founder', 'ceo', 'president',
                            'managing director', 'director', 'partner', 'principal',
                            'manager', 'general manager', 'operations manager'],
        'page' => 1,
        'per_page' => 10,
    ];
    if ($domain) {
        $body['q_organization_domains_list'] = [$domain];
    } elseif ($companyName) {
        $body['q_organization_name'] = $companyName;
    } else {
        return ['ok' => false, 'error' => 'Neither domain nor company name available', 'contacts' => []];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'x-api-key: ' . $apiKey,
        ],
    ]);
    $raw = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false) return ['ok' => false, 'error' => 'Network error: ' . $err, 'contacts' => []];

    $data = json_decode($raw, true);
    if (!is_array($data)) return ['ok' => false, 'error' => 'Invalid response from Apollo', 'contacts' => []];

    if ($status >= 400) {
        $msg = $data['error'] ?? $data['message'] ?? ('HTTP ' . $status);
        return ['ok' => false, 'error' => 'Apollo: ' . (is_string($msg) ? $msg : json_encode($msg)), 'contacts' => []];
    }

    $decisionMakerKeywords = [
        'owner', 'founder', 'co-founder', 'cofounder', 'ceo', 'chief executive',
        'president', 'managing director', 'director', 'partner', 'principal',
        'proprietor', 'chairman', 'md', 'coo', 'chief operating',
        'vp ', 'vice president', 'head of',
    ];

    $contacts = [];
    foreach (($data['people'] ?? []) as $p) {
        $email = $p['email'] ?? null;
        // Apollo returns "email_not_unlocked@domain.com" for locked emails.
        if ($email && stripos($email, 'email_not_unlocked') !== false) $email = null;

        $title = strtolower($p['title'] ?? '');
        if ($title !== '') {
            $isDecisionMaker = false;
            foreach ($decisionMakerKeywords as $kw) {
                if (strpos($title, $kw) !== false) { $isDecisionMaker = true; break; }
            }
            if (!$isDecisionMaker) continue;
        }

        $name = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: ($p['name'] ?? '');
        $contacts[] = [
            'name' => $name,
            'title' => $p['title'] ?? '',
            'email' => $email,
            'phone' => $p['sanitized_phone'] ?? ($p['phone_numbers'][0]['sanitized_number'] ?? null),
            'linkedin_url' => $p['linkedin_url'] ?? null,
            'source' => 'apollo',
        ];
    }
    return ['ok' => true, 'contacts' => $contacts];
}

/**
 * Hunter.io domain-search: all emails Hunter knows for a given domain,
 * with names and titles when available. One credit per lookup.
 */
function hunterFindContacts(?string $domain, string $apiKey): array
{
    if (empty($apiKey)) return ['ok' => false, 'error' => 'Hunter API key not set', 'contacts' => []];
    if (!$domain) return ['ok' => false, 'error' => 'No website domain for this lead', 'contacts' => []];

    $url = 'https://api.hunter.io/v2/domain-search?domain=' . urlencode($domain)
         . '&api_key=' . urlencode($apiKey) . '&limit=10';
    $data = httpGetJson($url);

    if (!$data) return ['ok' => false, 'error' => 'Network error reaching Hunter', 'contacts' => []];

    if (isset($data['errors'])) {
        $msg = is_array($data['errors']) ? ($data['errors'][0]['details'] ?? json_encode($data['errors'])) : $data['errors'];
        return ['ok' => false, 'error' => 'Hunter: ' . $msg, 'contacts' => []];
    }

    // Keywords that identify a real decision-maker vs a generic mailbox.
    $decisionMakerKeywords = [
        'owner', 'founder', 'co-founder', 'cofounder', 'ceo', 'chief executive',
        'president', 'managing director', 'director', 'partner', 'principal',
        'proprietor', 'chairman', 'chairwoman', 'chairperson', 'md', 'coo',
        'chief operating', 'vp ', 'vice president', 'head of',
    ];
    // Local-parts we always skip (generic role mailboxes, not people).
    $genericLocalParts = [
        'info', 'contact', 'hello', 'hi', 'support', 'help', 'admin', 'office',
        'sales', 'marketing', 'hr', 'careers', 'jobs', 'billing', 'accounts',
        'noreply', 'no-reply', 'donotreply', 'team', 'inquiries', 'enquiries',
        'service', 'services', 'general', 'mail', 'email',
    ];

    $contacts = [];
    foreach ((($data['data'] ?? [])['emails'] ?? []) as $e) {
        $name = trim(($e['first_name'] ?? '') . ' ' . ($e['last_name'] ?? ''));
        $title = strtolower($e['position'] ?? '');
        $email = strtolower($e['value'] ?? '');
        $localPart = $email ? substr($email, 0, strpos($email, '@')) : '';

        // 1) Drop generic role mailboxes (info@, sales@, hr@ etc).
        if ($localPart && in_array($localPart, $genericLocalParts, true)) continue;

        // 2) If title exists, only keep decision-maker titles.
        //    If title is missing but there's a real person name, keep it -
        //    Hunter sometimes has the name without the title.
        if ($title !== '') {
            $isDecisionMaker = false;
            foreach ($decisionMakerKeywords as $kw) {
                if (strpos($title, $kw) !== false) { $isDecisionMaker = true; break; }
            }
            if (!$isDecisionMaker) continue;
        } elseif ($name === '') {
            // No title and no name = useless, skip.
            continue;
        }

        $contacts[] = [
            'name' => $name,
            'title' => $e['position'] ?? '',
            'email' => $e['value'] ?? null,
            'phone' => $e['phone_number'] ?? null,
            'linkedin_url' => $e['linkedin'] ?? null,
            'source' => 'hunter',
        ];
    }
    return ['ok' => true, 'contacts' => $contacts];
}

/**
 * Serper.dev - Google search JSON API. Free tier 2500 searches/month.
 * Returns knowledgeGraph.profiles when Google shows the "Profiles" side
 * panel (Instagram/Facebook/LinkedIn/YouTube etc). Also parses organic
 * results as a fallback.
 */
function serperFindSocials(string $companyName, ?string $city, string $apiKey): array
{
    if (empty($apiKey)) return ['ok' => false, 'error' => 'Serper API key not set', 'socials' => []];
    if (empty($companyName)) return ['ok' => false, 'error' => 'Company name required', 'socials' => []];

    $query = $companyName;
    if ($city) $query .= ' ' . $city;

    $ch = curl_init('https://google.serper.dev/search');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['q' => $query, 'num' => 10]),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-API-KEY: ' . $apiKey,
        ],
    ]);
    $raw = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) return ['ok' => false, 'error' => 'Network error reaching Serper', 'socials' => []];
    $data = json_decode($raw, true);
    if (!is_array($data)) return ['ok' => false, 'error' => 'Invalid Serper response', 'socials' => []];
    if ($status >= 400) return ['ok' => false, 'error' => 'Serper: ' . ($data['message'] ?? "HTTP $status"), 'socials' => []];

    $socials = ['facebook_url' => null, 'instagram_url' => null, 'linkedin_url' => null,
                'twitter_url' => null, 'youtube_url' => null];

    $classify = function (string $url) use (&$socials) {
        $u = rtrim($url, '/');
        if (!$socials['facebook_url']  && preg_match('#https?://(?:www\.|m\.)?facebook\.com/[^/?#]+#i', $u, $m))  $socials['facebook_url']  = $m[0];
        if (!$socials['instagram_url'] && preg_match('#https?://(?:www\.)?instagram\.com/[^/?#]+#i', $u, $m))     $socials['instagram_url'] = $m[0];
        if (!$socials['linkedin_url']  && preg_match('#https?://(?:www\.)?linkedin\.com/(?:company|in)/[^/?#]+#i', $u, $m)) $socials['linkedin_url']  = $m[0];
        if (!$socials['twitter_url']   && preg_match('#https?://(?:www\.)?(?:twitter|x)\.com/[^/?#]+#i', $u, $m)) $socials['twitter_url']   = $m[0];
        if (!$socials['youtube_url']   && preg_match('#https?://(?:www\.)?youtube\.com/(?:@|c/|channel/|user/)[^/?#]+#i', $u, $m)) $socials['youtube_url']   = $m[0];
    };

    // 1) Knowledge graph "profiles" panel (matches the screenshot exactly).
    if (!empty($data['knowledgeGraph']['profiles'])) {
        foreach ($data['knowledgeGraph']['profiles'] as $p) {
            if (!empty($p['link'])) $classify($p['link']);
        }
    }
    // 2) Fallback: any organic result on a social domain.
    foreach (($data['organic'] ?? []) as $r) {
        if (!empty($r['link'])) $classify($r['link']);
    }
    // 3) Sitelinks sometimes have social too.
    if (!empty($data['knowledgeGraph']['website'])) {
        // (website is not social but nothing to do here)
    }

    return ['ok' => true, 'socials' => array_filter($socials)];
}

/**
 * Make sure lead_contacts table exists. Cheaper than asking user to
 * re-import database.sql after an update.
 */
function ensureLeadContactsTable(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS lead_contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            lead_id INT NOT NULL,
            contact_name VARCHAR(255),
            title VARCHAR(255),
            email VARCHAR(255),
            phone VARCHAR(50),
            linkedin_url VARCHAR(500),
            source VARCHAR(50),
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
            UNIQUE KEY unique_contact_per_lead (lead_id, email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

/**
 * Save a contact for a lead, skipping duplicates. Contacts without an
 * email are always inserted (dedupe key doesn't apply).
 */
function saveContactForLead(PDO $pdo, int $leadId, array $c): bool
{
    $email = !empty($c['email']) ? strtolower(trim($c['email'])) : null;

    if ($email) {
        $chk = $pdo->prepare('SELECT id FROM lead_contacts WHERE lead_id = ? AND email = ?');
        $chk->execute([$leadId, $email]);
        if ($chk->fetch()) return false; // already exists
    }

    $ins = $pdo->prepare('
        INSERT INTO lead_contacts (lead_id, contact_name, title, email, phone, linkedin_url, source)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $ins->execute([
        $leadId,
        trim($c['name'] ?? '') ?: null,
        trim($c['title'] ?? '') ?: null,
        $email,
        !empty($c['phone']) ? trim($c['phone']) : null,
        !empty($c['linkedin_url']) ? trim($c['linkedin_url']) : null,
        $c['source'] ?? 'manual',
    ]);
    return true;
}
