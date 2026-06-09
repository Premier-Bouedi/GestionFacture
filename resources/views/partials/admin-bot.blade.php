{{-- Sécurité Harick : Visible uniquement pour les administrateurs --}}
@auth
@if(auth()->user()->isAdmin())

{{-- ===== NIVEAU 1 : Bouton Flottant ===== --}}
<button id="admin-bot-toggle" class="admin-bot-btn" onclick="toggleAdminBot()">
    <i class="fas fa-robot"></i>
    <span class="admin-bot-label">Assistant Admis</span>
</button>

{{-- ===== NIVEAU 2 : Fenêtre de Chat ===== --}}
<div id="admin-bot-window" class="admin-bot-window">
    {{-- Header --}}
    <div class="admin-bot-header">
        <div class="d-flex align-items-center gap-2">
            <div class="admin-bot-avatar">🛡️</div>
            <div>
                <strong>Votre Assistant Admis</strong>
                <div class="admin-bot-status"><span class="status-dot"></span> En ligne</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="admin-bot-volume" class="admin-bot-icon-btn" onclick="toggleBotVolume()" title="Activer/Désactiver le son">
                <i class="bi bi-volume-up-fill" id="bot-volume-icon"></i>
            </button>
            <button class="admin-bot-close" onclick="toggleAdminBot()">&times;</button>
        </div>
    </div>

    {{-- Zone de Messages --}}
    <div id="admin-bot-messages" class="admin-bot-messages">
        {{-- Message de Bienvenue --}}
        <div class="bot-msg">
            <div class="bot-msg-avatar">🤖</div>
            <div class="bot-msg-content">
                Bonjour <strong>{{ auth()->user()->name }}</strong> ! 👋<br>
                Comment puis-je vous aider dans votre gestion aujourd'hui ?
            </div>
        </div>

        {{-- ===== NIVEAU 3 : Boutons Rapides ===== --}}
        <div class="quick-actions">
            <p class="quick-actions-label">Actions rapides :</p>
            <div class="quick-actions-grid">
                <button class="quick-btn" onclick="askBot('stock bas')">
                    <i class="fas fa-boxes-stacked"></i> État des stocks
                </button>
                <button class="quick-btn" onclick="askBot('rupture de stock')">
                    <i class="fas fa-circle-exclamation"></i> Ruptures de stock
                </button>
                <button class="quick-btn" onclick="askBot('nombre de clients')">
                    <i class="fas fa-users"></i> Nombre de clients
                </button>
                <button class="quick-btn" onclick="askBot('chiffre d\'affaires')">
                    <i class="fas fa-chart-line"></i> Chiffre d'Affaires
                </button>
                <button class="quick-btn" onclick="askBot('dernière facture')">
                    <i class="fas fa-file-invoice"></i> Dernière facture
                </button>
                <button class="quick-btn" onclick="askBot('résumé')">
                    <i class="fas fa-gauge-high"></i> Résumé global
                </button>
            </div>
        </div>

        {{-- ===== CHOIX DE LA VOIX ===== --}}
        <div class="voice-selector text-center mt-2 mb-2">
            <span class="quick-actions-label d-inline-block me-2">🎙️ Voix :</span>
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="botVoice" id="voiceMale" autocomplete="off" onchange="setBotVoice('male')">
                <label class="btn btn-outline-secondary" for="voiceMale" style="font-size:11px;">Homme</label>

                <input type="radio" class="btn-check" name="botVoice" id="voiceFemale" autocomplete="off" onchange="setBotVoice('female')" checked>
                <label class="btn btn-outline-secondary" for="voiceFemale" style="font-size:11px;">Femme</label>
            </div>
        </div>
    </div>

    {{-- Zone de Saisie --}}
    <div class="admin-bot-input-area">
        <button class="admin-bot-icon-btn text-secondary" id="admin-bot-mic" onclick="toggleSpeechRecognition()" title="Parler">
            <i class="fas fa-microphone"></i>
        </button>
        <input type="text" id="admin-bot-input" class="admin-bot-input" placeholder="Tapez votre question..." autocomplete="off">
        <button class="admin-bot-send" onclick="sendBotMessage()">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

