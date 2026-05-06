<?php

namespace App\Http\Controllers\TVS;
use App\Http\Controllers\Controller; 
use App\Models\ServiceType;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ServiceTypeController extends Controller
{
    public function index()
{
    $types = ServiceType::orderBy('sort_order')->get();
    return response()->json($types);
}

}