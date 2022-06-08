<?php

use Illuminate\Support\Facades\Http;

function getUser($userId){
    $url = env('URL_SERVICE_USER').'users/'.$userId;

    try {
        $response = Http::get($url);
        $data = $response->json();
        $data['http_code'] = $response->status();

        return $data;

    } catch (\Throwable $th) {
        return [
            'status' => false,
            'message' => $th->getMessage(),
            'http_code' => $th->getCode() ? $th->getCode() : 500
        ];
    }
}

function getUserByIds($userIds = []){
    $url = env('URL_SERVICE_USER').'users';

    try {
        $respone = Http::get($url, [
            'user_ids[]' => $userIds
        ]);
        $data = $respone->json();
        $data['http_code'] = $respone->status();

        return $data;
        
    } catch (\Throwable $th) {
        return [
            'status' => false,
            'message' => $th->getMessage(),
            'http_code' => $th->getCode()
        ];
    }
}

function postOrder($params) {
    $url = env('SERVICE_ORDER_PAYMENT').'orders';
    try {
        $response = Http::post($url, $params);
        $data = $response->json();
        $data['http_code'] = $response->status();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => false,
            'http_code' => 500,
            'message' => 'service order payment unavailable',
        ];
    }
}