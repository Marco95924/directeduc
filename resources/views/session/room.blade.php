<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Salle de Visioconférence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/peerjs@1.5.2/dist/peerjs.min.js"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col">

    <!-- Header -->
    <div class="bg-slate-900 border-b border-slate-800 p-4 flex justify-between items-center">
        <div>
            <h1 class="font-bold text-lg text-emerald-400">{{ $session->sujet }}</h1>
            <p class="text-xs text-slate-400">Niveau: {{ $session->niveau }} (Rôle: {{ $role == 'student' ? 'Élève' : 'Enseignant' }})</p>
        </div>
        <a href="{{ route('home') }}" class="bg-red-600/20 text-red-400 border border-red-600/30 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-600 hover:text-white transition">
            Quitter la session
        </a>
    </div>

    <!-- Grille Vidéo -->
    <div class="flex-1 p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Vidéo Distante -->
        <div class="relative bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden flex items-center justify-center min-h-[300px]">
            <video id="remoteVideo" autoplay playsinline class="w-full h-full object-cover"></video>
            <div id="waitingText" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/90 text-slate-400">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-400 mb-3"></div>
                <p>En attente du {{ $role == 'student' ? 'professeur' : 'de l\'élève' }}...</p>
            </div>
        </div>

        <!-- Vidéo Locale (Caméra ou Écran) -->
        <div class="relative bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden flex items-center justify-center min-h-[300px]">
            <video id="localVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
            <span id="localLabel" class="absolute bottom-3 left-3 bg-slate-900/80 backdrop-blur px-3 py-1 rounded-lg text-xs border border-slate-700">Vous</span>
        </div>
    </div>

    <!-- Barre de Contrôles -->
    <div class="bg-slate-900 border-t border-slate-800 p-4 flex justify-center items-center gap-3 flex-wrap">
        <!-- Bouton Micro -->
        <button id="toggleAudioBtn" onclick="toggleAudio()" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold px-4 py-2.5 rounded-xl border border-slate-700 transition">
            <span id="audioIcon">🎤</span>
            <span id="audioText">Micro On</span>
        </button>

        <!-- Bouton Caméra -->
        <button id="toggleVideoBtn" onclick="toggleVideo()" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold px-4 py-2.5 rounded-xl border border-slate-700 transition">
            <span id="videoIcon">📹</span>
            <span id="videoText">Caméra On</span>
        </button>

        <!-- Bouton Partage d'écran -->
        <button id="toggleScreenBtn" onclick="toggleScreenShare()" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold px-4 py-2.5 rounded-xl transition">
            <span id="screenIcon">🖥️</span>
            <span id="screenText">Partager l'écran</span>
        </button>
    </div>

    <!-- Script WebRTC PeerJS + Contrôles -->
    <script>
        const sessionId = "{{ $session->session_id }}";
        const role = "{{ $role }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        const waitingText = document.getElementById('waitingText');
        const localLabel = document.getElementById('localLabel');

        let localStream;
        let screenStream = null;
        let currentCall = null;
        let peer;
        let isAudioMuted = false;
        let isVideoOff = false;
        let isSharingScreen = false;

        // 1. Initialiser le flux Caméra/Micro
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                localStream = stream;
                localVideo.srcObject = stream;
                initPeer();
            })
            .catch(err => alert("Erreur d'accès à la caméra/micro: " + err));

        // 2. Initialiser PeerJS
        function initPeer() {
            peer = new Peer();

            peer.on('open', id => {
                fetch(`/api/session/${sessionId}/peer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ role: role, peer_id: id })
                });

                pollTargetPeer();
            });

            peer.on('call', call => {
                currentCall = call;
                const activeStream = isSharingScreen ? screenStream : localStream;
                call.answer(activeStream);

                call.on('stream', remoteStream => {
                    remoteVideo.srcObject = remoteStream;
                    waitingText.classList.add('hidden');
                });
            });
        }

        // 3. Détecter l'autre participant
        function pollTargetPeer() {
            const interval = setInterval(() => {
                fetch(`/api/session/${sessionId}/peer`)
                    .then(res => res.json())
                    .then(data => {
                        const targetPeerId = role === 'student' ? data.teacher : data.student;

                        if (targetPeerId) {
                            clearInterval(interval);
                            connectToPeer(targetPeerId);
                        }
                    });
            }, 2000);
        }

        // 4. Passer l'appel vers l'autre pair
        function connectToPeer(targetPeerId) {
            const activeStream = isSharingScreen ? screenStream : localStream;
            currentCall = peer.call(targetPeerId, activeStream);

            currentCall.on('stream', remoteStream => {
                remoteVideo.srcObject = remoteStream;
                waitingText.classList.add('hidden');
            });
        }

        // -------------------------------------------------------------
        // Logique de Partage d'Écran (Screen Sharing)
        // -------------------------------------------------------------
        async function toggleScreenShare() {
            if (!isSharingScreen) {
                try {
                    // Capturer l'écran de l'utilisateur
                    screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const screenTrack = screenStream.getVideoTracks()[0];

                    // Si l'utilisateur clique sur "Arrêter le partage" depuis la barre système native
                    screenTrack.onended = () => {
                        stopScreenShare();
                    };

                    // Remplacer la piste vidéo locale
                    replaceVideoTrack(screenTrack);
                    localVideo.srcObject = screenStream;
                    localLabel.textContent = "Votre écran";

                    isSharingScreen = true;
                    updateScreenBtnUI(true);

                } catch (err) {
                    console.error("Erreur lors du partage d'écran:", err);
                }
            } else {
                stopScreenShare();
            }
        }

        function stopScreenShare() {
            if (screenStream) {
                screenStream.getTracks().forEach(track => track.stop());
                screenStream = null;
            }

            const cameraTrack = localStream.getVideoTracks()[0];
            replaceVideoTrack(cameraTrack);
            localVideo.srcObject = localStream;
            localLabel.textContent = "Vous";

            isSharingScreen = false;
            updateScreenBtnUI(false);
        }

        // Remplacer la piste vidéo en direct dans la connexion PeerJS active
        function replaceVideoTrack(newTrack) {
            if (currentCall && currentCall.peerConnection) {
                const senders = currentCall.peerConnection.getSenders();
                const videoSender = senders.find(sender => sender.track && sender.track.kind === 'video');

                if (videoSender) {
                    videoSender.replaceTrack(newTrack);
                }
            }
        }

        function updateScreenBtnUI(active) {
            const btn = document.getElementById('toggleScreenBtn');
            const icon = document.getElementById('screenIcon');
            const text = document.getElementById('screenText');

            if (active) {
                btn.className = "flex items-center gap-2 bg-red-600 hover:bg-red-500 text-white font-bold px-4 py-2.5 rounded-xl transition";
                icon.textContent = "🛑";
                text.textContent = "Arrêter le partage";
            } else {
                btn.className = "flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold px-4 py-2.5 rounded-xl transition";
                icon.textContent = "🖥️";
                text.textContent = "Partager l'écran";
            }
        }

        // -------------------------------------------------------------
        // Micro & Caméra
        // -------------------------------------------------------------
        function toggleAudio() {
            if (!localStream) return;
            const audioTrack = localStream.getAudioTracks()[0];
            
            if (audioTrack) {
                isAudioMuted = !isAudioMuted;
                audioTrack.enabled = !isAudioMuted;

                const btn = document.getElementById('toggleAudioBtn');
                const icon = document.getElementById('audioIcon');
                const text = document.getElementById('audioText');

                if (isAudioMuted) {
                    btn.classList.replace('bg-slate-800', 'bg-red-600/20');
                    btn.classList.add('border-red-500/50', 'text-red-400');
                    icon.textContent = '🔇';
                    text.textContent = 'Micro Coupé';
                } else {
                    btn.classList.replace('bg-red-600/20', 'bg-slate-800');
                    btn.classList.remove('border-red-500/50', 'text-red-400');
                    icon.textContent = '🎤';
                    text.textContent = 'Micro On';
                }
            }
        }

        function toggleVideo() {
            if (!localStream) return;
            const videoTrack = localStream.getVideoTracks()[0];

            if (videoTrack) {
                isVideoOff = !isVideoOff;
                videoTrack.enabled = !isVideoOff;

                const btn = document.getElementById('toggleVideoBtn');
                const icon = document.getElementById('videoIcon');
                const text = document.getElementById('videoText');

                if (isVideoOff) {
                    btn.classList.replace('bg-slate-800', 'bg-red-600/20');
                    btn.classList.add('border-red-500/50', 'text-red-400');
                    icon.textContent = '📷';
                    text.textContent = 'Caméra Désactivée';
                } else {
                    btn.classList.replace('bg-red-600/20', 'bg-slate-800');
                    btn.classList.remove('border-red-500/50', 'text-red-400');
                    icon.textContent = '📹';
                    text.textContent = 'Caméra On';
                }
            }
        }
    </script>
</body>
</html>