{{-- ===== STYLES ===== --}}
<style>
    /* Bouton Flottant */
    .admin-bot-btn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 22px;
        border: none;
        border-radius: 50px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        box-shadow: 0 6px 30px rgba(15, 52, 96, 0.5);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .admin-bot-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 40px rgba(15, 52, 96, 0.6);
    }

    .admin-bot-btn i {
        font-size: 18px;
    }

    .admin-bot-label {
        letter-spacing: 0.5px;
    }

    /* Fenêtre de Chat */
    .admin-bot-window {
        position: fixed;
        bottom: 90px;
        right: 24px;
        width: 400px;
        max-height: 560px;
        z-index: 10001;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        display: none;
        flex-direction: column;
        overflow: hidden;
        animation: botSlideUp 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Inter', sans-serif;
    }

    .admin-bot-window.active {
        display: flex;
    }

    @keyframes botSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Header */
    .admin-bot-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: #fff;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-bot-avatar {
        font-size: 28px;
    }

    .admin-bot-status {
        font-size: 11px;
        opacity: 0.8;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        background: #00e676;
        border-radius: 50%;
        display: inline-block;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }
    }

    .admin-bot-close {
        background: none;
        border: none;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.2s;
        outline: none;
    }

    .admin-bot-close:hover {
        opacity: 1;
        color: #ff5252;
        transform: rotate(90deg);
    }

    .admin-bot-icon-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.8;
        transition: all 0.2s;
        outline: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .admin-bot-icon-btn:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    /* Messages */
    .admin-bot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 18px;
        max-height: 360px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .bot-msg {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .bot-msg-avatar {
        width: 34px;
        height: 34px;
        background: #f0f2f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .bot-msg-content {
        background: #f0f2f5;
        padding: 12px 16px;
        border-radius: 0 16px 16px 16px;
        font-size: 13px;
        line-height: 1.6;
        color: #333;
        max-width: 85%;
        white-space: pre-line;
        word-wrap: break-word;
    }

    .user-msg {
        display: flex;
        justify-content: flex-end;
    }

    .user-msg-content {
        background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
        color: #fff;
        padding: 12px 16px;
        border-radius: 16px 0 16px 16px;
        font-size: 13px;
        line-height: 1.5;
        max-width: 85%;
    }

    /* Quick Actions */
    .quick-actions {
        padding: 4px 0;
    }

    .quick-actions-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #999;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }

    .quick-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border: 1.5px solid #e0e0e0;
        border-radius: 12px;
        background: #fff;
        color: #333;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
    }

    .quick-btn:hover {
        border-color: #0f3460;
        color: #0f3460;
        background: #f8f9ff;
        transform: translateY(-1px);
    }

    .quick-btn i {
        font-size: 14px;
        color: #0f3460;
    }

    /* Typing Indicator */
    .typing-dots {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
        align-items: center;
    }

    .typing-dots span {
        width: 8px;
        height: 8px;
        background: #ccc;
        border-radius: 50%;
        animation: dotBounce 1.4s infinite;
    }

    .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes dotBounce {

        0%,
        60%,
        100% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-8px);
        }
    }

    /* Input */
    .admin-bot-input-area {
        display: flex;
        padding: 14px;
        border-top: 1px solid #eee;
        gap: 10px;
        background: #fafafa;
    }

    .admin-bot-input {
        flex: 1;
        border: 1.5px solid #e0e0e0;
        border-radius: 24px;
        padding: 11px 18px;
        font-size: 13px;
        outline: none;
        transition: border 0.2s;
        font-family: 'Inter', sans-serif;
    }

    .admin-bot-input:focus {
        border-color: #0f3460;
    }

    .admin-bot-send {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
        color: #fff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: transform 0.2s;
    }

    .admin-bot-send:hover {
        transform: scale(1.1);
    }
</style>

