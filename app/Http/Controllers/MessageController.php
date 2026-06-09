<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * MessageController — Gestion de la messagerie interne.
 * Permet au patron et aux caissières de s'échanger des messages.
 */
class MessageController extends Controller
{
    /**
     * Affiche la boîte de messagerie de l'utilisateur connecté.
     * - Récupère tous les utilisateurs avec lesquels il peut communiquer.
     * - Si un destinataire est sélectionné, charge la conversation complète.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // Liste de tous les utilisateurs sauf soi-même
        $users = User::where('id', '!=', $currentUser->id)->get();

        // Identifiant du destinataire sélectionné (depuis l'URL)
        $selectedUserId = $request->query('with');
        $selectedUser   = null;
        $conversation   = collect();

        if ($selectedUserId) {
            $selectedUser = User::findOrFail($selectedUserId);

            // Charger tous les messages entre les deux utilisateurs
            $conversation = Message::where(function ($q) use ($currentUser, $selectedUserId) {
                    $q->where('sender_id', $currentUser->id)
                      ->where('receiver_id', $selectedUserId);
                })
                ->orWhere(function ($q) use ($currentUser, $selectedUserId) {
                    $q->where('sender_id', $selectedUserId)
                      ->where('receiver_id', $currentUser->id);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            // Marquer les messages reçus comme lus
            Message::where('sender_id', $selectedUserId)
                   ->where('receiver_id', $currentUser->id)
                   ->where('is_read', false)
                   ->update(['is_read' => true]);
        }

        // Compter les non-lus par expéditeur (pour les badges de la liste)
        $unreadCounts = Message::where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->selectRaw('sender_id, COUNT(*) as total')
            ->groupBy('sender_id')
            ->pluck('total', 'sender_id');

        return view('messages.index', compact(
            'users',
            'selectedUser',
            'conversation',
            'unreadCounts'
        ));
    }

    /**
     * Envoie un nouveau message à un destinataire.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content'     => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content'     => $request->content,
            'is_read'     => false,
        ]);

        return redirect()->route('messages.index', ['with' => $request->receiver_id])
                         ->with('success', 'Message envoyé !');
    }

    /**
     * API JSON : retourne le nombre total de messages non lus pour l'utilisateur connecté.
     * Utilisé par le JavaScript de polling (toutes les 5 secondes).
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * API JSON : retourne les derniers messages d'une conversation.
     * Utilisé pour le rafraîchissement automatique du chat.
     */
    public function poll(Request $request)
    {
        $currentUser    = Auth::user();
        $selectedUserId = $request->query('with');

        if (!$selectedUserId) {
            return response()->json(['messages' => []]);
        }

        // Charger les messages depuis un certain ID (pour ne pas recharger tout)
        $sinceId = $request->query('since_id', 0);

        $messages = Message::where(function ($q) use ($currentUser, $selectedUserId) {
                $q->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $selectedUserId);
            })
            ->orWhere(function ($q) use ($currentUser, $selectedUserId) {
                $q->where('sender_id', $selectedUserId)
                  ->where('receiver_id', $currentUser->id);
            })
            ->where('id', '>', $sinceId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($msg) => [
                'id'         => $msg->id,
                'content'    => $msg->content,
                'is_mine'    => $msg->sender_id === $currentUser->id,
                'sender'     => $msg->sender->name,
                'created_at' => $msg->created_at->format('H:i'),
            ]);

        // Marquer comme lus les messages reçus
        Message::where('sender_id', $selectedUserId)
               ->where('receiver_id', $currentUser->id)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return response()->json(['messages' => $messages]);
    }
}
