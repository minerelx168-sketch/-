import jwt from "jsonwebtoken";
import type { Request, Response, NextFunction } from "express";

const JWT_SECRET = process.env.JWT_SECRET || "dev-only-secret-change-me";
const COOKIE_NAME = "cfc_session";
const TOKEN_TTL = 60 * 60 * 24 * 7; // 7 days

export type SessionUser = { username: string; role: "admin" };

export function signSession(user: SessionUser): string {
  return jwt.sign(user, JWT_SECRET, { expiresIn: TOKEN_TTL });
}

export function verifySession(token: string | undefined): SessionUser | null {
  if (!token) return null;
  try {
    const decoded = jwt.verify(token, JWT_SECRET) as SessionUser & { exp?: number };
    if (!decoded?.username) return null;
    return { username: decoded.username, role: "admin" };
  } catch {
    return null;
  }
}

export function readSessionFromReq(req: Request): SessionUser | null {
  const cookieToken = (req.cookies as Record<string, string> | undefined)?.[COOKIE_NAME];
  if (cookieToken) {
    const u = verifySession(cookieToken);
    if (u) return u;
  }
  const auth = req.header("authorization");
  if (auth?.startsWith("Bearer ")) {
    return verifySession(auth.slice(7));
  }
  return null;
}

export function setSessionCookie(res: Response, token: string): void {
  res.cookie(COOKIE_NAME, token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    maxAge: TOKEN_TTL * 1000,
    path: "/",
  });
}

export function clearSessionCookie(res: Response): void {
  res.clearCookie(COOKIE_NAME, { path: "/" });
}

export function requireAdmin(req: Request, _res: Response, next: NextFunction): void {
  const user = readSessionFromReq(req);
  if (!user) {
    next(Object.assign(new Error("Unauthorized"), { status: 401 }));
    return;
  }
  (req as Request & { user: SessionUser }).user = user;
  next();
}

export function checkAdminCredentials(username: string, password: string): boolean {
  const expectedUser = process.env.ADMIN_USERNAME || "admin";
  const expectedPass = process.env.ADMIN_PASSWORD || "admin";
  return username === expectedUser && password === expectedPass;
}

export { COOKIE_NAME };
