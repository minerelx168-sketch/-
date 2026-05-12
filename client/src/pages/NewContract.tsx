import { useMemo, useState } from "react";
import { useLocation } from "wouter";
import { trpc } from "../lib/trpc";
import { formatTHB } from "../lib/format";

export function NewContractPage() {
  const [, setLocation] = useLocation();
  const products = trpc.product.list.useQuery({ activeOnly: true });
  const customers = trpc.customer.list.useQuery({});
  const create = trpc.contract.create.useMutation({
    onSuccess: (data) => {
      if (data) setLocation(`/contracts/${data.id}`);
    },
  });

  const [productId, setProductId] = useState<number | null>(null);
  const [customerId, setCustomerId] = useState<number | null>(null);
  const [devicePrice, setDevicePrice] = useState(0);
  const [downPayment, setDownPayment] = useState(0);
  const [totalPeriods, setTotalPeriods] = useState(10);
  const [installmentAmount, setInstallmentAmount] = useState<number | null>(null);
  const [penaltyRate, setPenaltyRate] = useState(100);
  const [unlockFee, setUnlockFee] = useState(500);
  const [startDate, setStartDate] = useState(new Date().toISOString().slice(0, 10));

  const suggestedInstallment = useMemo(() => {
    const remaining = Math.max(0, devicePrice - downPayment);
    return totalPeriods > 0 ? Math.round((remaining / totalPeriods) * 100) / 100 : 0;
  }, [devicePrice, downPayment, totalPeriods]);

  const finalInstallment = installmentAmount ?? suggestedInstallment;

  const submit = () => {
    if (!productId || !customerId) return;
    create.mutate({
      productId,
      customerId,
      devicePrice,
      downPayment,
      totalPeriods,
      installmentAmount: finalInstallment,
      penaltyRate,
      unlockFee,
      startDate: new Date(startDate),
    });
  };

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">สร้างสัญญาใหม่</h1>
      <div className="card grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label className="label">สินค้า</label>
          <select
            className="input"
            value={productId ?? ""}
            onChange={(e) => {
              const id = Number(e.target.value);
              setProductId(id || null);
              const p = products.data?.find((p) => p.id === id);
              if (p) setDevicePrice(Number(p.sellingPrice));
            }}
          >
            <option value="">-- เลือก --</option>
            {products.data?.map((p) => (
              <option key={p.id} value={p.id}>
                {p.name} (SKU {p.sku}, คงเหลือ {p.stockQuantity})
              </option>
            ))}
          </select>
        </div>
        <div>
          <label className="label">ลูกค้า</label>
          <select
            className="input"
            value={customerId ?? ""}
            onChange={(e) => setCustomerId(Number(e.target.value) || null)}
          >
            <option value="">-- เลือก --</option>
            {customers.data?.map((c) => (
              <option key={c.id} value={c.id}>
                {c.fullName} ({c.idCardNumber})
              </option>
            ))}
          </select>
        </div>
        <div>
          <label className="label">ราคาเครื่อง (บาท)</label>
          <input
            className="input"
            type="number"
            value={devicePrice}
            onChange={(e) => setDevicePrice(Number(e.target.value))}
          />
        </div>
        <div>
          <label className="label">เงินดาวน์ (บาท)</label>
          <input
            className="input"
            type="number"
            value={downPayment}
            onChange={(e) => setDownPayment(Number(e.target.value))}
          />
        </div>
        <div>
          <label className="label">จำนวนงวด</label>
          <input
            className="input"
            type="number"
            value={totalPeriods}
            onChange={(e) => setTotalPeriods(Number(e.target.value))}
          />
        </div>
        <div>
          <label className="label">ค่างวด/เดือน (auto: {formatTHB(suggestedInstallment)})</label>
          <input
            className="input"
            type="number"
            placeholder={String(suggestedInstallment)}
            value={installmentAmount ?? ""}
            onChange={(e) =>
              setInstallmentAmount(e.target.value === "" ? null : Number(e.target.value))
            }
          />
        </div>
        <div>
          <label className="label">ค่าปรับ/วัน (บาท)</label>
          <input
            className="input"
            type="number"
            value={penaltyRate}
            onChange={(e) => setPenaltyRate(Number(e.target.value))}
          />
        </div>
        <div>
          <label className="label">ค่าปลดล็อค (บาท)</label>
          <input
            className="input"
            type="number"
            value={unlockFee}
            onChange={(e) => setUnlockFee(Number(e.target.value))}
          />
        </div>
        <div>
          <label className="label">วันเริ่มสัญญา</label>
          <input
            className="input"
            type="date"
            value={startDate}
            onChange={(e) => setStartDate(e.target.value)}
          />
        </div>
        <div className="md:col-span-2 text-sm text-slate-300 border-t border-slate-800 pt-3">
          <div>
            ยอดผ่อนทั้งหมด:{" "}
            <span className="font-semibold">
              {formatTHB(Math.max(0, devicePrice - downPayment))} บาท
            </span>
          </div>
          <div>
            งวด × ค่างวด:{" "}
            <span className="font-semibold">
              {totalPeriods} × {formatTHB(finalInstallment)} ={" "}
              {formatTHB(totalPeriods * finalInstallment)} บาท
            </span>
          </div>
        </div>
        <div className="md:col-span-2 flex justify-end gap-2">
          <button className="btn-ghost" onClick={() => setLocation("/contracts")}>
            ยกเลิก
          </button>
          <button
            className="btn-primary"
            disabled={!productId || !customerId || create.isPending}
            onClick={submit}
          >
            {create.isPending ? "กำลังสร้าง..." : "สร้างสัญญา"}
          </button>
        </div>
        {create.error && (
          <div className="md:col-span-2 text-sm text-red-400">{create.error.message}</div>
        )}
      </div>
    </div>
  );
}
