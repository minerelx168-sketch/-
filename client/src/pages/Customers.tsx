import { useState } from "react";
import { trpc } from "../lib/trpc";
import { formatThaiDateTime } from "../lib/format";

export function CustomersPage() {
  const [search, setSearch] = useState("");
  const [showForm, setShowForm] = useState(false);
  const list = trpc.customer.list.useQuery({ search });

  return (
    <div>
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-2xl font-bold">ลูกค้า</h1>
        <div className="flex gap-2">
          <a className="btn-ghost" href="/api/csv/customers">ดาวน์โหลด CSV</a>
          <button className="btn-primary" onClick={() => setShowForm(true)}>
            + เพิ่มลูกค้า
          </button>
        </div>
      </div>
      <input
        className="input mb-4 max-w-md"
        placeholder="ค้นหา ชื่อ / บัตรประชาชน / เบอร์"
        value={search}
        onChange={(e) => setSearch(e.target.value)}
      />
      {showForm && <CustomerForm onClose={() => setShowForm(false)} />}
      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>ชื่อ</th>
              <th>เลขบัตร</th>
              <th>เบอร์โทร</th>
              <th>LINE</th>
              <th>Bot</th>
              <th>สร้างเมื่อ</th>
            </tr>
          </thead>
          <tbody>
            {list.data?.map((c) => (
              <tr key={c.id}>
                <td>{c.fullName}</td>
                <td className="font-mono text-xs">{c.idCardNumber}</td>
                <td>{c.phoneNumber ?? "-"}</td>
                <td>{c.lineDisplayName ?? "-"}</td>
                <td>
                  <span className={`badge ${c.botActive ? "bg-emerald-700" : "bg-slate-700"}`}>
                    {c.botActive ? "on" : "off"}
                  </span>
                </td>
                <td className="text-xs text-slate-400">{formatThaiDateTime(c.createdAt)}</td>
              </tr>
            ))}
            {!list.data?.length && (
              <tr>
                <td colSpan={6} className="text-center text-slate-500 py-6">
                  ยังไม่มีลูกค้า
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function CustomerForm({ onClose }: { onClose: () => void }) {
  const utils = trpc.useUtils();
  const create = trpc.customer.create.useMutation({
    onSuccess: async () => {
      await utils.customer.list.invalidate();
      onClose();
    },
  });
  const [form, setForm] = useState({
    fullName: "",
    idCardNumber: "",
    phoneNumber: "",
    address: "",
    notes: "",
  });
  return (
    <form
      className="card mb-4 grid grid-cols-1 md:grid-cols-2 gap-3"
      onSubmit={(e) => {
        e.preventDefault();
        create.mutate(form);
      }}
    >
      <div>
        <label className="label">ชื่อ-นามสกุล</label>
        <input
          className="input"
          value={form.fullName}
          onChange={(e) => setForm({ ...form, fullName: e.target.value })}
        />
      </div>
      <div>
        <label className="label">เลขบัตรประชาชน (13 หลัก)</label>
        <input
          className="input"
          maxLength={13}
          value={form.idCardNumber}
          onChange={(e) => setForm({ ...form, idCardNumber: e.target.value.replace(/\D/g, "") })}
        />
      </div>
      <div>
        <label className="label">เบอร์โทร</label>
        <input
          className="input"
          value={form.phoneNumber}
          onChange={(e) => setForm({ ...form, phoneNumber: e.target.value })}
        />
      </div>
      <div>
        <label className="label">ที่อยู่</label>
        <input
          className="input"
          value={form.address}
          onChange={(e) => setForm({ ...form, address: e.target.value })}
        />
      </div>
      <div className="md:col-span-2">
        <label className="label">หมายเหตุ</label>
        <input
          className="input"
          value={form.notes}
          onChange={(e) => setForm({ ...form, notes: e.target.value })}
        />
      </div>
      <div className="md:col-span-2 flex gap-2 justify-end">
        <button type="button" className="btn-ghost" onClick={onClose}>ยกเลิก</button>
        <button className="btn-primary" disabled={create.isPending}>
          {create.isPending ? "กำลังบันทึก..." : "บันทึก"}
        </button>
      </div>
      {create.error && (
        <div className="md:col-span-2 text-sm text-red-400">{create.error.message}</div>
      )}
    </form>
  );
}
