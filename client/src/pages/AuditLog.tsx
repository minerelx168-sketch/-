import { trpc } from "../lib/trpc";
import { formatThaiDateTime } from "../lib/format";

export function AuditLogPage() {
  const list = trpc.audit.listAll.useQuery({ limit: 300 });

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">Audit Log</h1>
      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>เวลา</th>
              <th>action</th>
              <th>สัญญา</th>
              <th>คำอธิบาย</th>
              <th>เดิม → ใหม่</th>
              <th>หมายเหตุ</th>
              <th>โดย</th>
            </tr>
          </thead>
          <tbody>
            {list.data?.map((a) => (
              <tr key={a.id}>
                <td className="text-xs text-slate-400 whitespace-nowrap">
                  {formatThaiDateTime(a.createdAt)}
                </td>
                <td className="font-mono text-xs">{a.actionType}</td>
                <td className="font-mono text-xs">{a.contractNumber ?? "-"}</td>
                <td className="text-xs">{a.description ?? "-"}</td>
                <td className="text-xs">
                  {a.previousValue || a.newValue ? (
                    <>
                      <span className="text-slate-500">{a.previousValue ?? "-"}</span>{" "}
                      → <span className="text-slate-100">{a.newValue ?? "-"}</span>
                    </>
                  ) : (
                    "-"
                  )}
                </td>
                <td className="text-xs text-slate-300">{a.remark ?? "-"}</td>
                <td className="text-xs text-slate-400">{a.performedBy ?? "-"}</td>
              </tr>
            ))}
            {!list.data?.length && (
              <tr>
                <td colSpan={7} className="text-center text-slate-500 py-6">
                  ยังไม่มีข้อมูล
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
