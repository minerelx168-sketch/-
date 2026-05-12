import { trpc } from "../lib/trpc";
import { formatThaiDateTime } from "../lib/format";

export function MessagesPage() {
  const list = trpc.line.messages.useQuery({ limit: 200 });

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">ข้อความ LINE</h1>
      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>เวลา</th>
              <th>ทิศทาง</th>
              <th>ประเภท</th>
              <th>LINE userId</th>
              <th>เนื้อหา</th>
            </tr>
          </thead>
          <tbody>
            {list.data?.map((m) => (
              <tr key={m.id}>
                <td className="text-xs">{formatThaiDateTime(m.createdAt)}</td>
                <td>
                  <span
                    className={`badge ${
                      m.direction === "incoming" ? "bg-slate-700" : "bg-brand-primary"
                    }`}
                  >
                    {m.direction}
                  </span>
                </td>
                <td className="text-xs font-mono">{m.messageType}</td>
                <td className="text-xs font-mono">{m.lineUserId?.slice(0, 12) ?? "-"}…</td>
                <td className="text-xs">{m.messageContent ?? "-"}</td>
              </tr>
            ))}
            {!list.data?.length && (
              <tr>
                <td colSpan={5} className="text-center text-slate-500 py-6">
                  ยังไม่มีข้อความ
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
