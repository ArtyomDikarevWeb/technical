<?php

namespace App\Http\Controllers;

use App\Jobs\AddProductsFromApiJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class ProductsController extends Controller
{
    private string $jobUniqueId;
    private AddProductsFromApiJob $productJob;

    public function __construct()
    {
        $this->jobUniqueId = uuid_create();
        $this->productJob = new AddProductsFromApiJob($this->jobUniqueId);
    }

    public function loadProductsFromApi(): JsonResponse
    {
        Redis::set('user', json_encode(['user_unique_job_id' => $this->jobUniqueId]));
        Redis::set('job_'.$this->jobUniqueId, json_encode(['status' => $this->productJob::ONQUEUE]));
        dispatch($this->productJob);

        return response()->json(['data' => 'success']);
    }

    public function isJobDone(): JsonResponse
    {
        $data = json_decode(Redis::get('user'));
        $jobData = json_decode(Redis::get('job_'.$data->user_unique_job_id));

        if ($jobData->status === $this->productJob::DISPATCHED) {
            return response()->json(['data' => ['status' => $this->productJob::DISPATCHED]], 200);
        }

        if ($jobData->status === $this->productJob::PENDING) {
            return response()->json(['data' => ['status' => $this->productJob::PENDING]], 200);
        }

        if ($jobData->status === $this->productJob::FAILED) {
            return response()->json(['data' => ['status' => $this->productJob::FAILED]], 400);
        }

        if ($jobData->status === $this->productJob::ONQUEUE) {
            return response()->json(['data' => ['status' => $this->productJob::ONQUEUE]], 200);
        }

        return response()->json(['data' => false], 200);
    }


}
