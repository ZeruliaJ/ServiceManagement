<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PartController extends Controller
{
    public function searchParts(Request $request)
{
    $search = strtolower(trim($request->query('search', '')));

    $response = Http::get('http://192.168.21.6/api/parts/api_parts.php');
    $data     = $response->json();

    if ($search && isset($data['data'])) {
        $data['data'] = array_values(array_filter($data['data'], function ($item) use ($search) {
            return str_contains(strtolower($item['ItemName'] ?? ''), $search)
                || str_contains(strtolower($item['ItemCode'] ?? ''), $search);
        }));
    }

    return response()->json($data);
}
}

