import { trpc } from "../lib/trpc";
import { formatTHB } from "../lib/format";

export function AnalyticsPage() {
  const stats = trpc.analytics.paymentStats.useQuery();
  const daily = trpc.analytics.daily.useQuery({ days: 30 });
  const breakdown = trpc.analytics.statusBreakdown.useQuery();

  const max = Math.max(1, ...(daily.data?.map((d) => d.total) ?? [1]));

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">Analytics</h1>

      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <KpiCard label="ยอดเก็บได้รวม" value={`${formatTHB(stats.data?.totalCollected ?? 0)} ฿`} color="text-emerald-400" />
        <KpiCard
          label="Collection rate"
          value={`${Math.round((stats.data?.collectionRate ?? 0) * 100)}%`}
          color="text-brand-primary"
        />
        <KpiCard label="ตรวจสลิปแล้ว" value={String(stats.data?.verifiedCount ?? 0)} color="text-slate-100" />
        <KpiCard label="ยกเลิก / รอตรวจ" value={`${stats.data?.voidedCount ?? 0} / ${stats.data?.pendingCount ?? 0}`} color="text-yellow-400" />
      </div>

      <div className="card mb-4">
        <div className="text-sm font-semibold mb-3">ยอดชำระรายวัน (30 วันล่าสุด)</div>
        <div className="flex items-end h-48 gap-1">
          {daily.data?.map((d) => (
            <div
              key={d.date}
              className="flex-1 bg-brand-primary/80 hover:bg-brand-primary rounded-t cursor-pointer relative group"
              style={{ height: `${(d.total / max) * 100}%`, minHeight: 2 }}
              title={`${d.date}: ${formatTHB(d.total)} ฿ (${d.count} รายการ)`}
            >
              <div className="hidden group-hover:block absolute -top-12 left-1/2 -translate-x-1/2 bg-slate-900 border border-slate-700 rounded px-2 py-1 text-xs whitespace-nowrap z-10">
                {d.date}<br />
                <span className="font-semibold">{formatTHB(d.total)} ฿</span>
              </div>
            </div>
          ))}
          {!daily.data?.length && (
            <div className="text-sm text-slate-500 m-auto">ยังไม่มีข้อมูล</div>
          )}
        </div>
      </div>

      <div className="card">
        <div className="text-sm font-semibold mb-3">สถานะสัญญา</div>
        <div className="grid grid-cols-5 gap-2 text-sm">
          {breakdown.data &&
            Object.entries(breakdown.data).map(([k, v]) => (
              <div key={k} className="text-center">
                <div className="text-xs text-slate-400">{k}</div>
                <div className="text-2xl font-bold">{v}</div>
              </div>
            ))}
        </div>
      </div>
    </div>
  );
}

function KpiCard({ label, value, color }: { label: string; value: string; color: string }) {
  return (
    <div className="card">
      <div className="text-xs text-slate-400">{label}</div>
      <div className={`text-2xl font-bold mt-1 ${color}`}>{value}</div>
    </div>
  );
}