{{-- ===== JAVASCRIPT ===== --}}
<script>
    // ===== GESTION AUDIO & RECONNAISSANCE VOCALE =====
    
    let botVoicePref = 'female';
    let availableVoices = [];
    
    // Initialisation du volume depuis le localStorage
    function initBotVolume() {
        const isMuted = localStorage.getItem('audio_enabled') === 'false';
        updateVolumeIcon(isMuted);
        
        // Charger les voix quand elles sont prêtes
        if (window.speechSynthesis) {
            window.speechSynthesis.onvoiceschanged = () => {
                availableVoices = window.speechSynthesis.getVoices();
            };
        }
    }

    function toggleBotVolume() {
        const isMuted = localStorage.getItem('audio_enabled') === 'false';
        const newMutedState = !isMuted;
        localStorage.setItem('audio_enabled', newMutedState ? 'false' : 'true');
        updateVolumeIcon(newMutedState);
        
        // Si on coupe le son, on arrête de parler
        if (newMutedState && window.speechSynthesis) {
            window.speechSynthesis.cancel();
        }
    }

    function updateVolumeIcon(isMuted) {
        const icon = document.getElementById('bot-volume-icon');
        if (icon) {
            if (isMuted) {
                icon.className = 'bi bi-volume-mute-fill text-danger';
            } else {
                icon.className = 'bi bi-volume-up-fill';
            }
        }
    }

    function setBotVoice(gender) {
        botVoicePref = gender;
    }

    function speakText(text) {
        // Vérifier si le son est activé
        if (localStorage.getItem('audio_enabled') === 'false') return;
        
        if (!window.speechSynthesis) return;

        // Nettoyer le HTML et Markdown
        const cleanText = text
            .replace(/<[^>]*>?/gm, '') // Enlever balises HTML
            .replace(/[*_#`~]/g, '')    // Enlever Markdown
            .replace(/http[^\s]+/g, 'lien internet'); // Simplifier URL
            
        const utterance = new SpeechSynthesisUtterance(cleanText);
        utterance.lang = 'fr-FR';
        utterance.rate = 0.95; // Rythme humain fluide
        
        // Attribuer une voix
        if (availableVoices.length > 0) {
            // Chercher des voix françaises
            const frVoices = availableVoices.filter(v => v.lang.startsWith('fr'));
            if (frVoices.length > 0) {
                // Heuristique simple: on essaie de deviner homme/femme par le nom si possible
                // Google Français est souvent féminin, Microsoft Hortense est féminine, Microsoft Paul est masculin
                let selectedVoice = null;
                
                if (botVoicePref === 'male') {
                    selectedVoice = frVoices.find(v => v.name.toLowerCase().includes('paul') || v.name.toLowerCase().includes('thomas')) || frVoices[0];
                } else {
                    selectedVoice = frVoices.find(v => v.name.toLowerCase().includes('hortense') || v.name.toLowerCase().includes('julie') || v.name.toLowerCase().includes('google')) || frVoices[0];
                }
                
                if (selectedVoice) {
                    utterance.voice = selectedVoice;
                }
            }
        }
        
        window.speechSynthesis.speak(utterance);
    }

    // --- RECONNAISSANCE VOCALE (Speech Recognition) ---
    let recognition = null;
    let isRecording = false;

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRec();
        recognition.lang = 'fr-FR';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            const input = document.getElementById('admin-bot-input');
            input.value = transcript;
            sendBotMessage();
        };

        recognition.onend = function() {
            isRecording = false;
            updateMicIcon();
        };
        
        recognition.onerror = function(event) {
            console.error("Speech recognition error", event.error);
            isRecording = false;
            updateMicIcon();
        };
    }

    function toggleSpeechRecognition() {
        if (!recognition) {
            alert("La reconnaissance vocale n'est pas supportée par votre navigateur.");
            return;
        }

        if (isRecording) {
            recognition.stop();
        } else {
            // Si on parle, on coupe la synthèse vocale pour ne pas qu'elle s'écoute
            if (window.speechSynthesis) window.speechSynthesis.cancel();
            
            recognition.start();
            isRecording = true;
        }
        updateMicIcon();
    }

    function updateMicIcon() {
        const btn = document.getElementById('admin-bot-mic');
        const icon = btn.querySelector('i');
        if (isRecording) {
            icon.className = 'fas fa-microphone text-danger';
            icon.classList.add('fa-beat-fade');
        } else {
            icon.className = 'fas fa-microphone';
            icon.classList.remove('fa-beat-fade');
        }
    }


    function toggleAdminBot() {
        const win = document.getElementById('admin-bot-window');
        const btn = document.getElementById('admin-bot-toggle');
        if (win.classList.contains('active')) {
            win.classList.remove('active');
            btn.style.display = 'flex';
        } else {
            win.classList.add('active');
            btn.style.display = 'none';
            document.getElementById('admin-bot-input').focus();
        }
    }

    function addBotReply(text) {
        const container = document.getElementById('admin-bot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = 'bot-msg';
        wrapper.innerHTML = `
            <div class="bot-msg-avatar">🤖</div>
            <div class="bot-msg-content">${text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')}</div>
        `;
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
        
        // Lancer la lecture vocale
        speakText(text);
    }

    function addUserMsg(text) {
        const container = document.getElementById('admin-bot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = 'user-msg';
        wrapper.innerHTML = `<div class="user-msg-content">${text}</div>`;
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
    }

    function showBotTyping() {
        const container = document.getElementById('admin-bot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = 'bot-msg';
        wrapper.id = 'bot-typing';
        wrapper.innerHTML = `
            <div class="bot-msg-avatar">🤖</div>
            <div class="typing-dots"><span></span><span></span><span></span></div>
        `;
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
    }

    function hideBotTyping() {
        const el = document.getElementById('bot-typing');
        if (el) el.remove();
    }

    async function askBot(question) {
        addUserMsg(question);
        showBotTyping();
        
        // Arrêter la parole courante si on pose une nouvelle question
        if (window.speechSynthesis) window.speechSynthesis.cancel();

        try {
            const res = await fetch('{{ route("chatbot.handle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: question
                }),
            });
            const data = await res.json();
            hideBotTyping();
            addBotReply(data.reply);
        } catch (err) {
            hideBotTyping();
            addBotReply('❌ Erreur de connexion au serveur. Réessayez.');
        }
    }

    function sendBotMessage() {
        const input = document.getElementById('admin-bot-input');
        const msg = input.value.trim();
        if (!msg) return;
        input.value = '';
        askBot(msg);
    }

    document.getElementById('admin-bot-input')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendBotMessage();
    });
    
    // Initialiser au chargement
    document.addEventListener('DOMContentLoaded', initBotVolume);
</script>

@endif
@endauth