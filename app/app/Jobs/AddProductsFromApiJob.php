<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AddProductsFromApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //Константы в перспективе можно заменить на enum JobStatusEnum
    public const FAILED = 'Failed';
    public const PENDING = 'Pending';
    public const DISPATCHED = 'Dispatched';
    public const ONQUEUE = 'OnQueue';
    public string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = "https://dummyjson.com/products/search?limit=0&q=iPhone";

        Redis::set('job_'.$this->uuid, $this->setMessage(self::PENDING));
        $response = Http::get($url);

        if ($response->failed()) {
            Redis::set('job_'.$this->uuid, $this->setMessage(self::FAILED,'Request failed'));
            Log::channel('dispatch_log')->info($this->setMessage(self::FAILED, 'Request failed'));

            throw new \Exception('Request failed');
        }

        // Гипотетически нам может прийти очень большой ответ на >=10000 элементов.
        // Для экономии памяти воспользуемся функцией array_chunk()
        $chunks = array_chunk($response->json()['products'], 100);

        if (empty($chunks)) {
            Redis::set('job_'.$this->uuid, $this->setMessage(self::FAILED,'Empty response'));
            Log::channel('dispatch_log')->info($this->setMessage(self::FAILED,'Empty response'));

            throw new \Exception('Empty response');
        }

        try {
            DB::beginTransaction();

            foreach ($chunks as $products) {
                foreach ($products as $product) {
                    unset($product['id']);
                    $product['images'] = json_encode($product['images']);
                    $product = Product::query()->create($product);
                    $product->save();
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Redis::set('job_'.$this->uuid, $this->setMessage(self::FAILED,$e->getMessage()));
            Log::channel('dispatch_log')->info($this->setMessage(self::FAILED, $e->getMessage()));

            throw new \Exception($e->getMessage());
        }

        Redis::set('job_'.$this->uuid, $this->setMessage(self::DISPATCHED));
        Log::channel('dispatch_log')->info($this->setMessage(self::DISPATCHED));
    }

    private function setMessage(string $status, ?string $error = null): string
    {
        $message = ["uuid" => $this->uuid, "job_unique_id" => $this->job->uuid(), "status" => $status];

        if ($status === self::FAILED) {
            $message['message'] = $error;
        }

        return json_encode($message);
    }
}
