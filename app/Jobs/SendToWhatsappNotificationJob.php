<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use App\Models\SiteSetting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;

class SendToWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Phone number (already normalized by WaGateway).
     */
    protected string $number;

    /**
     * Message to send.
     */
    protected string $message;

    /**
     * Number of retry attempts.
     */
    public int $tries = 3;

    /**
     * Backoff in seconds between retries.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     *
     * @param string $number Normalized phone number (e.g., 6281234567890)
     * @param string $message Message content to send
     */
    public function __construct(string $number, string $message)
    {
        $this->number = $number;
        $this->message = $message;
    }

    /**
     * Execute the job.
     * 
     * Fetches WhatsApp gateway credentials from database and sends the message.
     * Gracefully handles missing credentials by logging a warning.
     */
    public function handle(): void
    {
        // Fetch credentials from database (dynamic, not hardcoded)
        $settings = SiteSetting::first();

        // Graceful handling: if credentials are missing, log warning and exit
        if (!$settings) {
            Log::warning('SendToWhatsappNotificationJob: No site_settings record found.');
            return;
        }

        $apiUrl = $settings->wa_api_url;
        $deviceId = $settings->wa_device_id;

        if (empty($apiUrl) || empty($deviceId)) {
            Log::warning('SendToWhatsappNotificationJob: WhatsApp credentials not configured.', [
                'has_api_url' => !empty($apiUrl),
                'has_device_id' => !empty($deviceId),
            ]);
            return;
        }

        // Ensure URL ends with proper endpoint
        $url = rtrim($apiUrl, '/') . '/send';

        $client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);

        try {
            $response = $client->get($url, [
                'query' => [
                    'device_id' => $deviceId,
                    'number' => $this->number,
                    'message' => $this->message,
                ],
            ]);

            $result = $response->getBody()->getContents();

            Log::info('SendToWhatsappNotificationJob: Message sent successfully.', [
                'phone' => $this->maskPhone($this->number),
                'response' => substr($result, 0, 200), // Truncate response for logging
            ]);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('SendToWhatsappNotificationJob: Connection failed.', [
                'phone' => $this->maskPhone($this->number),
                'error' => $e->getMessage(),
            ]);
            throw $e; // Rethrow to trigger retry
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('SendToWhatsappNotificationJob: Request failed.', [
                'phone' => $this->maskPhone($this->number),
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            throw $e; // Rethrow to trigger retry
        } catch (\Exception $e) {
            Log::error('SendToWhatsappNotificationJob: Unexpected error.', [
                'phone' => $this->maskPhone($this->number),
                'error' => $e->getMessage(),
            ]);
            // Don't rethrow generic exceptions to avoid infinite retries
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendToWhatsappNotificationJob: Job failed permanently.', [
            'phone' => $this->maskPhone($this->number),
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Mask phone number for logging (privacy).
     */
    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 6) {
            return '****';
        }

        return substr($phone, 0, 4) . '****' . substr($phone, -4);
    }
}
