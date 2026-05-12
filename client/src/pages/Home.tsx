import { trpc } from "../lib/trpc";
import { formatTHB } from "../lib/format";

export function HomePage() {
  const stats = trpc.contract.stats.useQuery();
  const products = trpc.product.list.useQuery({ activeOnly: true });
  const customers = trpc.customer.list.useQuery({});

  const s = stats.data;
  const cards = [
    { label: "สัญญาทั้งหมด", value: s?.total ?? 0, color: "text-slate-100" },
    { label: "ปกติ", value: s?.active ?? 0, color: "text-emerald-400" },
    { label: "ค้างชำระ", value: s?.overdue ?? 0, color: "text-red-400" },
    { label: "ปิดสัญญา", value: s?.paid ?? 0, color: "text-blue-400" },
  ];

  return (
    <div>
      <h1 className="text-2xl font-bold mb-6">ภาพรวมระบบ</h1>
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {cards.map((c) => (
          <div key={c.label} className="card">
            <div className="text-xs text-slate-400">{c.label}</div>
            <div className={`text-3xl font-bold mt-1 ${c.color}`}>{formatTHB(Number(c.value))}</div>
          </div>
        ))}
      </div>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="card">
          <div className="text-sm text-slate-400 mb-2">สินค้าใน stock</div>
          <div className="text-2xl font-bold">{products.data?.length ?? 0} รายการ</div>
        </div>
        <div className="card">
          <div className="text-sm text-slate-400 mb-2">ลูกค้าทั้งหมด</div>
          <div className="text-2xl font-bold">{customers.data?.length ?? 0} คน</div>
        </div>
      </div>
    </div>
  );
}
