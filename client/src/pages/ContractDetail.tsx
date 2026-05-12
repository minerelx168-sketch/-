import { useState } from "react";
import { Link } from "wouter";
import { trpc } from "../lib/trpc";
import { formatTHB, formatThaiDate, STATUS_LABELS } from "../lib/format";

export function ContractDetailPage({ id }: { id: number }) {
  const contract = trpc.contract.getById.useQuery(id);
  const installments = trpc.installment.byContract.useQuery(id);
  const payments = trpc.payment.listByContract.useQuery(id);
  const quoteInst = trpc.contract.quote.useQuery({ contractId: id, type: "installment" });
  const quoteFull = trpc.contract.quote.useQuery({ contractId: id, type: "full" });
  const audit = trpc.audit.listByContract.useQuery(id);
  const [showPay, setShowPay] = useState(false);

  if (contract.isLoading) return <div className="text-slate-400">กำลังโหลด...</div>;
  if (!contract.data) return <div className="text-red-400">ไม่พบสัญญา</div>;
  const c = contract.data;
  const statusMeta = STATUS_LABELS[c.status] ?? { label: c.status, color: "bg-slate-700" };

  return (
    <div>
      <div className="flex items-center justify-between mb-4">
        <div>
          <Link href="/contracts" className="text-sm text-slate-400 hover:text-slate-200">
            ← กลับ
          </Link>
          <h1 className="text-2xl font-bold mt-1">
            สัญญา <span className="font-mono">{c.contractNumber}</span>
          </h1>
        </div>
        <span className={`badge ${statusMeta.color}`}>{statusMeta.label}</span>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <InfoCard title="ราคาเครื่อง" value={`${formatTHB(c.devicePrice)} บาท`} />
        <InfoCard title="เงินดาวน์" value={`${formatTHB(c.downPayment)} บาท`} />
        <InfoCard title="ยอดผ่อนทั้งหมด" value={`${formatTHB(c.totalAmount)} บาท`} />
        <InfoCard title="ค่างวด/เดือน" value={`${formatTHB(c.installmentAmount)} บาท`} />
        <InfoCard title="จำนวนงวด" value={String(c.totalPeriods)} />
        <InfoCard title="ค่าปรับ/วัน" value={`${formatTHB(c.penaltyRate)} บาท`} />
        <InfoCard title="ค่าปลดล็อค" value={`${formatTHB(c.unlockFee)} บาท`} />
        <InfoCard title="วันเริ่ม" value={formatThaiDate(c.startDate)} />
        <InfoCard title="สถานะเครื่อง" value={c.lockStatus} />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div className="card">
          <div className="text-sm text-slate-400">ต่อดอก (ค่างวด + ค่าปรับ)</div>
          <div className="text-3xl font-bold text-emerald-400 mt-2">
            {formatTHB(quoteInst.data?.total ?? 0)} บาท
          </div>
          <div className="text-xs text-slate-400 mt-1">{quoteInst.data?.breakdown}</div>
        </div>
        <div className="card">
          <div className="text-sm text-slate-400">ปิดยอด (ทั้งหมด)</div>
          <div className="text-3xl font-bold text-blue-400 mt-2">
            {formatTHB(quoteFull.data?.total ?? 0)} บาท
          </div>
          <div className="text-xs text-slate-400 mt-1">{quoteFull.data?.breakdown}</div>
        </div>
      </div>

      <div className="flex justify-between items-center mb-2">
        <h2 className="text-xl font-semibold">ตารางงวด</h2>
        <button className="btn-primary" onClick={() => setShowPay((v) => !v)}>
          {showPay ? "ปิด" : "+ บันทึกรับชำระ"}
        </button>
      </div>
      {showPay && <ManualPaymentForm contractId={id} onDone={() => setShowPay(false)} />}
      <div className="card overflow-x-auto mb-6">
        <table className="table-base">
          <thead>
            <tr>
              <th>งวด</th>
              <th>วันครบกำหนด</th>
              <th>ยอดต้องจ่าย</th>
              <th>จ่ายแล้ว</th>
              <th>คงเหลือ</th>
              <th>ค่าปรับ</th>
              <th>สถานะ</th>
            </tr>
          </thead>
          <tbody>
            {installments.data?.map((inst) => {
              const m = STATUS_LABELS[inst.status] ?? { label: inst.status, color: "bg-slate-700" };
              return (
                <tr key={inst.id}>
                  <td>{inst.period}</td>
                  <td>{formatThaiDate(inst.dueDate)}</td>
                  <td className="text-right">{formatTHB(inst.dueAmount)}</td>
                  <td className="text-right">{formatTHB(inst.paidAmount)}</td>
                  <td className="text-right">{formatTHB(inst.remainingAmount)}</td>
                  <td className="text-right">{formatTHB(inst.penaltyAmount)}</td>
                  <td>
                    <span className={`badge ${m.color}`}>{m.label}</span>
                  </td>
                </tr>
              );
            })}
            {!installments.data?.length && (
              <tr>
                <td colSpan={7} className="text-center text-slate-500 py-6">
                  ไม่มีตารางงวด
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <h2 className="text-xl font-semibold mb-2">ประวัติการชำระ</h2>
      <div className="card overflow-x-auto mb-6">
        <table className="table-base">
          <thead>
            <tr>
              <th>วันที่</th>
              <th>งวด</th>
              <th>ยอด</th>
              <th>สถานะ</th>
              <th>หมายเหตุ</th>
            </tr>
          </thead>
          <tbody>
            {payments.data?.map((p) => (
              <tr key={p.id}>
                <td>{formatThaiDate(p.paymentDate)}</td>
                <td className="text-center">{p.period ?? "-"}</td>
                <td className="text-right">{formatTHB(p.amount)}</td>
                <td>{p.verificationStatus}</td>
                <td className="text-xs text-slate-400">{p.notes ?? "-"}</td>
              </tr>
            ))}
            {!payments.data?.length && (
              <tr>
                <td colSpan={5} className="text-center text-slate-500 py-6">
                  ยังไม่มีการชำระ
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <h2 className="text-xl font-semibold mb-2">Audit Log</h2>
      <div className="card overflow-x-auto">
        <table className="table-base">
          <thead>
            <tr>
              <th>เวลา</th>
              <th>action</th>
              <th>คำอธิบาย</th>
              <th>โดย</th>
            </tr>
          </thead>
          <tbody>
            {audit.data?.map((a) => (
              <tr key={a.id}>
                <td className="text-xs">{formatThaiDate(a.createdAt)}</td>
                <td className="font-mono text-xs">{a.actionType}</td>
                <td className="text-xs">{a.description ?? "-"}</td>
                <td className="text-xs text-slate-400">{a.performedBy ?? "-"}</td>
              </tr>
            ))}
            {!audit.data?.length && (
              <tr>
                <td colSpan={4} className="text-center text-slate-500 py-6">
                  ไม่มีบันทึก
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function InfoCard({ title, value }: { title: string; value: string }) {
  return (
    <div className="card">
      <div className="text-xs text-slate-400">{title}</div>
      <div className="text-lg font-semibold mt-1">{value}</div>
    </div>
  );
}

function ManualPaymentForm({
  contractId,
  onDone,
}: {
  contractId: number;
  onDone: () => void;
}) {
  const utils = trpc.useUtils();
  const mut = trpc.payment.manualRecord.useMutation({
    onSuccess: async () => {
      await Promise.all([
        utils.payment.listByContract.invalidate(contractId),
        utils.installment.byContract.invalidate(contractId),
        utils.contract.quote.invalidate({ contractId, type: "installment" }),
        utils.contract.quote.invalidate({ contractId, type: "full" }),
        utils.audit.listByContract.invalidate(contractId),
      ]);
      onDone();
    },
  });
  const [amount, setAmount] = useState(0);
  const [paymentDate, setPaymentDate] = useState(new Date().toISOString().slice(0, 10));
  const [notes, setNotes] = useState("");

  return (
    <form
      className="card mb-4 grid grid-cols-1 md:grid-cols-3 gap-3"
      onSubmit={(e) => {
        e.preventDefault();
        mut.mutate({
          contractId,
          amount,
          paymentDate: new Date(paymentDate),
          notes: notes || undefined,
        });
      }}
    >
      <div>
        <label className="label">ยอดชำระ</label>
        <input
          className="input"
          type="number"
          value={amount}
          onChange={(e) => setAmount(Number(e.target.value))}
        />
      </div>
      <div>
        <label className="label">วันที่ชำระ</label>
        <input
          className="input"
          type="date"
          value={paymentDate}
          onChange={(e) => setPaymentDate(e.target.value)}
        />
      </div>
      <div>
        <label className="label">หมายเหตุ</label>
        <input
          className="input"
          value={notes}
          onChange={(e) => setNotes(e.target.value)}
        />
      </div>
      <div className="md:col-span-3 flex gap-2 justify-end">
        <button type="button" className="btn-ghost" onClick={onDone}>
          ยกเลิก
        </button>
        <button className="btn-primary" disabled={mut.isPending}>
          {mut.isPending ? "กำลังบันทึก..." : "บันทึก"}
        </button>
      </div>
      {mut.error && <div className="md:col-span-3 text-sm text-red-400">{mut.error.message}</div>}
    </form>
  );
}
