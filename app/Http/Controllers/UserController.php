<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        // Sécurité : Un admin ne peut pas se retirer ses propres droits par erreur
        if (auth()->id() == $id && $request->role === 'user') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas retirer vos propres droits administrateur.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', "Le rôle de l'utilisateur a été mis à jour.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Sécurité : Un admin ne peut pas se supprimer lui-même
        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->back()->with('success', "L'utilisateur a été supprimé.");
    }

    /**
     * Réinitialisation forcée du mot de passe (Technique Admis - Matoor)
     */
    public function forceReset(User $user)
    {
        // Sécurité : Un admin ne peut pas reset son propre mot de passe via cette méthode
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Utilisez la méthode classique pour changer votre propre mot de passe.');
        }

        $temporaryPassword = 'password123';

        $user->update([
            'password' => Hash::make($temporaryPassword)
        ]);

        return redirect()->back()->with('success', "Le mot de passe de {$user->name} a été réinitialisé. Mot de passe provisoire : {$temporaryPassword}");
    }
}
