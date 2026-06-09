@extends('layouts.app')

@section('content')
{{-- ===== MESSAGERIE INTERNE ===== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-comments me-2 text-primary"></i>Messagerie Interne
    </h2>
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="row" style="height: 72vh;">

    {{-- ===== COLONNE GAUCHE : Liste des utilisateurs ===== --}}
    <div class="col-md-4 col-lg-3 d-flex flex-column">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Conversations</h6>
            </div>
            <div class="list-group list-group-flush overflow-auto flex-grow-1">
                @forelse($users as $user)
                    @php
                        $unread = $unreadCounts[$user->id] ?? 0;
                        $isSelected = $selectedUser && $selectedUser->id === $user->id;
                    @endphp
                    <a href="{{ route('messages.index', ['with' => $user->id]) }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3
                              {{ $isSelected ? 'active' : '' }}">
                        {{-- Avatar initiales --}}
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px;height:40px;font-weight:700;font-size:15px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate">{{ $user->name }}</div>
                            <small class="{{ $isSelected ? 'text-white-50' : 'text-muted' }}">
                                {{ ucfirst($user->role) }}
                            </small>
                        </div>
                        {{-- Badge non-lus --}}
                        @if($unread > 0 && !$isSelected)
                            <span class="badge bg-danger rounded-pill">{{ $unread }}</span>
                        @endif
                    </a>
                @empty
                    <div class="list-group-item text-muted text-center py-4">
                        <i class="fas fa-user-slash mb-2 d-block fs-3"></i>
                        Aucun utilisateur disponible.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===== COLONNE DROITE : Zone de Chat ===== --}}
    <div class="col-md-8 col-lg-9 d-flex flex-column">
        <div class="card shadow-sm h-100 d-flex flex-column">

            @if($selectedUser)
                {{-- En-tête du chat --}}
                <div class="card-header bg-dark text-white d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:38px;height:38px;font-weight:700;">
                        {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $selectedUser->name }}</div>
                        <small class="text-white-50"><span class="status-dot-chat"></span> En ligne</small>
                    </div>
                </div>

                {{-- Zone de messages --}}
                <div id="chat-messages" class="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-2"
                     style="background:#f8f9fa;">
                    @forelse($conversation as $msg)
                        @if($msg->sender_id === Auth::id())
                            {{-- Message envoyé (à droite) --}}
                            <div class="d-flex justify-content-end">
                                <div class="px-3 py-2 rounded-3 text-white shadow-sm"
                                     style="background:linear-gradient(135deg,#1a1a2e,#0f3460);max-width:75%;">
                                    <div>{{ $msg->content }}</div>
                                    <div class="text-end mt-1" style="font-size:11px;opacity:.7;">
                                        {{ $msg->created_at->format('H:i') }}
                                        <i class="fas fa-check{{ $msg->is_read ? '-double' : '' }} ms-1"></i>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Message reçu (à gauche) --}}
                            <div class="d-flex align-items-end gap-2">
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:30px;height:30px;font-size:12px;font-weight:700;">
                                    {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                                </div>
                                <div class="px-3 py-2 rounded-3 bg-white shadow-sm"
                                     style="max-width:75%;">
                                    <div>{{ $msg->content }}</div>
                                    <div class="text-muted mt-1" style="font-size:11px;">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-muted my-auto">
                            <i class="fas fa-comment-dots fs-1 mb-3 d-block opacity-25"></i>
                            Commencez la conversation avec <strong>{{ $selectedUser->name }}</strong>
                        </div>
                    @endforelse
                </div>

                {{-- Formulaire d'envoi --}}
                <div class="card-footer bg-white border-top p-3">
                    <form action="{{ route('messages.store') }}" method="POST"
                          class="d-flex gap-2" id="chat-form">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                        <input type="text" name="content" id="chat-input"
                               class="form-control rounded-pill"
                               placeholder="Tapez votre message..."
                               autocomplete="off" required>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            @else
                {{-- Aucune conversation sélectionnée --}}
                <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-muted">
                    <i class="fas fa-comments fs-1 mb-3 opacity-25"></i>
                    <p class="mb-0">Sélectionnez un utilisateur pour démarrer une conversation</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Point de statut vert animé */
    .status-dot-chat {
        display: inline-block;
        width: 7px; height: 7px;
        background: #00e676;
        border-radius: 50%;
        animation: pulse 2s infinite;
        margin-right: 4px;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.3; }
    }
</style>

<script>
    // ===== POLLING : Vérification de nouveaux messages toutes les 5 secondes =====

    // Identifiant du destinataire actuellement ouvert
    const selectedUserId = {{ $selectedUser ? $selectedUser->id : 'null' }};

    // Dernier ID de message connu (pour ne charger que les nouveaux)
    let lastMessageId = {{ $conversation->count() > 0 ? $conversation->last()->id : 0 }};

    // Compte de non-lus précédent (pour détecter les nouveaux)
    let previousUnread = 0;

    /**
     * Joue un bip court via Web Audio API (signal de réception de message).
     */
    function playMessageBip() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.3);
        } catch(e) { /* Silence si AudioContext non disponible */ }
    }

    /**
     * Ajoute un message à droite (envoyé) dans la zone de chat.
     */
    function appendSentMessage(content, time) {
        const container = document.getElementById('chat-messages');
        if (!container) return;
        const div = document.createElement('div');
        div.className = 'd-flex justify-content-end';
        div.innerHTML = `
            <div class="px-3 py-2 rounded-3 text-white shadow-sm"
                 style="background:linear-gradient(135deg,#1a1a2e,#0f3460);max-width:75%;">
                <div>${escapeHtml(content)}</div>
                <div class="text-end mt-1" style="font-size:11px;opacity:.7;">${time}</div>
            </div>`;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    /**
     * Ajoute un message à gauche (reçu) dans la zone de chat.
     */
    function appendReceivedMessage(content, time, senderInitial) {
        const container = document.getElementById('chat-messages');
        if (!container) return;
        const div = document.createElement('div');
        div.className = 'd-flex align-items-end gap-2';
        div.innerHTML = `
            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:30px;height:30px;font-size:12px;font-weight:700;">${escapeHtml(senderInitial)}</div>
            <div class="px-3 py-2 rounded-3 bg-white shadow-sm" style="max-width:75%;">
                <div>${escapeHtml(content)}</div>
                <div class="text-muted mt-1" style="font-size:11px;">${time}</div>
            </div>`;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
        playMessageBip();
    }

    /** Échappe les caractères HTML pour éviter les injections. */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    /**
     * Polling : récupère les nouveaux messages de la conversation ouverte.
     */
    async function pollMessages() {
        if (!selectedUserId) return;
        try {
            const res = await fetch(`/api/messages/poll?with=${selectedUserId}&since_id=${lastMessageId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            data.messages.forEach(msg => {
                if (msg.id > lastMessageId) {
                    lastMessageId = msg.id;
                    if (msg.is_mine) {
                        // Ne pas réafficher les messages déjà rendus par Blade
                    } else {
                        appendReceivedMessage(msg.content, msg.created_at, msg.sender.charAt(0).toUpperCase());
                    }
                }
            });
        } catch(e) { /* Erreur réseau silencieuse */ }
    }

    /**
     * Polling : met à jour le badge de non-lus dans la navbar.
     */
    async function pollUnreadCount() {
        try {
            const res = await fetch('/api/messages/unread-count', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            const badge = document.getElementById('nav-unread-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                    // Nouveau message détecté depuis le dernier poll
                    if (data.count > previousUnread) {
                        playMessageBip();
                    }
                } else {
                    badge.style.display = 'none';
                }
                previousUnread = data.count;
            }
        } catch(e) {}
    }

    // Démarrer les pollings au chargement
    document.addEventListener('DOMContentLoaded', () => {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            // Scroller vers le bas au chargement
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Polling toutes les 5 secondes
        setInterval(pollMessages, 5000);
        setInterval(pollUnreadCount, 5000);
    });
</script>
@endsection
