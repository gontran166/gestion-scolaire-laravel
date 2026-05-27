<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function showChangePassword()
    {
        return view('profil.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        // Vérification que le mot de passe actuel saisi est correct
        // Hash::check compare le texte clair avec le hash stocké en base
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ])->withInput();    //conserve les autres champs remplis sauf les mots de passe
        }

        // Mise à jour avec le nouveau mot de passe hashé
        auth()->user()->update([
            'password' => $request->password,
        ]);

        // Déconnexion après changement de mot de passe :
        // bonne pratique de sécurité, force une reconnexion avec le nouveau mdp
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                         ->with('success', 'Mot de passe modifié. Reconnectez-vous avec votre nouveau mot de passe.');
    }
}
