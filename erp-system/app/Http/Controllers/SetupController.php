<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * One-time browser-based installer for hosts without a Terminal/SSH.
 * Delete routes/setup.php and this file once setup is confirmed working —
 * see DEPLOY-CPANEL.md step 5.
 */
class SetupController extends Controller
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
            return response('Galat ya missing token. Guide me diya hua link use karo.', 403);
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
            DB::connection()->getPdo();
            $log[] = 'OK — Database connect ho gaya.';
        } catch (\Throwable $e) {
            $log[] = 'FAIL — Database connect nahi hua. .env me DB_DATABASE/DB_USERNAME/DB_PASSWORD check karo.';
            $log[] = 'Error detail: '.$e->getMessage();

            return response($this->page($log), 500);
        }

        if (blank(env('APP_KEY'))) {
            Artisan::call('key:generate', ['--force' => true]);
            $log[] = 'OK — APP_KEY generate ho gaya.';
        } else {
            $log[] = 'OK — APP_KEY pehle se set hai.';
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $log[] = 'OK — Migrations chal gaye.';
            $log[] = trim(Artisan::output());
        } catch (\Throwable $e) {
            $log[] = 'FAIL — Migration me error: '.$e->getMessage();

            return response($this->page($log), 500);
        }

        try {
            Artisan::call('db:seed', ['--force' => true]);
            $log[] = 'OK — Roles aur Owner user seed ho gaye (ya pehle se the).';
        } catch (\Throwable $e) {
            $log[] = 'NOTE — Seeding dobara chalane pe "Owner already exists" jaisa error aa sakta hai, ye normal hai agar aap dusri baar run kar rahe ho.';
        }

        try {
            Artisan::call('storage:link');
            $log[] = 'OK — Storage link ban gaya.';
        } catch (\Throwable $e) {
            $log[] = 'NOTE — Storage link already tha ya skip ho gaya.';
        }

        $log[] = '';
        $log[] = 'SAB DONE. Ab /login pe jao: owner@example.com / password — login karke turant password badal do.';
        $log[] = 'Fir routes/setup.php aur app/Http/Controllers/SetupController.php DELETE kar dena (security ke liye).';

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
<title>ERP — First-Time Setup</title>
<style>
  body { font-family: -apple-system, Arial, sans-serif; max-width: 640px; margin: 60px auto; padding: 0 20px; color: #222; }
  h1 { font-size: 22px; }
  p { line-height: 1.6; color: #555; }
  button { background: #d9661e; color: #fff; border: none; padding: 12px 22px; border-radius: 8px; font-size: 15px; cursor: pointer; }
  button:hover { background: #b3542f; }
</style>
</head>
<body>
  <h1>ERP — First-Time Setup</h1>
  <p>Ye button click karne se database migrate + roles/Owner user seed ho jayenge. Pehle .env me database details (DB_DATABASE, DB_USERNAME, DB_PASSWORD) save kar chuke ho, tabhi click karo.</p>
  <form method="POST" action="/setup/run?token={$token}">
    <input type="hidden" name="_token" value="{$csrf}">
    <button type="submit">Run Setup</button>
  </form>
  {$logHtml}
</body>
</html>
HTML;
    }
}
