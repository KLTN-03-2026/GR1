import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

export function createChatEcho(token) {
    const backendUrl = import.meta.env.VITE_BACKEND_URL ?? 'http://127.0.0.1:8000';

    return new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
        forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: `${backendUrl}/broadcasting/auth`,
        auth: {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
            },
        },
    });
}

export function joinNhomChatChannel(echo, nhomDuLichId, onMessage) {
    return echo
        .private(`nhom-chat.${nhomDuLichId}`)
        .listen('.nhom-chat.message.sent', (event) => {
            onMessage(event.chat);
        });
}
