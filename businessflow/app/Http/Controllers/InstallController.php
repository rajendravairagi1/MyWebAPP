<?php

namespace App\Http\Controllers;

use App\Support\EnvWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Browser-based installer for hosts with no SSH/Composer access — upload the
 * app (vendor/ included) via File Manager/FTP, then visit
 * /install?token=<INSTALL_TOKEN> once to configure the database and run
 * migrations. Locks itself after a successful install so it can't be run
 * again by mistake (or by anyone else who finds the URL).
 */
class InstallController extends Controller
{
    protected function lockPath(): string
    {
        return storage_path('app/installed.lock');
    }

    protected function alreadyInstalled(): bool
    {
        return File::exists($this->lockPath());
    }

    protected function authorizeToken(Request $request): void
    {
        $expected = config('app.install_token');

        abort_unless(
            filled($expected) && hash_equals($expected, (string) $request->query('token')),
            403,
            'Missing or invalid install token.'
        );
    }

    public function index(Request $request): View|RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/')->with('status', 'BusinessFlow is already installed.');
        }

        $this->authorizeToken($request);

        return view('install.index', [
            'token' => $request->query('token'),
            'appUrl' => $request->getSchemeAndHttpHost(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            abort(403);
        }

        $this->authorizeToken($request);

        $data = $request->validate([
            'db_host' => ['required', 'string'],
            'db_port' => ['required', 'numeric'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
            'app_url' => ['required', 'url'],
        ]);

        config(['database.connections.install_probe' => [
            'driver' => 'mysql',
            'host' => $data['db_host'],
            'port' => $data['db_port'],
            'database' => $data['db_database'],
            'username' => $data['db_username'],
            'password' => $data['db_password'] ?? '',
            'charset' => 'utf8mb4',
        ]]);

        try {
            DB::connection('install_probe')->getPdo();
        } catch (\Throwable $e) {
            DB::purge('install_probe');

            return back()->withInput()->withErrors([
                'db_database' => 'Could not connect with these credentials: '.$e->getMessage(),
            ]);
        }

        DB::purge('install_probe');

        $appKey = config('app.key') ?: 'base64:'.base64_encode(random_bytes(32));

        EnvWriter::set([
            'APP_URL' => rtrim($data['app_url'], '/'),
            'APP_KEY' => $appKey,
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'] ?? '',
        ]);

        config([
            'app.key' => $appKey,
            'app.url' => rtrim($data['app_url'], '/'),
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $data['db_host'],
            'database.connections.mysql.port' => $data['db_port'],
            'database.connections.mysql.database' => $data['db_database'],
            'database.connections.mysql.username' => $data['db_username'],
            'database.connections.mysql.password' => $data['db_password'] ?? '',
        ]);

        DB::purge('mysql');
        DB::setDefaultConnection('mysql');

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('storage:link');

        File::ensureDirectoryExists(dirname($this->lockPath()));
        File::put($this->lockPath(), 'Installed '.now()->toDateTimeString());

        return redirect('/register')->with('status', 'Installation complete — create your account to get started.');
    }
}
