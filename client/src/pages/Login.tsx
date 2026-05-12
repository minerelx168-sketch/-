import { useState } from "react";
import { useLocation } from "wouter";
import { trpc } from "../lib/trpc";

export function LoginPage() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [, setLocation] = useLocation();
  const utils = trpc.useUtils();
  const login = trpc.auth.adminLogin.useMutation({
    onSuccess: async () => {
      await utils.auth.me.invalidate();
      setLocation("/");
    },
  });

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-950">
      <form
        className="card w-full max-w-sm"
        onSubmit={(e) => {
          e.preventDefault();
          login.mutate({ username, password });
        }}
      >
        <h1 className="text-xl font-bold text-brand-primary mb-4">CloudForCash V2</h1>
        <label className="label">ชื่อผู้ใช้</label>
        <input
          className="input mb-3"
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          autoFocus
        />
        <label className="label">รหัสผ่าน</label>
        <input
          className="input mb-4"
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
        {login.error && (
          <div className="text-sm text-red-400 mb-3">{login.error.message}</div>
        )}
        <button className="btn-primary w-full" disabled={login.isPending}>
          {login.isPending ? "กำลังเข้าสู่ระบบ..." : "เข้าสู่ระบบ"}
        </button>
      </form>
    </div>
  );
}
