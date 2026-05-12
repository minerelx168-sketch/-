import { useEffect, useState } from "react";
import { trpc } from "../lib/trpc";

const KNOWN_KEYS: { key: string; label: string; description?: string }[] = [
  { key: "owner_name", label: "ชื่อเจ้าของร้าน" },
  { key: "default_penalty_rate", label: "ค่าปรับ/วัน (default)", description: "ใช้กับสัญญาใหม่ที่ไม่ระบุ" },
  { key: "default_unlock_fee", label: "ค่าปลดล็อค (default)" },
  { key: "default_total_periods", label: "จำนวนงวด (default)" },
  { key: "broadcast_signature", label: "ลายเซ็นท้ายข้อความ broadcast" },
];

export function SettingsPage() {
  const list = trpc.settings.list.useQuery();
  const utils = trpc.useUtils();
  const setMut = trpc.settings.set.useMutation({
    onSuccess: () => utils.settings.list.invalidate(),
  });
  const [values, setValues] = useState<Record<string, string>>({});

  useEffect(() => {
    if (list.data) setValues(list.data);
  }, [list.data]);

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">ตั้งค่าระบบ</h1>
      <div className="card grid grid-cols-1 md:grid-cols-2 gap-4">
        {KNOWN_KEYS.map((k) => (
          <div key={k.key}>
            <label className="label">
              {k.label}
              <span className="text-slate-500 ml-2 text-[10px] font-mono">{k.key}</span>
            </label>
            <div className="flex gap-2">
              <input
                className="input"
                value={values[k.key] ?? ""}
                onChange={(e) => setValues({ ...values, [k.key]: e.target.value })}
              />
              <button
                className="btn-primary"
                onClick={() => setMut.mutate({ key: k.key, value: values[k.key] ?? "" })}
                disabled={setMut.isPending}
              >
                บันทึก
              </button>
            </div>
            {k.description && (
              <div className="text-[11px] text-slate-500 mt-1">{k.description}</div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
