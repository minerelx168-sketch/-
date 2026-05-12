import { useState } from "react";
import { Link } from "wouter";
import { trpc } from "../lib/trpc";
import { formatTHB, formatThaiDate, STATUS_LABELS } from "../lib/format";

export function ContractsPage() {
  const [status, setStatus] = useState<"" | "active" | "overdue" | "paid" | "defaulted" | "closed">("");
  const [search, setSearch] = useState("");
  const list = trpc.contract.list.useQuery({
    search: search || undefined,
    status: status || undefined,
  });

  return (
    <div>
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-2xl font-bold">สัญญาผ่อนชำระ</h1>
        <div className="flex gap-2">
          <a className="btn-ghost" href="/api/csv/contracts">ดาวน์โหลด CSV</a>
          <Link href="/contracts/new" className="btn-primary">+ สร้างสัญญา</Link>
        </div>
      </div>
      <div className="flex gap-3 mb-4">
        <input
          className="input max-w-xs"
          placeholder="ค้นหาเลขสัญญา"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        <select
          className="input max-w-xs"
          value={status}
          onChange={(e) => setStatus(e.target.value as typeof status)}
        >
          <option value="">ทุกสถานะ</option>
          <option value="active">ปกติ</option>
          <option value="overdue">ค้างชำระ</option>
          <option value="paid">ปิดสัญญา</option>
          <option value="defaulted">หนี้สูญ</option>
          <option value="closed">ปิดแล้ว</option>
        </select>
      </div>
      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>เลขสัญญา</th>
              <th>ราคาเครื่อง</th>
              <th>ดาวน์</th>
              <th>ค่างวด</th>
              <th>งวด</th>
              <th>เริ่ม</th>
              <th>สถานะ</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {list.data?.map((c) => {
              const meta = STATUS_LABELS[c.status] ?? { label: c.status, color: "bg-slate-700" };
              return (
                <tr key={c.id}>
                  <td className="font-mono">{c.contractNumber}</td>
                  <td className="text-right">{formatTHB(c.devicePrice)}</td>
                  <td className="text-right">{formatTHB(c.downPayment)}</td>
                  <td className="text-right">{formatTHB(c.installmentAmount)}</td>
                  <td className="text-center">{c.totalPeriods}</td>
                  <td>{formatThaiDate(c.startDate)}</td>
                  <td>
                    <span className={`badge ${meta.color}`}>{meta.label}</span>
                  </td>
                  <td>
                    <Link href={`/contracts/${c.id}`} className="text-brand-primary text-sm">
                      ดู
                    </Link>
                  </td>
                </tr>
              );
            })}
            {!list.data?.length && (
              <tr>
                <td colSpan={8} className="text-center text-slate-500 py-6">
                  ยังไม่มีสัญญา
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
