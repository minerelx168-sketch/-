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
  const discountsQ = trpc.discount.listByContract.useQuery(id);
  const [showPay, setShowPay] = useState(false);
  const [actionModal, setActionModal] = useState<null | OverrideKind>(null);

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

      <div className="card mb-4 flex flex-wrap gap-2 text-xs">
        <span className="text-xs text-slate-400 mr-2 self-center">Admin actions:</span>
        <button className="btn-ghost" onClick={() => setActionModal("status")}>เปลี่ยนสถานะ</button>
        <button className="btn-ghost" onClick={() => setActionModal("lock")}>
          {c.lockStatus === "locked" ? "ปลดล็อค" : "ล็อคเครื่อง"}
        </button>
        <button className="btn-ghost" onClick={() => setActionModal("discount")}>ให้ส่วนลด</button>
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
              <th></th>
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
                  <td className="text-xs flex gap-2">
                    <button
                      className="text-slate-400 hover:text-brand-primary"
                      onClick={() => setActionModal({ kind: "date", installmentId: inst.id, period: inst.period, currentDate: inst.dueDate })}
                    >
                      แก้วัน
                    </button>
                    <button
                      className="text-slate-400 hover:text-brand-primary"
                      onClick={() => setActionModal({ kind: "penalty", installmentId: inst.id, period: inst.period, currentPenalty: Number(inst.penaltyAmount) })}
                    >
                      แก้ค่าปรับ
                    </button>
                  </td>
                </tr>
              );
            })}
            {!installments.data?.length && (
              <tr>
                <td colSpan={8} className="text-center text-slate-500 py-6">
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
              <th></th>
            </tr>
          </thead>
          <tbody>
            {payments.data?.map((p) => (
              <tr key={p.id} className={p.voided ? "opacity-50 line-through" : ""}>
                <td>{formatThaiDate(p.paymentDate)}</td>
                <td className="text-center">{p.period ?? "-"}</td>
                <td className="text-right">{formatTHB(p.amount)}</td>
                <td>
                  {p.voided ? "voided" : p.verificationStatus}
                </td>
                <td className="text-xs text-slate-400">{p.notes ?? "-"}</td>
                <td className="text-xs flex gap-2">
                  {!p.voided && (
                    <>
                      <button
                        className="text-slate-400 hover:text-brand-primary"
                        onClick={() => setActionModal({ kind: "edit-amount", paymentId: p.id, currentAmount: Number(p.amount) })}
                      >
                        แก้
                      </button>
                      <button
                        className="text-slate-400 hover:text-red-400"
                        onClick={() => setActionModal({ kind: "void", paymentId: p.id, amount: Number(p.amount) })}
                      >
                        ยกเลิก
                      </button>
                    </>
                  )}
                </td>
              </tr>
            ))}
            {!payments.data?.length && (
              <tr>
                <td colSpan={6} className="text-center text-slate-500 py-6">
                  ยังไม่มีการชำระ
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <h2 className="text-xl font-semibold mb-2">ส่วนลด</h2>
      <div className="card overflow-x-auto mb-6">
        <table className="table-base">
          <thead>
            <tr>
              <th>วันที่</th>
              <th>ประเภท</th>
              <th className="text-right">ราคาเดิม</th>
              <th className="text-right">ส่วนลด</th>
              <th className="text-right">สุทธิ</th>
              <th>เหตุผล</th>
              <th>สถานะ</th>
            </tr>
          </thead>
          <tbody>
            {discountsQ.data?.map((d) => (
              <tr key={d.id} className={d.status === "revoked" ? "opacity-50" : ""}>
                <td className="text-xs">{formatThaiDate(d.createdAt)}</td>
                <td>{d.discountType}</td>
                <td className="text-right">{formatTHB(d.originalAmount)}</td>
                <td className="text-right">{formatTHB(d.discountAmount)}</td>
                <td className="text-right">{formatTHB(d.finalAmount)}</td>
                <td className="text-xs">{d.reason ?? "-"}</td>
                <td className="text-xs">{d.status}</td>
              </tr>
            ))}
            {!discountsQ.data?.length && (
              <tr>
                <td colSpan={7} className="text-center text-slate-500 py-6">ยังไม่มีส่วนลด</td>
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

      {actionModal && (
        <OverrideModal
          action={actionModal}
          contractId={id}
          onClose={() => setActionModal(null)}
        />
      )}
    </div>
  );
}

type OverrideKind =
  | "status"
  | "lock"
  | "discount"
  | { kind: "edit-amount"; paymentId: number; currentAmount: number }
  | { kind: "void"; paymentId: number; amount: number }
  | { kind: "date"; installmentId: number; period: number; currentDate: Date | string }
  | { kind: "penalty"; installmentId: number; period: number; currentPenalty: number };

function OverrideModal({
  action,
  contractId,
  onClose,
}: {
  action: OverrideKind;
  contractId: number;
  onClose: () => void;
}) {
  const utils = trpc.useUtils();
  const invalidateAll = async () => {
    await Promise.all([
      utils.contract.getById.invalidate(contractId),
      utils.installment.byContract.invalidate(contractId),
      utils.payment.listByContract.invalidate(contractId),
      utils.contract.quote.invalidate({ contractId, type: "installment" }),
      utils.contract.quote.invalidate({ contractId, type: "full" }),
      utils.audit.listByContract.invalidate(contractId),
      utils.discount.listByContract.invalidate(contractId),
    ]);
    onClose();
  };

  const statusMut = trpc.override.statusOverride.useMutation({ onSuccess: invalidateAll });
  const lockMut = trpc.override.lockOverride.useMutation({ onSuccess: invalidateAll });
  const editMut = trpc.override.editAmount.useMutation({ onSuccess: invalidateAll });
  const voidMut = trpc.override.voidPayment.useMutation({ onSuccess: invalidateAll });
  const dateMut = trpc.override.dateAdjustment.useMutation({ onSuccess: invalidateAll });
  const penaltyMut = trpc.override.penaltyAdjustment.useMutation({ onSuccess: invalidateAll });
  const discountMut = trpc.discount.create.useMutation({ onSuccess: invalidateAll });

  const [remark, setRemark] = useState("");
  const [v1, setV1] = useState<string>("");
  const [v2, setV2] = useState<string>("");

  const pending =
    statusMut.isPending || lockMut.isPending || editMut.isPending || voidMut.isPending ||
    dateMut.isPending || penaltyMut.isPending || discountMut.isPending;
  const error =
    statusMut.error || lockMut.error || editMut.error || voidMut.error ||
    dateMut.error || penaltyMut.error || discountMut.error;

  let title = "";
  let body: React.ReactNode = null;
  let submit: () => void = () => {};

  if (action === "status") {
    title = "เปลี่ยนสถานะสัญญา";
    body = (
      <select className="input" value={v1} onChange={(e) => setV1(e.target.value)}>
        <option value="">-- เลือก --</option>
        <option value="active">ปกติ</option>
        <option value="overdue">ค้างชำระ</option>
        <option value="paid">ปิดสัญญา</option>
        <option value="defaulted">หนี้สูญ</option>
        <option value="closed">ปิดแล้ว</option>
      </select>
    );
    submit = () =>
      v1 && statusMut.mutate({ contractId, newStatus: v1 as "active", remark });
  } else if (action === "lock") {
    title = "ล็อค / ปลดล็อคเครื่อง";
    body = (
      <select className="input" value={v1} onChange={(e) => setV1(e.target.value)}>
        <option value="">-- เลือก --</option>
        <option value="lock">ล็อคเครื่อง</option>
        <option value="unlock">ปลดล็อค</option>
      </select>
    );
    submit = () => v1 && lockMut.mutate({ contractId, locked: v1 === "lock", remark });
  } else if (action === "discount") {
    title = "ให้ส่วนลด";
    body = (
      <>
        <label className="label">ประเภทส่วนลด</label>
        <select className="input mb-2" value={v1} onChange={(e) => setV1(e.target.value)}>
          <option value="">-- เลือก --</option>
          <option value="penalty">ลดค่าปรับ</option>
          <option value="closing">ลดยอดปิด</option>
          <option value="installment">ลดค่างวด</option>
          <option value="other">อื่นๆ</option>
        </select>
        <div className="grid grid-cols-2 gap-2">
          <div>
            <label className="label">ราคาเดิม</label>
            <input className="input" type="number" value={v2.split("|")[0] ?? ""} onChange={(e) => setV2(`${e.target.value}|${v2.split("|")[1] ?? ""}`)} />
          </div>
          <div>
            <label className="label">ส่วนลด</label>
            <input className="input" type="number" value={v2.split("|")[1] ?? ""} onChange={(e) => setV2(`${v2.split("|")[0] ?? ""}|${e.target.value}`)} />
          </div>
        </div>
      </>
    );
    submit = () => {
      const [origStr, discStr] = v2.split("|");
      const orig = Number(origStr);
      const disc = Number(discStr);
      if (!v1 || !Number.isFinite(orig) || !Number.isFinite(disc)) return;
      discountMut.mutate({
        contractId,
        discountType: v1 as "penalty",
        originalAmount: orig,
        discountAmount: disc,
        reason: remark,
      });
    };
  } else if (typeof action !== "string" && action.kind === "edit-amount") {
    title = "แก้ไขยอดชำระ";
    body = (
      <>
        <div className="text-xs text-slate-400 mb-1">ยอดเดิม: {action.currentAmount.toLocaleString()} บาท</div>
        <input className="input" type="number" value={v1} onChange={(e) => setV1(e.target.value)} placeholder="ยอดใหม่" />
      </>
    );
    submit = () => {
      const n = Number(v1);
      if (Number.isFinite(n)) editMut.mutate({ paymentId: action.paymentId, newAmount: n, remark });
    };
  } else if (typeof action !== "string" && action.kind === "void") {
    title = "ยกเลิกรายการชำระ";
    body = <div className="text-sm text-slate-300">ยกเลิกการชำระ {action.amount.toLocaleString()} บาท?</div>;
    submit = () => voidMut.mutate({ paymentId: action.paymentId, reason: remark });
  } else if (typeof action !== "string" && action.kind === "date") {
    title = `แก้วันครบกำหนดงวด ${action.period}`;
    body = <input className="input" type="date" value={v1} onChange={(e) => setV1(e.target.value)} />;
    submit = () =>
      v1 && dateMut.mutate({ installmentId: action.installmentId, newDueDate: new Date(v1), remark });
  } else if (typeof action !== "string" && action.kind === "penalty") {
    title = `แก้ค่าปรับงวด ${action.period}`;
    body = (
      <>
        <div className="text-xs text-slate-400 mb-1">ค่าปรับเดิม: {action.currentPenalty.toLocaleString()} บาท</div>
        <input className="input" type="number" value={v1} onChange={(e) => setV1(e.target.value)} placeholder="ค่าปรับใหม่" />
      </>
    );
    submit = () => {
      const n = Number(v1);
      if (Number.isFinite(n)) penaltyMut.mutate({ installmentId: action.installmentId, newPenalty: n, remark });
    };
  }

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onClick={onClose}>
      <div className="card w-full max-w-md" onClick={(e) => e.stopPropagation()}>
        <h3 className="text-lg font-bold mb-3">{title}</h3>
        <div className="mb-3">{body}</div>
        <label className="label">เหตุผล / หมายเหตุ (จำเป็น)</label>
        <textarea
          className="input mb-3"
          rows={2}
          value={remark}
          onChange={(e) => setRemark(e.target.value)}
        />
        {error && <div className="text-sm text-red-400 mb-2">{error.message}</div>}
        <div className="flex justify-end gap-2">
          <button className="btn-ghost" onClick={onClose}>ยกเลิก</button>
          <button className="btn-primary" onClick={submit} disabled={pending || !remark.trim()}>
            {pending ? "กำลังบันทึก..." : "ยืนยัน"}
          </button>
        </div>
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
