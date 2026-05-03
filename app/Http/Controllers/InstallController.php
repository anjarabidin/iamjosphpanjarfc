<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;

class InstallController extends Controller
{
    /**
     * Step 1: Requirements Check
     */
    public function index()
    {
        // Auto-create .env from .env.example if it doesn't exist
        $envFile = base_path('.env');
        $envExampleFile = base_path('.env.example');
        
        if (!file_exists($envFile) && file_exists($envExampleFile)) {
            if (is_writable(base_path())) {
                File::copy($envExampleFile, $envFile);
            }
        }

        $requirements = [
            'php' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'extensions' => [
                'bcmath'    => extension_loaded('bcmath'),
                'ctype'     => extension_loaded('ctype'),
                'fileinfo'  => extension_loaded('fileinfo'),
                'mbstring'  => extension_loaded('mbstring'),
                'openssl'   => extension_loaded('openssl'),
                'pdo_pgsql' => extension_loaded('pdo_pgsql'),
                'xml'       => extension_loaded('xml'),
            ],
            'permissions' => [
                '.env'             => is_writable(base_path('.env')),
                'storage'          => is_writable(storage_path()),
                'bootstrap/cache'  => is_writable(base_path('bootstrap/cache')),
            ],
        ];

        $allChecksPass = $requirements['php'];
        foreach ($requirements['extensions'] as $pass) {
            if (!$pass) {
                $allChecksPass = false;
            }
        }
        
        foreach ($requirements['permissions'] as $pass) {
            if (!$pass) {
                $allChecksPass = false;
            }
        }

        $requirements['allPass'] = $allChecksPass;

        return view('install.step1', compact('requirements'));
    }

    /**
     * Step 2: Database Setup
     */
    public function step2()
    {
        return view('install.step2');
    }

    public function testDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
        ]);

        try {
            config()->set('database.connections.pgsql_test', [
                'driver' => 'pgsql',
                'url' => env('DATABASE_URL'),
                'host' => $request->db_host,
                'port' => $request->db_port,
                'database' => $request->db_database,
                'username' => $request->db_username,
                'password' => $request->db_password,
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]);

            DB::purge('pgsql_test');
            DB::connection('pgsql_test')->getPdo();

            // Cache credentials in session for final step
            session([
                'install_db' => $request->only('db_host', 'db_port', 'db_database', 'db_username', 'db_password')
            ]);

            return response()->json(['success' => true, 'message' => 'Database connection successful!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Step 3: SMTP Mail Configuration
     */
    public function step3()
    {
        return view('install.step3');
    }

    public function testSmtp(Request $request)
    {
        $request->validate([
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'nullable',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required',
        ]);

        try {
            config()->set('mail.mailers.smtp_test', [
                'transport' => 'smtp',
                'url' => env('MAIL_URL'),
                'host' => $request->mail_host,
                'port' => $request->mail_port,
                'encryption' => $request->mail_encryption,
                'username' => $request->mail_username,
                'password' => $request->mail_password,
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ]);

            config()->set('mail.from.address', $request->mail_from_address);
            config()->set('mail.from.name', $request->mail_from_name);
            
            config()->set('mail.default', 'smtp_test');

            Mail::raw('This is a test email from the IAMJOS installer.', function ($message) use ($request) {
                $message->to($request->mail_from_address)
                        ->subject('IAMJOS SMTP Test');
            });

            // Cache credentials
            session([
                'install_mail' => $request->only(
                    'mail_host', 'mail_port', 'mail_username', 'mail_password', 
                    'mail_encryption', 'mail_from_address', 'mail_from_name'
                )
            ]);

            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'SMTP Test failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Step 4: Admin & Site Setup
     */
    public function step4()
    {
        return view('install.step4');
    }

    public function execute(Request $request)
    {
        $request->validate([
            'admin_name' => 'required',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8|confirmed',
            'app_name' => 'required|string',
            'app_env' => 'required|in:local,production',
            'app_debug' => 'required|in:true,false',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
        ]);

        try {
            // 1. Update .env
            $dbConfig = session('install_db');
            $mailConfig = session('install_mail');

            if (!$dbConfig || !$mailConfig) {
                return back()->with('error', 'Session expired. Please restart the installer.');
            }

            $envUpdates = [
                'APP_NAME' => $request->app_name,
                'APP_ENV' => $request->app_env,
                'APP_DEBUG' => $request->app_debug,
                'APP_URL' => $request->app_url,
                'APP_TIMEZONE' => $request->app_timezone,
                'DB_HOST' => $dbConfig['db_host'],
                'DB_PORT' => $dbConfig['db_port'],
                'DB_DATABASE' => $dbConfig['db_database'],
                'DB_USERNAME' => $dbConfig['db_username'],
                'DB_PASSWORD' => $dbConfig['db_password'],
                'MAIL_HOST' => $mailConfig['mail_host'],
                'MAIL_PORT' => $mailConfig['mail_port'],
                'MAIL_USERNAME' => $mailConfig['mail_username'],
                'MAIL_PASSWORD' => $mailConfig['mail_password'],
                'MAIL_ENCRYPTION' => $mailConfig['mail_encryption'] ?? '',
                'MAIL_FROM_ADDRESS' => $mailConfig['mail_from_address'],
                'MAIL_FROM_NAME' => '"' . $mailConfig['mail_from_name'] . '"',
            ];

            $this->updateEnvFile($envUpdates);

            // Re-configure DB dynamically to run migrations
            config()->set('database.connections.pgsql.host', $dbConfig['db_host']);
            config()->set('database.connections.pgsql.port', $dbConfig['db_port']);
            config()->set('database.connections.pgsql.database', $dbConfig['db_database']);
            config()->set('database.connections.pgsql.username', $dbConfig['db_username']);
            config()->set('database.connections.pgsql.password', $dbConfig['db_password']);
            DB::purge('pgsql');

            // 2. Migrate and Seed
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            // Generate APP_KEY
            Artisan::call('key:generate', ['--force' => true]);

            // 3. Create Super Admin User
            $admin = User::firstOrCreate(
                ['email' => $request->admin_email],
                [
                    'name' => $request->admin_name,
                    'password' => Hash::make($request->admin_password),
                ]
            );

            // Using Spatie Roles (assuming Super Admin role exists from seeds)
            try {
                if (!$admin->hasRole('Super Admin')) {
                    $admin->assignRole('Super Admin');
                }
            } catch (\Exception $e) {
                // Ignore if role doesn't exist just in case
            }

            // 4. Create storage/installed file
            File::put(storage_path('installed'), 'installed_at: ' . now());
            File::put(storage_path('install.log'), 'installed_at: ' . now() . "\n" . Artisan::output());

            // Clear cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            
            // Clean up session
            session()->forget(['install_db', 'install_mail']);

            return redirect('/login')->with('success', 'Installation successful! Please log in.');
        } catch (\Exception $e) {
            return back()->with('error', 'Installation failed: ' . $e->getMessage());
        }
    }

    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $content = file_get_contents($envFile);
        
        foreach ($data as $key => $value) {
            // Make sure value is scalar
            $value = (string) $value;

            // Quote value if it contains spaces or special characters
            // Avoid double quoting if it already has quotes
            if (preg_match('/\s/', $value) || str_contains($value, '#') || str_contains($value, '$')) {
                $value = trim($value, '"\'');
                $value = '"' . $value . '"';
            }

            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                $content .= "\n{$replacement}";
            }
        }
        
        file_put_contents($envFile, $content);
    }
}
