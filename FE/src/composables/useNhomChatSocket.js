import { onBeforeUnmount, ref, unref, watch } from "vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { sendNhomChatMessage } from "../services/chatApi";
import { getClientAccessToken } from "../utils/clientAuth";

window.Pusher = Pusher;

const DEFAULT_BACKEND_URL = "http://localhost:8000";
const EVENT_NAME = "nhom-chat.message.sent";

const reverbConfig = {
  key: import.meta.env.VITE_REVERB_APP_KEY || "",
  host: import.meta.env.VITE_REVERB_HOST || "127.0.0.1",
  port: Number(import.meta.env.VITE_REVERB_PORT || 8080),
  scheme: (import.meta.env.VITE_REVERB_SCHEME || "http").toLowerCase(),
  backendUrl: (import.meta.env.VITE_BACKEND_URL || DEFAULT_BACKEND_URL).replace(
    /\/+$/,
    "",
  ),
};

let echoInstance = null;
let echoToken = "";

function getChatId(chat) {
  if (!chat || typeof chat !== "object") return null;
  return chat.id ?? chat.id_tin_nhan ?? chat.chat_id ?? null;
}

function createEcho(token) {
  const useTLS = reverbConfig.scheme === "https";
  console.log('🔧 Creating Echo with config:', {
    broadcaster: 'reverb',
    key: reverbConfig.key,
    wsHost: reverbConfig.host,
    wsPort: reverbConfig.port,
    scheme: reverbConfig.scheme,
    authEndpoint: `${reverbConfig.backendUrl}/broadcasting/auth`,
    token: token ? 'present' : 'missing'
  });
  return new Echo({
    broadcaster: "reverb",
    key: reverbConfig.key,
    wsHost: reverbConfig.host,
    wsPort: reverbConfig.port,
    wssPort: reverbConfig.port,
    forceTLS: useTLS,
    enabledTransports: ["ws", "wss"],
    authEndpoint: `${reverbConfig.backendUrl}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    },
  });
}

function getEcho() {
  const token = getClientAccessToken();
  if (!token) {
    throw new Error("Missing client token in localStorage.");
  }

  if (!echoInstance || echoToken !== token) {
    if (echoInstance) {
      echoInstance.disconnect();
    }
    echoInstance = createEcho(token);
    echoToken = token;
  }

  return echoInstance;
}

function shutdownEchoIfIdle(activeChannelRef) {
  if (!echoInstance || activeChannelRef.value) return;
  echoInstance.disconnect();
  echoInstance = null;
  echoToken = "";
}

export function useNhomChatSocket(idNhomDuLichRef, initialMessages = []) {
  const messages = ref(
    Array.isArray(initialMessages) ? [...initialMessages] : [],
  );
  const sending = ref(false);
  const loadingSocket = ref(false);
  const socketError = ref("");
  const activeChannel = ref("");

  const appendMessage = (chat) => {
    if (!chat) return;
    const newId = getChatId(chat);
    if (newId == null) {
      messages.value = [...messages.value, chat];
      return;
    }
    const existed = messages.value.some((item) => getChatId(item) === newId);
    if (!existed) {
      messages.value = [...messages.value, chat];
    }
  };

  const leaveCurrentChannel = () => {
    if (!activeChannel.value || !echoInstance) return;
    echoInstance.leave(activeChannel.value);
    activeChannel.value = "";
  };

  const subscribeToGroup = (idNhomDuLich) => {
    if (!idNhomDuLich) return;

    const channelName = `nhom-chat.${idNhomDuLich}`;
    if (activeChannel.value === channelName) return;

    leaveCurrentChannel();
    loadingSocket.value = true;
    socketError.value = "";

    try {
      const echo = getEcho();
      console.log('🔗 Echo instance:', echo);

      const channel = echo.private(channelName);
      channel.listen(`.${EVENT_NAME}`, (event) => {
        console.log('📨 Received event:', EVENT_NAME, event);
        console.log('📨 Event data:', event?.chat);
        console.log('📨 Full event object:', event);
        appendMessage(event?.chat);
      });

      channel.listenToAll((event, data) => {
        console.log('📨 Received ALL event on channel:', channelName, event, data);
      });

      channel.subscribed(() => {
        console.log('📡 Channel subscribed:', channelName);
      });

      channel.error((error) => {
        console.error('⚠️ Channel error:', channelName, error);
      });

      activeChannel.value = channelName;
    } catch (error) {
      console.error('❌ Subscription failed:', error);
      socketError.value = error?.message || 'Cannot connect to realtime socket.';
    } finally {
      loadingSocket.value = false;
    }
  };

  const sendMessage = async ({ id_chi_tiet_nhom, message }) => {
    const id_nhom_du_lich = unref(idNhomDuLichRef);
    if (!id_nhom_du_lich) {
      throw new Error("Missing id_nhom_du_lich to send message.");
    }

    sending.value = true;
    try {
      const data = await sendNhomChatMessage({
        id_nhom_du_lich,
        id_chi_tiet_nhom,
        message,
      });
      appendMessage(data?.chat);
      return data;
    } finally {
      sending.value = false;
    }
  };

  watch(
    () => unref(idNhomDuLichRef),
    (newId) => {
      if (!newId) {
        leaveCurrentChannel();
        shutdownEchoIfIdle(activeChannel);
        return;
      }
      subscribeToGroup(newId);
    },
    { immediate: true },
  );

  onBeforeUnmount(() => {
    leaveCurrentChannel();
    shutdownEchoIfIdle(activeChannel);
  });

  return {
    messages,
    sending,
    loadingSocket,
    socketError,
    appendMessage,
    sendMessage,
    leaveCurrentChannel,
  };
}
