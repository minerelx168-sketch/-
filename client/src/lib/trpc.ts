import { createTRPCReact, httpBatchLink } from "@trpc/react-query";
import superjson from "superjson";
import type { AppRouter } from "../../../server/routers";

export const trpc = createTRPCReact<AppRouter>();

export function makeTrpcClient() {
  return trpc.createClient({
    links: [
      httpBatchLink({
        url: "/api/trpc",
        transformer: superjson,
        fetch(url, options) {
          return fetch(url, { ...options, credentials: "include" });
        },
      }),
    ],
  });
}
