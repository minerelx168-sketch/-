declare global {
  interface Window {
    liff?: {
      init: (opts: { liffId: string }) => Promise<void>;
      isLoggedIn: () => boolean;
      login: (opts?: { redirectUri?: string }) => void;
      getProfile: () => Promise<{ userId: string; displayName?: string; pictureUrl?: string }>;
      ready: Promise<void>;
      closeWindow: () => void;
      isInClient: () => boolean;
    };
    QRCode?: {
      toDataURL: (
        text: string,
        opts?: { width?: number; margin?: number },
      ) => Promise<string>;
    };
  }
}

const LIFF_SDK_URL = "https://static.line-scdn.net/liff/edge/2/sdk.js";
const QRCODE_SDK_URL = "https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js";

const loadedScripts = new Set<string>();

export function loadScript(src: string): Promise<void> {
  if (loadedScripts.has(src)) return Promise.resolve();
  return new Promise((resolve, reject) => {
    const existing = document.querySelector(`script[src="${src}"]`);
    if (existing) {
      loadedScripts.add(src);
      resolve();
      return;
    }
    const s = document.createElement("script");
    s.src = src;
    s.async = true;
    s.onload = () => {
      loadedScripts.add(src);
      resolve();
    };
    s.onerror = () => reject(new Error(`Failed to load ${src}`));
    document.head.appendChild(s);
  });
}

export async function loadLiffSdk(): Promise<void> {
  await loadScript(LIFF_SDK_URL);
}

export async function loadQrCodeSdk(): Promise<void> {
  await loadScript(QRCODE_SDK_URL);
}

/**
 * Resolve a LINE userId for the current visitor.
 * - In LINE in-app browser with a configured LIFF ID: real LIFF profile.
 * - Outside (e.g. local dev / desktop): falls back to the `?u=` query param
 *   so admins can test by visiting /pay?u=Uxxxxx directly.
 */
export async function resolveLineUserId(): Promise<{
  userId: string | null;
  displayName?: string;
  reason?: string;
}> {
  const params = new URLSearchParams(window.location.search);
  const override = params.get("u");
  if (override) return { userId: override, displayName: params.get("name") ?? undefined };

  const liffId = (import.meta as unknown as { env?: { VITE_LIFF_ID?: string } }).env?.VITE_LIFF_ID;
  if (!liffId) {
    return { userId: null, reason: "VITE_LIFF_ID not configured" };
  }
  try {
    await loadLiffSdk();
    await window.liff!.init({ liffId });
    if (!window.liff!.isLoggedIn()) {
      window.liff!.login();
      return { userId: null, reason: "redirecting to LINE login" };
    }
    const profile = await window.liff!.getProfile();
    return { userId: profile.userId, displayName: profile.displayName };
  } catch (err) {
    return { userId: null, reason: (err as Error).message };
  }
}
