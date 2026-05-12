import { trpc } from "../lib/trpc";
import { formatThaiDateTime } from "../lib/format";

export function LineConnectionsPage() {
  const list = trpc.line.connections.useQuery();
  const utils = trpc.useUtils();
  const toggle = trpc.line.toggleBot.useMutation({
    onSuccess: () => {
      void utils.line.connections.invalidate();
    },
  });

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">ผู้ใช้ LINE ที่ผูกแล้ว</h1>
      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>ลูกค้า</th>
              <th>เลขบัตร</th>
              <th>LINE Display Name</th>
              <th>userId</th>
              <th>Bot</th>
              <th>อัพเดตล่าสุด</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {list.data?.map((c) => (
              <tr key={c.id}>
                <td>{c.fullName}</td>
                <td className="font-mono text-xs">{c.idCardNumber}</td>
                <td>{c.lineDisplayName ?? "-"}</td>
                <td className="font-mono text-xs">{c.lineUserId?.slice(0, 16)}…</td>
                <td>
                  <span className={`badge ${c.botActive ? "bg-emerald-700" : "bg-slate-700"}`}>
                    {c.botActive ? "on" : "off"}
                  </span>
                </td>
                <td className="text-xs text-slate-400">{formatThaiDateTime(c.updatedAt)}</td>
                <td>
                  <button
                    className="btn-ghost text-xs"
                    onClick={() =>
                      toggle.mutate({ customerId: c.id, botActive: c.botActive ? 0 : 1 })
                    }
                  >
                    {c.botActive ? "ปิด bot" : "เปิด bot"}
                  </button>
                </td>
              </tr>
            ))}
            {!list.data?.length && (
              <tr>
                <td colSpan={7} className="text-center text-slate-500 py-6">
                  ยังไม่มีลูกค้าที่ผูก LINE
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
