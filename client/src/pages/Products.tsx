import { useState } from "react";
import { trpc } from "../lib/trpc";
import { formatTHB } from "../lib/format";

export function ProductsPage() {
  const [search, setSearch] = useState("");
  const [showForm, setShowForm] = useState(false);
  const list = trpc.product.list.useQuery({ search });

  return (
    <div>
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-2xl font-bold">สินค้า</h1>
        <button className="btn-primary" onClick={() => setShowForm(true)}>
          + เพิ่มสินค้า
        </button>
      </div>
      <input
        className="input mb-4 max-w-md"
        placeholder="ค้นหา SKU หรือชื่อสินค้า"
        value={search}
        onChange={(e) => setSearch(e.target.value)}
      />
      {showForm && <ProductForm onClose={() => setShowForm(false)} />}
      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>SKU</th>
              <th>ชื่อ</th>
              <th>หมวด</th>
              <th>ทุน</th>
              <th>ราคาขาย</th>
              <th>คงเหลือ</th>
              <th>สถานะ</th>
            </tr>
          </thead>
          <tbody>
            {list.data?.map((p) => (
              <tr key={p.id}>
                <td className="font-mono">{p.sku}</td>
                <td>{p.name}</td>
                <td>{p.category}</td>
                <td className="text-right">{formatTHB(p.costPrice)}</td>
                <td className="text-right">{formatTHB(p.sellingPrice)}</td>
                <td className="text-right">{p.stockQuantity}</td>
                <td>
                  <span className={`badge ${p.isActive ? "bg-emerald-700" : "bg-slate-700"}`}>
                    {p.isActive ? "active" : "inactive"}
                  </span>
                </td>
              </tr>
            ))}
            {!list.data?.length && (
              <tr>
                <td colSpan={7} className="text-center text-slate-500 py-6">
                  ยังไม่มีสินค้า
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function ProductForm({ onClose }: { onClose: () => void }) {
  const utils = trpc.useUtils();
  const create = trpc.product.create.useMutation({
    onSuccess: async () => {
      await utils.product.list.invalidate();
      onClose();
    },
  });
  const [form, setForm] = useState({
    sku: "",
    name: "",
    category: "smartphone",
    costPrice: 0,
    sellingPrice: 0,
    stockQuantity: 1,
  });

  return (
    <form
      className="card mb-4 grid grid-cols-1 md:grid-cols-3 gap-3"
      onSubmit={(e) => {
        e.preventDefault();
        create.mutate(form);
      }}
    >
      <div>
        <label className="label">SKU</label>
        <input
          className="input"
          value={form.sku}
          onChange={(e) => setForm({ ...form, sku: e.target.value })}
        />
      </div>
      <div className="md:col-span-2">
        <label className="label">ชื่อสินค้า</label>
        <input
          className="input"
          value={form.name}
          onChange={(e) => setForm({ ...form, name: e.target.value })}
        />
      </div>
      <div>
        <label className="label">หมวดหมู่</label>
        <input
          className="input"
          value={form.category}
          onChange={(e) => setForm({ ...form, category: e.target.value })}
        />
      </div>
      <div>
        <label className="label">ราคาทุน</label>
        <input
          className="input"
          type="number"
          value={form.costPrice}
          onChange={(e) => setForm({ ...form, costPrice: Number(e.target.value) })}
        />
      </div>
      <div>
        <label className="label">ราคาขาย</label>
        <input
          className="input"
          type="number"
          value={form.sellingPrice}
          onChange={(e) => setForm({ ...form, sellingPrice: Number(e.target.value) })}
        />
      </div>
      <div>
        <label className="label">สต็อก</label>
        <input
          className="input"
          type="number"
          value={form.stockQuantity}
          onChange={(e) => setForm({ ...form, stockQuantity: Number(e.target.value) })}
        />
      </div>
      <div className="md:col-span-3 flex gap-2 justify-end">
        <button type="button" className="btn-ghost" onClick={onClose}>
          ยกเลิก
        </button>
        <button className="btn-primary" disabled={create.isPending}>
          {create.isPending ? "กำลังบันทึก..." : "บันทึก"}
        </button>
      </div>
      {create.error && (
        <div className="md:col-span-3 text-sm text-red-400">{create.error.message}</div>
      )}
    </form>
  );
}
