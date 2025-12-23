<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfNotParmittedMultiple;
use App\Models\Language;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Pusher\Pusher;

class SettingsController extends Controller {
    public function __construct(){
        $this->middleware(RedirectIfNotParmittedMultiple::class.':global,smtp,pusher');
    }

    const BASE_URL = 'https://gitlab.com';
    const PROJECT_ID = '44441130';

    public function systemUpdate() {
        $env = DotenvEditor::load();
        $demo = config('app.demo');
        return Inertia::render('Settings/Update', [
            'title' => 'Check latest updates',
            'current_version' => $env->getValue('VERSION'),
            'demo' => boolval($demo),
        ]);
    }

    public function systemUpdateCheck()
    {
        try {
            $env = DotenvEditor::load();
            $current_version = $env->getValue('VERSION');
            $update_info = $this->getVersionAvailable($current_version);

            if ($update_info && is_array($update_info)) {
                return response()->json([
                    'success' => true,
                    'has_update' => true,
                    'current_version' => $current_version,
                    'latest_version' => $update_info['version'],
                    'release_date' => $update_info['release_date'],
                    'release_notes' => $update_info['release_notes'],
                    'download_url' => $update_info['download_url'],
                    'files' => [] // Keep for backward compatibility
                ]);
            }

            return response()->json([
                'success' => true,
                'has_update' => false,
                'current_version' => $current_version,
                'latest_version' => $current_version,
                'message' => 'You are running the latest version',
                'files' => []
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in systemUpdateCheck', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to check for updates',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function getVersionAvailable($current)
    {
        // Cache the result for 1 hour to avoid excessive API calls
        return Cache::remember('latest_version_check', 3600, function () use ($current) {
            try {
                // Use GitLab Releases API instead of repository tags for better security
                $response = Http::timeout(10)
                    ->get(self::BASE_URL . '/api/v4/projects/' . self::PROJECT_ID . '/releases', [
                        'per_page' => 1,
                        'order_by' => 'released_at',
                        'sort' => 'desc'
                    ]);

                if (!$response->successful()) {
                    \Log::warning('GitLab API request failed', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return false;
                }

                $releases = $response->json();

                if (empty($releases)) {
                    return false;
                }

                $latestRelease = $releases[0];
                $latestVersion = $latestRelease['tag_name'] ?? null;

                if (!$latestVersion) {
                    return false;
                }

                // Compare versions
                if (version_compare($current, $latestVersion, '<')) {
                    return [
                        'version' => $latestVersion,
                        'release_date' => $latestRelease['released_at'] ?? null,
                        'release_notes' => $latestRelease['description'] ?? null,
                        'download_url' => $latestRelease['assets']['sources'][0]['url'] ?? null
                    ];
                }

                return false;

            } catch (\Exception $e) {
                \Log::error('Error checking for updates', [
                    'error' => $e->getMessage(),
                    'current_version' => $current
                ]);
                return false;
            }
        });
    }

    private function configExist($array){
        $hasValue = true;
        $envLoad = DotenvEditor::load();
        $keys = $envLoad->getKeys($array);
        foreach ($keys as $key){
            if(!$key['value']){
                $hasValue = false;
                break;
            }
        }
        return $hasValue;
    }

    public function index(){
        $pusher_setup = $this->configExist(['PUSHER_APP_ID','PUSHER_APP_KEY','PUSHER_APP_SECRET']);
        $piping_setup = $this->configExist(['IMAP_HOST','IMAP_PORT','IMAP_PROTOCOL','IMAP_ENCRYPTION','IMAP_USERNAME','IMAP_PASSWORD']);
        $settings = Setting::orderBy('id')->get();
        $settingData = [];
        foreach ($settings as $setting){
            $settingData[$setting['slug']] = ['id' => $setting->id, 'name' => $setting->name, 'slug' => $setting->slug, 'type' => $setting->type, 'value' => $setting->value];
            if($setting->type === 'json'){
                $settingData[$setting['slug']]['value'] = $setting->value? json_decode($setting->value, true): null;
            }
        }
        $customCss = '';
        if (File::exists(public_path('css/custom.css'))) {
            $customCss = File::get(public_path('css/custom.css'));
        }
        $settingData['custom_css'] = ['slug' => 'custom_css', 'name' => 'Custom CSS', 'value' => $customCss];
        $env = DotenvEditor::load();
        $site_key = $env->keyExists('RE_CAPTCHA_KEY')?$env->getValue('RE_CAPTCHA_KEY'):'';
        return Inertia::render('Settings/Index', [
            'title' => 'Global Settings',
            'site_key' => $site_key,
            'settings' => $settingData,
            'pusher' => $pusher_setup,
            'piping' => $piping_setup,
            'languages' => Language::orderBy('name')
                ->get()
                ->map
                ->only('code', 'name'),
            'users' => User::orderBy('first_name')
                ->get()
                ->map
                ->only('id', 'name'),
        ]);
    }

    public function update(){
        $requests = Request::all();

        if (config('app.demo')) {
            return Redirect::back()->with('error', 'Updating global settings are not allowed for the live demo.');
        }

        // Validate form data
        $validated = Request::validate([
            'app_name' => 'required|string|max:255',
            'site_key' => 'nullable|string|max:255',
            'default_language' => 'required|string|max:10',
            'default_recipient' => 'required|integer|exists:users,id',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'logo_white' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:512',
            'custom_css' => 'nullable|string',
            'enable_options' => 'nullable|array',
            'email_notifications' => 'nullable|array',
            'hide_ticket_fields' => 'nullable|array',
            'required_ticket_fields' => 'nullable|array',
        ]);

        if(!empty($requests['custom_css'])){
            Storage::disk('public_path')->put('css/custom.css', $requests['custom_css']);
        }

        if(!empty($requests['site_key'])){
            $env = DotenvEditor::load();
            $env->setKey('RE_CAPTCHA_KEY', $requests['site_key']);
            $env->save();
        }
        // Remove custom_css from requests array to avoid processing it as a setting
        if (isset($requests['custom_css'])) {
            unset($requests['custom_css']);
        }
        $jsonFields = ['hide_ticket_fields', 'required_ticket_fields'];


        foreach ($requests as $requestKey => $requestValue){
            $setting = Setting::where('slug', $requestKey)->first();
            if(isset($setting)){
                if($setting->type == 'json'){
                    if(!in_array($setting->slug, $jsonFields)) {
                        foreach ($requestValue as &$rv) {
                            $rv['value'] = (bool)$rv['value'];
                        }
                    }
                    $setting->value = json_encode($requestValue);
                }else{
                    $setting->value = $requestValue;
                }
                $setting->save();
            }else{
                Setting::create([
                    'slug' => $requestKey,
                    'name' => ucfirst(str_replace('_', ' ', $requestKey)),
                    'type' => in_array($requestKey, $jsonFields)? 'json' : 'text',
                    'value' => in_array($requestKey, $jsonFields)? json_encode($requestValue) : $requestValue,
                ]);
            }
        }

        if(Request::file('logo') && !empty(Request::file('logo'))){
            $logo = Request::file('logo');
            $ext = strtolower($logo->getClientOriginalExtension()); // png|jpg|jpeg
            $fileName = 'logo.' . ($ext === 'jpeg' ? 'jpg' : $ext);
            Storage::disk('image')->putFileAs('/', $logo, $fileName);
            // Clean up stale variants
            foreach (['png','jpg'] as $variant) {
                $name = 'logo.' . $variant;
                if ($name !== $fileName && File::exists(Storage::disk('image')->path($name))) {
                    File::delete(Storage::disk('image')->path($name));
                }
            }
        }

        if(Request::file('logo_white') && !empty(Request::file('logo_white'))){
            $logoWhite = Request::file('logo_white');
            $ext = strtolower($logoWhite->getClientOriginalExtension()); // png|jpg|jpeg
            $fileName = 'logo_white.' . ($ext === 'jpeg' ? 'jpg' : $ext);
            Storage::disk('image')->putFileAs('/', $logoWhite, $fileName);
            // Clean up stale variants
            foreach (['png','jpg'] as $variant) {
                $name = 'logo_white.' . $variant;
                if ($name !== $fileName && File::exists(Storage::disk('image')->path($name))) {
                    File::delete(Storage::disk('image')->path($name));
                }
            }
        }

        if(Request::file('favicon')){
            $favicon = Request::file('favicon');
            $ext = strtolower($favicon->getClientOriginalExtension());
            $fileName = $ext === 'ico' ? 'favicon.ico' : 'favicon.png';
            // Save favicon with appropriate extension at public root
            Storage::disk('public_path')->putFileAs('/', $favicon, $fileName);

            // Clean up stale counterpart to avoid conflicts
            if($fileName === 'favicon.ico'){
                if (File::exists(public_path('favicon.png'))) {
                    File::delete(public_path('favicon.png'));
                }
            }else{
                if (File::exists(public_path('favicon.ico'))) {
                    File::delete(public_path('favicon.ico'));
                }
            }
        }

        if(Request::file('logo') || Request::file('logo_white') || Request::file('favicon')){
            Artisan::call('cache:clear');
        }

        if(!empty($requests['default_language'])){
            /*
            $user = Auth::user();
            $user->locale = $requests['default_language'];
            $user->save();
             */
            User::whereNotNull('locale')->update(['locale' => $requests['default_language']]);
        }

        return Redirect::back()->with('success', 'Settings updated.');
    }

    public function smtp(){
        $demo = config('app.demo');
        $env = DotenvEditor::load();
        $rawKeys = $env->getKeys(['MAIL_HOST','MAIL_PORT','MAIL_USERNAME','MAIL_PASSWORD','MAIL_ENCRYPTION','MAIL_FROM_ADDRESS','MAIL_FROM_NAME']);

        // Format keys for Vue component
        $keys = [];
        foreach ($rawKeys as $key => $value) {
            $keys[$key] = $value['value'] ?? '';
        }

        return Inertia::render('Settings/Smtp', [
            'title' => 'SMTP Settings',
            'keys' => $keys,
            'demo' => boolval($demo),
        ]);
    }

    public function updateSmtp(){
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'Updating SMTP setup is not allowed for the live demo.');
        }

        $mailVariables = Request::validate([
            'MAIL_HOST' => ['required', 'string', 'max:255'],
            'MAIL_PORT' => ['required', 'integer', 'min:1', 'max:65535'],
            'MAIL_USERNAME' => ['required', 'string', 'max:255'],
            'MAIL_PASSWORD' => ['required', 'string', 'max:255'],
            'MAIL_ENCRYPTION' => ['required', 'string', 'in:tls,ssl,none'],
            'MAIL_FROM_ADDRESS' => ['nullable', 'email', 'max:255'],
            'MAIL_FROM_NAME' => ['nullable', 'string', 'max:255'],
        ]);

        $this->setEnvVariables($mailVariables);
        return Redirect::back()->with('success', 'SMTP configuration updated!');
    }

    public function updatePusher(){

        if (config('app.demo')) {
            return Redirect::back()->with('error', 'Updating pusher setup is not allowed for the live demo.');
        }

        $mailVariables = Request::validate([
            'PUSHER_APP_ID' => ['required'],
            'PUSHER_APP_KEY' => ['required'],
            'PUSHER_APP_SECRET' => ['required'],
            'PUSHER_APP_CLUSTER' => ['required']
        ]);

        // Save to .env file
        $this->setEnvVariables($mailVariables);

        // Persist to DB settings as JSON for runtime usage
        try {
            $payload = [
                'app_id' => $mailVariables['PUSHER_APP_ID'],
                'key' => $mailVariables['PUSHER_APP_KEY'],
                'secret' => $mailVariables['PUSHER_APP_SECRET'],
                'cluster' => $mailVariables['PUSHER_APP_CLUSTER'],
            ];
            
            $setting = Setting::where('slug', 'pusher')->first();
            
            if ($setting) {
                // Update existing setting
                $setting->name = 'Pusher Settings';
                $setting->type = 'json';
                $setting->value = json_encode($payload);
                $setting->save();
            } else {
                // Create new setting
                Setting::create([
                    'slug' => 'pusher',
                    'name' => 'Pusher Settings',
                    'type' => 'json',
                    'value' => json_encode($payload)
                ]);
            }
            
            // Invalidate cached settings to ensure fresh data
            Cache::forget('app_settings');
        } catch (\Exception $e) {
            \Log::error('Failed to save pusher settings in DB', ['error' => $e->getMessage()]);
        }

        // Optional legacy replacement for built asset (kept for backward compat)
        $this->setEnvVariableToJsFile();

        return Redirect::back()->with('success', 'Pusher configuration updated!');
    }

    private function setEnvVariableToJsFile(){
        $env = DotenvEditor::load();
        $pusherAppKey = $env->getValue('PUSHER_APP_KEY');
        $pusherAppCluster = $env->getValue('PUSHER_APP_CLUSTER');

        $jsFile = File::get(public_path('js/app.js'));
        $linePosition = strrpos($jsFile, 'broadcaster:"pusher",key:');
        if($linePosition){
            $pusherKey = substr($jsFile, $linePosition + 26, 20);
            $cluster = substr($jsFile, $linePosition + 57, 3);

            if(strlen($pusherKey) == 20){
                $jsFile = str_replace($pusherKey, $pusherAppKey, $jsFile);
            }

            if(strlen($cluster) == 3){
                $jsFile = str_replace($cluster, $pusherAppCluster, $jsFile);
            }

            File::delete(public_path('js/app.js'));

            Storage::disk('public_path')->put('js/app.js', $jsFile);
        }
    }

    private function setEnvVariables($data) {
        $env = DotenvEditor::load();
        foreach ($data as $data_key => $data_value){
            $env->setKey($data_key, $data_value);
        }
        $env->save();
    }

    public function pusher(){
        // Try to get from database first (most current values)
        $keys = [];
        try {
            $pusherSetting = Setting::where('slug', 'pusher')->first();
            if ($pusherSetting && $pusherSetting->type === 'json' && $pusherSetting->value) {
                $pusherConfig = json_decode($pusherSetting->value, true);
                if ($pusherConfig) {
                    $keys['PUSHER_APP_ID'] = $pusherConfig['app_id'] ?? '';
                    $keys['PUSHER_APP_KEY'] = $pusherConfig['key'] ?? '';
                    $keys['PUSHER_APP_SECRET'] = $pusherConfig['secret'] ?? '';
                    $keys['PUSHER_APP_CLUSTER'] = $pusherConfig['cluster'] ?? '';
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to read pusher settings from database', ['error' => $e->getMessage()]);
        }
        
        // Fallback to .env if database doesn't have values
        if (empty($keys['PUSHER_APP_KEY'])) {
            $env = DotenvEditor::load();
            $rawKeys = $env->getKeys(['PUSHER_APP_ID','PUSHER_APP_KEY','PUSHER_APP_SECRET','PUSHER_APP_CLUSTER']);
            
            foreach ($rawKeys as $key => $value) {
                $keys[$key] = $value['value'] ?? '';
            }
        }

        if (config('app.demo')) {
            $keys['PUSHER_APP_ID'] = '*******';
            $keys['PUSHER_APP_KEY'] = '*********************';
            $keys['PUSHER_APP_SECRET'] = '********************';
        }

        return Inertia::render('Settings/Pusher', [
            'title' => 'Pusher Settings',
            'keys' => $keys,
        ]);
    }

    public function testSmtp(){
        if (config('app.demo')) {
            return response()->json([
                'success' => false,
                'message' => 'Testing SMTP is not allowed for the live demo.'
            ]);
        }

        $data = Request::validate([
            'MAIL_HOST' => ['required', 'string', 'max:255'],
            'MAIL_PORT' => ['required', 'integer', 'min:1', 'max:65535'],
            'MAIL_USERNAME' => ['required', 'string', 'max:255'],
            'MAIL_PASSWORD' => ['required', 'string', 'max:255'],
            'MAIL_ENCRYPTION' => ['required', 'string', 'in:tls,ssl,none'],
            'MAIL_FROM_ADDRESS' => ['nullable', 'email', 'max:255'],
            'MAIL_FROM_NAME' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // Validate encryption and port combination
            $this->validateSmtpConfiguration($data);

            // Enhanced DSN construction for Laravel 12 and Gmail compatibility
            $dsn = $this->buildSmtpDsn($data);

            // Create transport with enhanced options for Gmail
            $transport = $this->createSmtpTransport($dsn, $data);

            // Test connection without sending email (connection-only test)
            $this->testSmtpConnection($transport);

            // Optional: Send a test email if connection is successful
            if ($this->shouldSendTestEmail($data)) {
                $this->sendTestEmail($transport, $data);
            }

            return response()->json([
                'success' => true,
                'message' => 'SMTP connection successful! Email server is properly configured and ready to send emails.'
            ]);

        } catch (TransportExceptionInterface $e) {
            \Log::error('SMTP Transport Error', [
                'error' => $e->getMessage(),
                'host' => $data['MAIL_HOST'],
                'port' => $data['MAIL_PORT'],
                'encryption' => $data['MAIL_ENCRYPTION'],
                'username' => $data['MAIL_USERNAME']
            ]);

            return response()->json([
                'success' => false,
                'message' => $this->getUserFriendlySmtpError($e->getMessage())
            ]);
        } catch (\Exception $e) {
            \Log::error('SMTP Test Error', [
                'error' => $e->getMessage(),
                'host' => $data['MAIL_HOST'],
                'port' => $data['MAIL_PORT'],
                'encryption' => $data['MAIL_ENCRYPTION'],
                'username' => $data['MAIL_USERNAME']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SMTP connection failed: ' . $this->getUserFriendlySmtpError($e->getMessage())
            ]);
        }
    }

    /**
     * Build SMTP DSN with enhanced options for Laravel 12
     */
    private function buildSmtpDsn($data)
    {
        $dsn = sprintf(
            'smtp://%s:%s@%s:%d?timeout=30&verify_peer=0&verify_peer_name=0',
            urlencode($data['MAIL_USERNAME']),
            urlencode($data['MAIL_PASSWORD']),
            $data['MAIL_HOST'],
            $data['MAIL_PORT']
        );

        if ($data['MAIL_ENCRYPTION'] !== 'none') {
            $dsn .= '&encryption=' . $data['MAIL_ENCRYPTION'];
        }

        // Gmail-specific optimizations
        if (strpos($data['MAIL_HOST'], 'gmail.com') !== false) {
            $dsn .= '&stream_options[ssl][allow_self_signed]=1';
            $dsn .= '&stream_options[ssl][verify_peer]=0';
            $dsn .= '&stream_options[ssl][verify_peer_name]=0';
        }

        return $dsn;
    }

    /**
     * Create SMTP transport with enhanced configuration
     */
    private function createSmtpTransport($dsn, $data)
    {
        $transport = Transport::fromDsn($dsn);

        // Additional configuration for Gmail
        if (strpos($data['MAIL_HOST'], 'gmail.com') !== false) {
            // Set additional stream context options for Gmail
            $streamContext = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
                ]
            ]);

            // Apply stream context if transport supports it
            if (method_exists($transport, 'setStreamContext')) {
                $transport->setStreamContext($streamContext);
            }
        }

        return $transport;
    }

    /**
     * Test SMTP connection without sending email
     */
    private function testSmtpConnection($transport)
    {
        // Start the transport to test connection
        $transport->start();

        // Stop the transport
        $transport->stop();
    }

    /**
     * Send test email if conditions are met
     */
    private function sendTestEmail($transport, $data)
    {
        $mailer = new Mailer($transport);
        $fromEmail = $data['MAIL_FROM_ADDRESS'] ?? $data['MAIL_USERNAME'];

        $email = (new Email())
            ->from($fromEmail)
            ->to($fromEmail) // Send test to self
            ->subject('SMTP Test - ' . config('app.name'))
            ->text('This is a test email to verify SMTP configuration. Connection successful!');

        $mailer->send($email);
    }

    /**
     * Determine if test email should be sent
     */
    private function shouldSendTestEmail($data)
    {
        // Only send test email for non-Gmail providers or if explicitly requested
        return strpos($data['MAIL_HOST'], 'gmail.com') === false;
    }

    /**
     * Validate SMTP configuration for common issues
     */
    private function validateSmtpConfiguration($data)
    {
        // Gmail-specific validation
        if (strpos($data['MAIL_HOST'], 'gmail.com') !== false) {
            $this->validateGmailConfiguration($data);
        }

        // General validation for all providers
        if ($data['MAIL_ENCRYPTION'] === 'ssl' && $data['MAIL_PORT'] != 465) {
            throw new \Exception('SSL encryption typically requires port 465. Please check your configuration.');
        }

        if ($data['MAIL_ENCRYPTION'] === 'tls' && !in_array($data['MAIL_PORT'], [587, 25])) {
            throw new \Exception('TLS encryption typically requires port 587 or 25. Please check your configuration.');
        }

        if ($data['MAIL_ENCRYPTION'] === 'none' && !in_array($data['MAIL_PORT'], [25, 587])) {
            throw new \Exception('Unencrypted connections typically use port 25 or 587. Please check your configuration.');
        }

        // Validate password format for Gmail
        if (strpos($data['MAIL_HOST'], 'gmail.com') !== false) {
            $this->validateGmailPassword($data['MAIL_PASSWORD']);
        }
    }

    /**
     * Validate Gmail-specific configuration
     */
    private function validateGmailConfiguration($data)
    {
        // Gmail requires specific ports and encryption
        if (!in_array($data['MAIL_PORT'], [587, 465])) {
            throw new \Exception('Gmail SMTP requires port 587 (TLS) or 465 (SSL). Port ' . $data['MAIL_PORT'] . ' is not supported.');
        }

        if ($data['MAIL_PORT'] == 465 && $data['MAIL_ENCRYPTION'] !== 'ssl') {
            throw new \Exception('Gmail port 465 requires SSL encryption.');
        }

        if ($data['MAIL_PORT'] == 587 && $data['MAIL_ENCRYPTION'] !== 'tls') {
            throw new \Exception('Gmail port 587 requires TLS encryption.');
        }

        // Check if username is a valid Gmail address
        if (!filter_var($data['MAIL_USERNAME'], FILTER_VALIDATE_EMAIL) ||
            !str_ends_with($data['MAIL_USERNAME'], '@gmail.com')) {
            throw new \Exception('Gmail SMTP requires a valid Gmail email address as username.');
        }
    }

    /**
     * Validate Gmail password format
     */
    private function validateGmailPassword($password)
    {
        // Gmail App Password should be 16 characters without spaces
        if (strlen($password) !== 16 || strpos($password, ' ') !== false) {
            throw new \Exception('Gmail requires an App Password (16 characters, no spaces). Please generate one from your Google Account settings.');
        }

        // Check if it looks like a regular password (contains spaces or special characters that App Passwords don't have)
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            throw new \Exception('Gmail App Passwords should only contain letters and numbers. Please generate a new App Password.');
        }
    }

    /**
     * Convert technical SMTP errors to user-friendly messages
     */
    private function getUserFriendlySmtpError($errorMessage)
    {
        $errorMessage = strtolower($errorMessage);

        // Gmail-specific error messages
        if (strpos($errorMessage, 'gmail') !== false || strpos($errorMessage, 'google') !== false) {
            if (strpos($errorMessage, 'authentication') !== false || strpos($errorMessage, 'invalid credentials') !== false) {
                return 'Gmail authentication failed. Please ensure you are using an App Password (not your regular password) and that 2-Factor Authentication is enabled in your Google Account.';
            }
            if (strpos($errorMessage, 'ssl') !== false || strpos($errorMessage, 'tls') !== false) {
                return 'Gmail SSL/TLS connection failed. Please use port 587 with TLS or port 465 with SSL encryption.';
            }
            return 'Gmail SMTP connection failed. Please verify your App Password and ensure 2-Factor Authentication is enabled.';
        }

        // General error messages
        if (strpos($errorMessage, 'connection refused') !== false) {
            return 'Connection refused. Please check if the SMTP server is running and the host/port are correct.';
        }

        if (strpos($errorMessage, 'connection timed out') !== false) {
            return 'Connection timed out. Please check your network connection and firewall settings.';
        }

        if (strpos($errorMessage, 'authentication failed') !== false || strpos($errorMessage, 'invalid credentials') !== false) {
            return 'Authentication failed. Please verify your username and password. For Gmail, you may need to use an App Password.';
        }

        if (strpos($errorMessage, 'ssl') !== false || strpos($errorMessage, 'tls') !== false) {
            return 'SSL/TLS connection failed. Please check your encryption settings and ensure the server supports the selected encryption method.';
        }

        if (strpos($errorMessage, 'host not found') !== false) {
            return 'SMTP host not found. Please verify the host address is correct.';
        }

        if (strpos($errorMessage, 'stream_socket_client') !== false) {
            return 'Network connection failed. Please check your internet connection and firewall settings.';
        }

        if (strpos($errorMessage, 'certificate') !== false) {
            return 'SSL certificate verification failed. Please check your encryption settings.';
        }

        return 'SMTP connection failed: ' . $errorMessage;
    }

    public function testPusher(){
        if (config('app.demo')) {
            return response()->json([
                'success' => false,
                'message' => 'Testing Pusher is not allowed for the live demo.'
            ]);
        }

        $data = Request::validate([
            'PUSHER_APP_ID' => ['required'],
            'PUSHER_APP_KEY' => ['required'],
            'PUSHER_APP_SECRET' => ['required'],
            'PUSHER_APP_CLUSTER' => ['required'],
        ]);

        try {
            // Test Pusher connection
            $pusher = new Pusher(
                $data['PUSHER_APP_KEY'],
                $data['PUSHER_APP_SECRET'],
                $data['PUSHER_APP_ID'],
                [
                    'cluster' => $data['PUSHER_APP_CLUSTER'],
                    'useTLS' => true
                ]
            );

            // Try to trigger a test event
            $pusher->trigger('test-channel', 'test-event', [
                'message' => 'Pusher connection test successful!'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pusher connection successful! Real-time features are ready to use.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pusher connection failed: ' . $e->getMessage()
            ]);
        }
    }

    public function piping(){
        $env = DotenvEditor::load();
        $keys = $env->getKeys(['IMAP_HOST','IMAP_PORT','IMAP_PROTOCOL','IMAP_ENCRYPTION','IMAP_USERNAME','IMAP_PASSWORD']);
        $setting = Setting::where('slug', 'enable_options')->first();
        $options = $setting->value? json_decode($setting->value, true): null;
        $key = array_search('enable_piping', array_column($options, 'slug'));
        $option = $options[$key];
        $demoMode = !!config('app.demo');

        if ($demoMode) {
            $keys['IMAP_HOST']['value'] = '*********************';
            $keys['IMAP_PORT']['value'] = '*********************';
            $keys['IMAP_PROTOCOL']['value'] = '********************';
            $keys['IMAP_ENCRYPTION']['value'] = '********************';
            $keys['IMAP_USERNAME']['value'] = '********************';
            $keys['IMAP_PASSWORD']['value'] = '********************';
        }

        return Inertia::render('Settings/Piping', [
            'title' => 'Email Piping Settings',
            'keys' => $keys,
            'option' => $option,
            'demo' => $demoMode,
        ]);
    }

    public function updatePiping(){

        if (config('app.demo')) {
            return Redirect::back()->with('error', 'Updating piping setup is not allowed for the live demo.');
        }

        if(!empty(Request::input('enable_piping'))){
            $pipingObject = Request::input('enable_piping');
            $setting = Setting::where('slug', 'enable_options')->first();
            $options = $setting->value? json_decode($setting->value, true): null;
            $key = array_search('enable_piping', array_column($options, 'slug'));
            $options[$key]['value'] = $pipingObject['value'];
            $setting->value = json_encode($options);
            $setting->save();
        }

        $pipingVariables = Request::validate([
            'IMAP_HOST' => ['required'],
            'IMAP_PORT' => ['required'],
            'IMAP_PROTOCOL' => ['required'],
            'IMAP_ENCRYPTION' => ['required'],
            'IMAP_USERNAME' => ['required'],
            'IMAP_PASSWORD' => ['required'],
        ]);

        $this->setEnvVariables($pipingVariables);

        return Redirect::back()->with('success', 'Piping settings are updated!');
    }

    public function clearCache($slug){
        // php artisan optimize && php artisan cache:clear && php artisan route:cache && php artisan view:clear && php artisan config:cache
        $slugArray = [
            'config' => 'config:cache', 'optimize' => 'optimize', 'cache' => 'cache:clear',
            'route' => 'route:cache', 'view' => 'view:clear'
        ];
        if(isset($slugArray[$slug])){
            Artisan::call($slugArray[$slug]);
        }elseif($slug == 'all'){
            Artisan::call('optimize');
            Artisan::call('cache:clear');
            Artisan::call('route:cache');
            Artisan::call('view:clear');
            Artisan::call('config:cache');
            Artisan::call('clear-compiled');
        }
        return response()->json(['success'=>true]);
    }
}
