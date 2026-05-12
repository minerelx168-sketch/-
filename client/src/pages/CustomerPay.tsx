import { useEffect, useState } from "react";
import { useLocation } from "wouter";
import { trpc } from "../lib/trpc";
import { formatTHB, formatThaiDate } from "../lib/format";
import { resolveLineUserId } from "../lib/liff";

type Stage = "loading" | "not-linked" | "ready" | "paid" | "error";

export function CustomerPayPage() {
  const [stage, setStage] = useState<Stage>("loading");
  const [lineUserId, setLineUserId] = useState<string | null>(null);
  const [displayName, setDisplayName] = useState<string | undefined>(undefined);
  const [errorMessage, setErrorMessage] = useState<string | undefined>(undefined);

  useEffect(() => {
    resolveLineUserId().then((r) => {
      if (!r.userId) {
        setStage("error");
        setErrorMessage(r.reason ?? "ไม่สามารถยืนยันตัวตนผ่าน LINE ได้");
        return;
      }
      setLineUserId(r.userId);
      setDisplayName(r.displayName);
    });
  }, []);

  const lookup = trpc.customerPay.lookupByLine.useQuery(
    { lineUserId: lineUserId ?? "" },
    { enabled: !!lineUserId },
  );

  useEffect(() => {
    if (!lookup.data) return;
    if (!lookup.data.contract) {
      setStage("not-linked");
    } else if (lookup.data.contract.status === "paid" || lookup.data.contract.status === "closed") {
      setStage("paid");
    } else {
      setStage("ready");
    }
  }, [lookup.data]);

  if (stage === "loading" || lookup.isLoading) {
    return <Shell><Spinner /></Shell>;
  }

  if (stage === "error") {
    return (
      <Shell>
        <ErrorState
          title="ไม่สามารถยืนยันตัวตนได้"
          detail={errorMessage}
          hint="กรุณาเปิดลิงก์นี้ผ่านแอป LINE หรือเปิด LIFF ใหม่อีกครั้ง"
        />
      </Shell>
    );
  }

  if (stage === "not-linked") {
    return (
      <Shell>
        <div className="card">
          <div className="text-center py-6">
            <div className="text-5xl mb-3">🔗</div>
            <h1 className="text-xl font-bold mb-2">ยังไม่ได้ผูกสัญญา</h1>
            <p className="text-sm text-slate-300 mb-4">
              สวัสดี{displayName ? ` คุณ${displayName}` : ""} 👋
              <br />
              กรุณาส่งเลขสัญญา (เช่น <span className="font-mono text-brand-primary">CF-26041500003</span>) ในแชท LINE
              เพื่อผูกสัญญากับบัญชี LINE ของคุณ
            </p>
            <p className="text-xs text-slate-500">
              หากไม่ทราบเลขสัญญา กรุณาติดต่อร้านค้า
            </p>
          </div>
        </div>
      </Shell>
    );
  }

  if (stage === "paid") {
    return (
      <Shell>
        <div className="card text-center py-8">
          <div className="text-5xl mb-3">🎉</div>
          <h1 className="text-xl font-bold mb-2">ชำระครบทุกงวดแล้ว</h1>
          <p className="text-sm text-slate-300">
            สัญญา <span className="font-mono">{lookup.data?.contract?.contractNumber}</span> ปิดเรียบร้อย
          </p>
        </div>
      </Shell>
    );
  }

  // ready
  const data = lookup.data!;
  const contract = data.contract!;
  const inst = data.currentInstallment;
  const q = data.quotes!;

  return (
    <Shell>
      <div className="card mb-3">
        <div className="text-xs text-slate-400 mb-1">สวัสดี{displayName ? ` คุณ${displayName}` : ""}</div>
        <div className="text-sm">{data.customer?.fullName}</div>
        <div className="text-xs text-slate-400 mt-2">เลขสัญญา</div>
        <div className="font-mono text-brand-primary text-lg">{contract.contractNumber}</div>
      </div>

      {inst && (
        <div className="card mb-3">
          <div className="text-xs text-slate-400">งวดปัจจุบัน</div>
          <div className="font-semibold text-lg">งวดที่ {inst.period} / {contract.totalPeriods}</div>
          <div className="text-xs text-slate-400 mt-2">ครบกำหนด {formatThaiDate(inst.dueDate)}</div>
          {q.installment.overdueDays > 0 && (
            <div className="text-xs text-red-400 mt-1">เกินกำหนด {q.installment.overdueDays} วัน</div>
          )}
        </div>
      )}

      <div className="grid grid-cols-1 gap-3 mb-3">
        <QuoteCard
          label="ต่อดอก (ค่างวด + ค่าปรับ)"
          amount={q.installment.total}
          tone="emerald"
          contractNumber={contract.contractNumber}
          type="installment"
          breakdown={q.installment.breakdown}
        />
        <QuoteCard
          label="ปิดยอด (จบสัญญา)"
          amount={q.full.total}
          tone="blue"
          contractNumber={contract.contractNumber}
          type="full"
          breakdown={q.full.breakdown}
        />
      </div>

      <div className="text-xs text-center text-slate-500 mt-6">
        เมื่อชำระแล้ว ส่งสลิปการโอนกลับมาในแชท LINE ระบบจะตรวจอัตโนมัติ
      </div>
    </Shell>
  );
}

function Shell({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 p-4 max-w-md mx-auto">
      <div className="text-center py-3 mb-3">
        <div className="text-brand-primary font-bold text-lg">CloudForCash</div>
        <div className="text-xs text-slate-500">ตรวจยอด · ชำระเงิน · ผ่านระบบ LINE</div>
      </div>
      {children}
    </div>
  );
}

function Spinner() {
  return (
    <div className="card text-center py-12">
      <div className="text-sm text-slate-400">กำลังโหลด…</div>
    </div>
  );
}

function ErrorState({ title, detail, hint }: { title: string; detail?: string; hint?: string }) {
  return (
    <div className="card text-center py-8">
      <div className="text-5xl mb-3">⚠️</div>
      <h1 className="text-xl font-bold mb-2">{title}</h1>
      {detail && <p className="text-sm text-red-400 mb-2 break-all">{detail}</p>}
      {hint && <p className="text-xs text-slate-400">{hint}</p>}
    </div>
  );
}

function QuoteCard({
  label,
  amount,
  tone,
  contractNumber,
  type,
  breakdown,
}: {
  label: string;
  amount: number;
  tone: "emerald" | "blue";
  contractNumber: string;
  type: "installment" | "full";
  breakdown: string;
}) {
  const [, setLocation] = useLocation();
  const color = tone === "emerald" ? "text-emerald-400" : "text-blue-400";
  return (
    <div className="card">
      <div className="text-xs text-slate-400">{label}</div>
      <div className={`text-3xl font-bold mt-1 ${color}`}>{formatTHB(amount)} ฿</div>
      <div className="text-[11px] text-slate-500 mt-1">{breakdown}</div>
      <button
        className={`btn-primary w-full mt-3 ${tone === "emerald" ? "bg-emerald-600 hover:bg-emerald-500" : ""}`}
        onClick={() => setLocation(`/pay/${encodeURIComponent(contractNumber)}/${type}`)}
      >
        ชำระเงิน ({formatTHB(amount)} ฿)
      </button>
    </div>
  );
}
