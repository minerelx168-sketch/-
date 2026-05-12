/**
 * Thin LINE Messaging API client.
 * `fetcher` is injectable for tests (default uses global fetch).
 */
type Fetcher = (url: string, init?: RequestInit) => Promise<Response>;

const LINE_API = "https://api.line.me/v2/bot";
const LINE_DATA_API = "https://api-data.line.me/v2/bot";

function getAccessToken(): string {
  const token = process.env.LINE_CHANNEL_ACCESS_TOKEN;
  if (!token) throw new Error("LINE_CHANNEL_ACCESS_TOKEN is not set");
  return token;
}

export function fetchWithTimeout(
  url: string,
  init: RequestInit & { timeoutMs?: number },
  fetcher: Fetcher = globalThis.fetch,
): Promise<Response> {
  const ctrl = new AbortController();
  const ms = init.timeoutMs ?? 15_000;
  const id = setTimeout(() => ctrl.abort(), ms);
  return fetcher(url, { ...init, signal: ctrl.signal }).finally(() => clearTimeout(id));
}

export type FlexMessage = {
  type: "flex";
  altText: string;
  contents: unknown;
};

export type LineMessage = { type: "text"; text: string } | FlexMessage;

export async function replyMessage(
  replyToken: string,
  messages: LineMessage[],
  fetcher: Fetcher = globalThis.fetch,
): Promise<void> {
  const res = await fetchWithTimeout(
    `${LINE_API}/message/reply`,
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${getAccessToken()}`,
      },
      body: JSON.stringify({ replyToken, messages }),
      timeoutMs: 15_000,
    },
    fetcher,
  );
  if (!res.ok) {
    const body = await res.text().catch(() => "");
    throw new Error(`LINE reply failed (${res.status}): ${body}`);
  }
}

export async function pushMessage(
  to: string,
  messages: LineMessage[],
  fetcher: Fetcher = globalThis.fetch,
): Promise<void> {
  const res = await fetchWithTimeout(
    `${LINE_API}/message/push`,
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${getAccessToken()}`,
      },
      body: JSON.stringify({ to, messages }),
      timeoutMs: 15_000,
    },
    fetcher,
  );
  if (!res.ok) {
    const body = await res.text().catch(() => "");
    throw new Error(`LINE push failed (${res.status}): ${body}`);
  }
}

export async function downloadMessageContent(
  messageId: string,
  fetcher: Fetcher = globalThis.fetch,
): Promise<Buffer> {
  const res = await fetchWithTimeout(
    `${LINE_DATA_API}/message/${encodeURIComponent(messageId)}/content`,
    {
      method: "GET",
      headers: { Authorization: `Bearer ${getAccessToken()}` },
      timeoutMs: 15_000,
    },
    fetcher,
  );
  if (!res.ok) {
    throw new Error(`LINE download content failed (${res.status})`);
  }
  const ab = await res.arrayBuffer();
  return Buffer.from(ab);
}

export type LineProfile = {
  displayName?: string;
  pictureUrl?: string;
  statusMessage?: string;
};

export async function getUserProfile(
  userId: string,
  fetcher: Fetcher = globalThis.fetch,
): Promise<LineProfile> {
  const res = await fetchWithTimeout(
    `${LINE_API}/profile/${encodeURIComponent(userId)}`,
    {
      method: "GET",
      headers: { Authorization: `Bearer ${getAccessToken()}` },
      timeoutMs: 10_000,
    },
    fetcher,
  );
  if (!res.ok) return {} as LineProfile;
  return (await res.json()) as LineProfile;
}

export type { Fetcher };
