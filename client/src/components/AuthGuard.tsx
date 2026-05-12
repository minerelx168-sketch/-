import { useLocation } from "wouter";
import { useEffect } from "react";
import { trpc } from "../lib/trpc";

export function AuthGuard({ children }: { children: React.ReactNode }) {
  const me = trpc.auth.me.useQuery();
  const [, setLocation] = useLocation();

  useEffect(() => {
    if (!me.isLoading && !me.data) {
      setLocation("/login");
    }
  }, [me.isLoading, me.data, setLocation]);

  if (me.isLoading) {
    return <div className="p-8 text-slate-400">กำลังโหลด...</div>;
  }
  if (!me.data) return null;
  return <>{children}</>;
}
