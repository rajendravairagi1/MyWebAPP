<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install BusinessFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10">
    <div class="w-full max-w-lg bg-white shadow-sm rounded-lg p-8">
        <h1 class="text-xl font-semibold text-gray-800">Install BusinessFlow</h1>
        <p class="text-sm text-gray-600 mt-2 mb-6">
            Enter the MySQL database details from your hosting cPanel. This runs once —
            it configures <code>.env</code> and applies the database migrations.
        </p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/install') }}?token={{ $token }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="app_url">App URL</label>
                <input id="app_url" name="app_url" type="url" required value="{{ old('app_url', $appUrl) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700" for="db_host">Database host</label>
                    <input id="db_host" name="db_host" type="text" required value="{{ old('db_host', 'localhost') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="db_port">Port</label>
                    <input id="db_port" name="db_port" type="text" required value="{{ old('db_port', '3306') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="db_database">Database name</label>
                <input id="db_database" name="db_database" type="text" required value="{{ old('db_database') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="db_username">Database username</label>
                <input id="db_username" name="db_username" type="text" required value="{{ old('db_username') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="db_password">Database password</label>
                <input id="db_password" name="db_password" type="password" value="{{ old('db_password') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            </div>

            <button type="submit"
                class="w-full bg-accent-600 hover:bg-accent-700 text-white text-sm font-medium py-2 rounded-md">
                Install
            </button>
        </form>
    </div>
</body>
</html>
