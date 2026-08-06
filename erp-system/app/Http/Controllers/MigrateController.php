<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

/**
 * One-time browser-based migration runner for hosts without a Terminal/SSH.
 * Runs ONLY `migrate` (never seed, never fresh) — safe to click after every
 * update ZIP that adds new tables/columns. Delete routes/migrate.php and this
 * file once you've confirmed the migration ran (or keep it if Terminal keeps
 * failing and you'll need it again for the next update).
 */
class MigrateController extends Controller
{
    protected string $token = 'first-install-2026';

    protected function checkToken(string $given): bool
    {
        return hash_equals($this->token, $given);
    }

    public function index()
    {
        $given = request('token', '');
        if (! $this->checkToken($given)) {
            return response('Galat ya missing token.', 403);
        }

        return response($this->page());
    }

    public function run()
    {
        $given = request('token', '');
        if (! $this->checkToken($given)) {
            return response('Galat ya missing token.', 403);
        }

        $log = [];

        try {
            Artisan::call('migrate', ['--force' => true]);
            $log[] = 'OK — Migrations chal gaye.';
            $log[] = trim(Artisan::output());
        } catch (\Throwable $e) {
            $log[] = 'FAIL — Migration me error: '.$e->getMessage();

            return response($this->page($log), 500);
        }

        $log[] = '';
        $log[] = 'SAB DONE. Naye modules ab use ho sakte hain.';

        return response($this->page($log));
    }

    protected function page(array $log = []): string
    {
        $token = e(request('token', ''));
        $csrf = csrf_token();
        $logHtml = $log
            ? '<pre style="background:#111;color:#0f0;padding:16px;border-radius:8px;white-space:pre-wrap;">'.e(implode("\n", $log)).'</pre>'
            : '';

        return <<<HTML
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>ERP — Run Migrations</title>
<style>
  body { font-family: -apple-system, Arial, sans-serif; max-width: 640px; margin: 60px auto; padding: 0 20px; color: #222; }
  h1 { font-size: 22px; }
  p { line-height: 1.6; color: #555; }
  button { background: #d9661e; color: #fff; border: none; padding: 12px 22px; border-radius: 8px; font-size: 15px; cursor: pointer; }
  button:hover { background: #b3542f; }
</style>
</head>
<body>
  <h1>ERP — Run Migrations</h1>
  <p>Ye button naye database tables/columns bana dega. Purana data safe rehta hai — sirf naya add hota hai. Jab bhi update ZIP me nayi migration files ho, isi button ko dobara chala dena.</p>
  <form method="POST" action="/migrate/run?token={$token}">
    <input type="hidden" name="_token" value="{$csrf}">
    <button type="submit">Run Migrations</button>
  </form>
  {$logHtml}
</body>
</html>
HTML;
    }
}
