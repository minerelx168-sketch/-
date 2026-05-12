import { Link, useLocation } from "wouter";
import { trpc } from "../lib/trpc";

const NAV = [
  { href: "/", label: "ภาพรวม" },
  { href: "/products", label: "สินค้า" },
  { href: "/customers", label: "ลูกค้า" },
  { href: "/contracts", label: "สัญญา" },
  { href: "/messages", label: "ข้อความ LINE" },
  { href: "/line-connections", label: "การเชื่อมต่อ LINE" },
];

export function DashboardLayout({ children }: { children: React.ReactNode }) {
  const [location] = useLocation();
  const me = trpc.auth.me.useQuery();
  const utils = trpc.useUtils();
  const logout = trpc.auth.logout.useMutation({
    onSuccess: () => {
      utils.auth.me.invalidate();
      window.location.href = "/login";
    },
  });

  return (
    <div className="min-h-screen flex bg-slate-950">
      <aside className="w-56 shrink-0 border-r border-slate-800 bg-slate-900 p-4 flex flex-col">
        <div className="text-lg font-bold text-brand-primary mb-6">CloudForCash V2</div>
        <nav className="flex flex-col gap-1">
          {NAV.map((item) => {
            const active = location === item.href || (item.href !== "/" && location.startsWith(item.href));
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`px-3 py-2 rounded-md text-sm ${
                  active
                    ? "bg-brand-primary text-white"
                    : "text-slate-300 hover:bg-slate-800"
                }`}
              >
                {item.label}
              </Link>
            );
          })}
        </nav>
        <div className="mt-auto pt-4 border-t border-slate-800 text-xs text-slate-400">
          <div className="mb-2">ผู้ใช้: {me.data?.username ?? "-"}</div>
          <button onClick={() => logout.mutate()} className="btn-ghost w-full">
            ออกจากระบบ
          </button>
        </div>
      </aside>
      <main className="flex-1 p-6 overflow-auto">{children}</main>
    </div>
  );
}
