<?php

namespace App\Http\Controllers;

class AccueilController extends Controller
{
    public function index()
    {
        // Si l'utilisateur est déjà connecté, on le redirige directement
        // vers son tableau de bord — pas besoin de revoir l'accueil
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('accueil');
    }
}