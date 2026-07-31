<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../includes/enrichment.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiJson(['ok' => false, 'error' => 'POST required'], 405);
}
apiVerifyCsrf();

$leadId = (int) ($_POST['lead_id'] ?? 0);
if (!$leadId) apiJson(['ok' => false, 'error' => 'lead_id required'], 400);

$pdo = getDb();
ensureLeadContactsTable($pdo);

$stmt = $pdo->prepare('SELECT * FROM leads WHERE id = ?');
$stmt->execute([$leadId]);
$lead = $stmt->fetch();
if (!$lead) apiJson(['ok' => false, 'error' => 'Lead not found'], 404);

$domain = extractDomain($lead['website'] ?? null);

$apolloEnabled = getSetting('apollo_api_enabled', '0') === '1';
$hunterEnabled = getSetting('hunter_api_enabled', '0') === '1';
$apolloKey = getSetting('apollo_api_key', '');
$hunterKey = getSetting('hunter_api_key', '');

if (!$apolloEnabled && !$hunterEnabled) {
    apiJson(['ok' => false, 'error' => 'Both Apollo and Hunter are OFF in Settings. Turn at least one on.'], 400);
}

$log = [];
$totalAdded = 0;
$errors = [];

if ($apolloEnabled) {
    if (!$apolloKey) {
        $errors[] = 'Apollo key missing';
    } else {
        $res = apolloFindContacts($domain, $lead['company_name'], $apolloKey);
        if (!$res['ok']) {
            $errors[] = $res['error'];
        } else {
            $addedHere = 0;
            foreach ($res['contacts'] as $c) {
                if (saveContactForLead($pdo, $leadId, $c)) $addedHere++;
            }
            $log[] = "Apollo: " . count($res['contacts']) . " found, $addedHere new";
            $totalAdded += $addedHere;
        }
    }
}

if ($hunterEnabled) {
    if (!$hunterKey) {
        $errors[] = 'Hunter key missing';
    } else {
        $res = hunterFindContacts($domain, $hunterKey);
        if (!$res['ok']) {
            $errors[] = $res['error'];
        } else {
            $addedHere = 0;
            foreach ($res['contacts'] as $c) {
                if (saveContactForLead($pdo, $leadId, $c)) $addedHere++;
            }
            $log[] = "Hunter: " . count($res['contacts']) . " found, $addedHere new";
            $totalAdded += $addedHere;
        }
    }
}

// Re-fetch full contacts list for this lead so UI can render fresh.
$fetch = $pdo->prepare('SELECT * FROM lead_contacts WHERE lead_id = ? ORDER BY id DESC');
$fetch->execute([$leadId]);
$contacts = $fetch->fetchAll();

// If Apollo/Hunter surfaced a "best" email and the lead itself has no
// email yet, promote it onto the lead record so the pitch templates
// can auto-fill it.
if (empty($lead['email'])) {
    foreach ($contacts as $c) {
        if (!empty($c['email'])) {
            $pdo->prepare('UPDATE leads SET email = ? WHERE id = ?')->execute([$c['email'], $leadId]);
            break;
        }
    }
}

apiJson([
    'ok' => true,
    'added' => $totalAdded,
    'contacts' => $contacts,
    'log' => $log,
    'errors' => $errors,
]);
