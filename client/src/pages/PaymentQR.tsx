import { useEffect, useState } from "react";
import { Link } from "wouter";
import { trpc } from "../lib/trpc";
import { formatTHB } from "../lib/format";
import { loadQrCodeSdk } from "../lib/liff";

export function PaymentQRPage({
  contractNumber,
  type,
}: {
  contractNumber: string;
  type: "installment" | "full";
}) {
  const [qrDataUrl, setQrDataUrl] = useState<string | null>(null);
  const [renderError, setRenderError] = useState<string | null>(null);

  const gen = trpc.qrPayment.generate.useMutation();

  useEffect(() => {
    gen.mutate({ contractNumber, type });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [contractNumber, type]);

  useEffect(() => {
    if (!gen.data?.payload) return;
    (async () => {
      try {
        await loadQrCodeSdk();
        if (!window.QRCode) throw new Error("QRCode SDK failed to load");
        const url = await window.QRCode.toDataURL(gen.data!.payload, {
          width: 320,
          margin: 1,
        });
        setQrDataUrl(url);
      } catch (err) {
        setRenderError((err as Error).message);
      }
    })();
  }, [gen.data?.payload]);

  const copy = () => {
    if (!gen.data?.amount) return;
    navigator.clipboard?.writeText(String(gen.data.amount));
  };

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 p-4 max-w-md mx-auto">
      <div className="flex items-center mb-3">
        <Link href="/pay" className="text-sm text-slate-400 hover:text-slate-200">← กลับ</Link>
      </div>

      <div className="card text-center mb-3">
        <div className="text-xs text-slate-400">เลขสัญญา</div>
        <div className="font-mono text-brand-primary">{contractNumber}</div>
        <div className="text-xs text-slate-400 mt-3">
          {type === "installment" ? "ต่อดอก (ค่างวด + ค่าปรับ)" : "ปิดยอด (จบสัญญา)"}
        </div>
        <div className="text-3xl font-bold mt-1">
          {gen.data ? `${formatTHB(gen.data.amount)} ฿` : "—"}
        </div>
        {gen.data && (
          <div className="text-xs text-slate-500 mt-1">{gen.data.breakdown}</div>
        )}
        <button className="text-xs text-brand-primary mt-2" onClick={copy}>📋 คัดลอกยอด</button>
      </div>

      <div className="card mb-3">
        {gen.isPending && (
          <div className="py-12 text-center text-sm text-slate-400">กำลังสร้าง QR…</div>
        )}
        {gen.error && (
          <div className="py-12 text-center text-sm text-red-400">{gen.error.message}</div>
        )}
        {renderError && (
          <div className="py-12 text-center text-sm text-red-400">QR error: {renderError}</div>
        )}
        {qrDataUrl && (
          <div className="bg-white rounded-md p-4">
            <img src={qrDataUrl} alt="PromptPay QR" className="w-full max-w-[320px] mx-auto" />
          </div>
        )}
        {gen.data && (
          <div className="mt-3 text-center">
            <div className="inline-block bg-emerald-700 text-white text-xs px-3 py-1 rounded-full">
              PromptPay ✓ ยอดล็อค
            </div>
          </div>
        )}
      </div>

      <div className="card text-sm space-y-2">
        <div className="font-semibold">วิธีชำระ</div>
        <ol className="text-xs text-slate-300 list-decimal list-inside space-y-1">
          <li>เปิดแอปธนาคาร → สแกน QR ด้านบน</li>
          <li>ยอดเงินจะถูกล็อคให้อัตโนมัติ ({gen.data ? formatTHB(gen.data.amount) : "—"} ฿) — ห้ามแก้</li>
          <li>โอนเงินตามขั้นตอนปกติ</li>
          <li>ส่งสลิปกลับมาในแชท LINE — ระบบจะตรวจสลิปและบันทึกการชำระอัตโนมัติ</li>
        </ol>
      </div>

      <div className="text-[11px] text-center text-slate-500 mt-6">
        ระบบจะอัพเดตยอดในแอปทันทีหลังตรวจสลิปสำเร็จ
      </div>
    </div>
  );
}
