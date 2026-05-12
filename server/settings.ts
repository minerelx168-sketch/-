import { eq } from "drizzle-orm";
import { getDb, schema } from "./db.js";

export async function getSetting(key: string): Promise<string | null> {
  const db = getDb();
  const [row] = await db
    .select()
    .from(schema.systemSettings)
    .where(eq(schema.systemSettings.settingKey, key))
    .limit(1);
  return row?.settingValue ?? null;
}

export async function listSettings(): Promise<Record<string, string>> {
  const db = getDb();
  const rows = await db.select().from(schema.systemSettings);
  const out: Record<string, string> = {};
  for (const r of rows) out[r.settingKey] = r.settingValue ?? "";
  return out;
}

export async function setSetting(key: string, value: string, performedBy: string) {
  const db = getDb();
  const existing = await getSetting(key);
  if (existing === null) {
    await db.insert(schema.systemSettings).values({ settingKey: key, settingValue: value });
  } else if (existing !== value) {
    await db
      .update(schema.systemSettings)
      .set({ settingValue: value })
      .where(eq(schema.systemSettings.settingKey, key));
  } else {
    return;
  }
  await db.insert(schema.auditLogs).values({
    actionType: "other",
    description: `ตั้งค่า ${key}`,
    previousValue: existing ?? "(unset)",
    newValue: value,
    performedBy,
  });
}
