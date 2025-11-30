<?php

namespace App\Http\Controllers;

use App\Models\AbteilungDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AbteilungController extends Controller
{
    public function getAbteilung()
    {
        // Wir holen AbteilungID, nennen es aber für das Frontend "id"
        return AbteilungDefinition::select('AbteilungID as id', 'name')->get();
    }
}
