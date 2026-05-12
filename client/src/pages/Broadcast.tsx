import { useState } from "react";
import { trpc } from "../lib/trpc";
import { formatTHB } from "../lib/format";

export function BroadcastPage() {
  const [status, setStatus] = useState<"" | "active" | "overdue">("overdue");
  const [minOverdue, setMinOverdue] = useState<number>(1);
  const [message, setMessage] = useState(
    "เรียนคุณ {name}\nสัญญา {contractNumber} มียอดค้างชำระ {remaining} บาท กรุณาชำระภายในวันนี้ค่ะ",
  );

  const filter = {
    status: status || undefined,
    minOverdueDays: minOverdue || undefined,
  } as const;

  const preview = trpc.broadcast.preview.useQuery(filter);
  const utils = trpc.useUtils();
  const send = trpc.broadcast.send.useMutation({
    onSuccess: () => {
      void utils.broadcast.preview.invalidate();
    },
  });
  const syncMut = trpc.scheduled.runDailySync.useMutation();

  return (
    <div>
      <div className="flex items-center justify-between mb-4">
        <h1 className="text-2xl font-bold">กระจายข้อความ / ทวงหนี้</h1>
        <button
          className="btn-ghost"
          onClick={() => syncMut.mutate({ dryRun: true })}
          disabled={syncMut.isPending}
        >
          {syncMut.isPending ? "กำลังประเมิน..." : "Dry-run daily sync"}
        </button>
      </div>

      {syncMut.data && (
        <div className="card mb-4 text-sm">
          <div className="font-semibold mb-1">ผล dry-run:</div>
          <div className="text-slate-300">
            สแกน {syncMut.data.scannedContracts} • อัพเดต overdue {syncMut.data.updatedOverdue} •
            จะส่ง {syncMut.data.remindersSent} • แจ้งเจ้าของ {syncMut.data.ownerNotified} •
            error {syncMut.data.errors}
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div className="card">
          <div className="text-sm font-semibold mb-3">เงื่อนไขผู้รับ</div>
          <label className="label">สถานะ</label>
          <select
            className="input mb-3"
            value={status}
            onChange={(e) => setStatus(e.target.value as typeof status)}
          >
            <option value="">ทุกสถานะ (botActive=1)</option>
            <option value="overdue">ค้างชำระ</option>
            <option value="active">ปกติ</option>
          </select>
          <label className="label">ค้างขั้นต่ำ (วัน)</label>
          <input
            className="input mb-3"
            type="number"
            min={0}
            value={minOverdue}
            onChange={(e) => setMinOverdue(Number(e.target.value))}
          />
          <div className="text-xs text-slate-400 mt-2">
            ผู้รับที่ตรงเงื่อนไข: <span className="font-semibold">{preview.data?.length ?? 0}</span> ราย
          </div>
        </div>
        <div className="card">
          <div className="text-sm font-semibold mb-3">ข้อความ</div>
          <label className="label">ใส่ตัวแปร: {"{name}"}, {"{contractNumber}"}, {"{remaining}"}</label>
          <textarea
            className="input"
            rows={6}
            value={message}
            onChange={(e) => setMessage(e.target.value)}
          />
          <div className="flex gap-2 justify-end mt-3">
            <button
              className="btn-ghost"
              onClick={() => send.mutate({ filter, message, dryRun: true })}
              disabled={send.isPending || !preview.data?.length}
            >
              Dry-run
            </button>
            <button
              className="btn-primary"
              onClick={() => {
                if (confirm(`ส่งจริงไปยัง ${preview.data?.length ?? 0} ราย?`)) {
                  send.mutate({ filter, message, dryRun: false });
                }
              }}
              disabled={send.isPending || !preview.data?.length}
            >
              {send.isPending ? "กำลังส่ง..." : "ส่งจริง"}
            </button>
          </div>
          {send.data && (
            <div className="mt-3 text-sm">
              ✅ ส่งสำเร็จ {send.data.sent}/{send.data.total} • failed {send.data.failed}
            </div>
          )}
          {send.error && <div className="mt-3 text-sm text-red-400">{send.error.message}</div>}
        </div>
      </div>

      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>เลขสัญญา</th>
              <th>ลูกค้า</th>
              <th className="text-right">ค้าง (วัน)</th>
              <th className="text-right">ยอดคงเหลือ</th>
              <th>LINE userId</th>
            </tr>
          </thead>
          <tbody>
            {preview.data?.map((r) => (
              <tr key={r.contractId}>
                <td className="font-mono">{r.contractNumber}</td>
                <td>{r.customerName}</td>
                <td className="text-right">{r.overdueDays}</td>
                <td className="text-right">{formatTHB(r.remainingAmount)}</td>
                <td className="font-mono text-xs">{r.lineUserId.slice(0, 16)}…</td>
              </tr>
            ))}
            {!preview.data?.length && (
              <tr>
                <td colSpan={5} className="text-center text-slate-500 py-6">
                  ไม่มีผู้รับที่ตรงเงื่อนไข
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
