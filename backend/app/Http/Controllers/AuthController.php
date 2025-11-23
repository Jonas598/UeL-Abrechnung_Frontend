<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Neuer Benutzer registrieren
     */
    public function register(Request $request) {
        // Validierung: 'confirmed' erwartet ein Feld 'password_confirmation' im Request
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        // Benutzer erstellen & Passwort hashen
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password'])
        ]);

        // Token erstellen
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    /**
     * Login und Token-Ausgabe
     */
    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Benutzer anhand der E-Mail suchen
        $user = User::where('email', $fields['email'])->first();

        // Überprüfen, ob Benutzer existiert und Passwort stimmt
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Falsche Zugangsdaten (E-Mail oder Passwort)'
            ], 401);
        }

        // Token erstellen (Standard Sanctum Vorgehen)
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'message' => 'Login erfolgreich'
        ];

        return response($response, 201);
    }

    /**
     * Logout (Token ungültig machen)
     */
    public function logout(Request $request) {
        // Löscht das Token, das für den aktuellen Request genutzt wurde
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'Erfolgreich ausgeloggt'
        ];
    }
}
