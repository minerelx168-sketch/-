import { eq } from "drizzle-orm";
import { getDb, schema } from "./db.js";

export type BindResult =
  | { ok: true; customerId: number; contractId: number; rebound: boolean }
  | { ok: false; reason: "contract_not_found" | "no_customer" };

/**
 * Bind a LINE userId to the customer of a given contract.
 *
 * Rules (spec §4.4):
 *   1. Find contract by contractNumber. If not found → return contract_not_found.
 *   2. If the contract's customer already has a *different* lineUserId, that's fine
 *      (we just overwrite — same person rebinding).
 *   3. If *another* customer already owns this lineUserId, unbind them first
 *      (clear lineUserId + set botActive=0 on the old customer).
 *   4. Set customer.lineUserId = userId, customer.lineDisplayName = displayName,
 *      customer.botActive = 1.
 */
export async function bindLineUserToContract(args: {
  contractNumber: string;
  lineUserId: string;
  displayName?: string;
}): Promise<BindResult> {
  const db = getDb();
  const [contract] = await db
    .select()
    .from(schema.contracts)
    .where(eq(schema.contracts.contractNumber, args.contractNumber))
    .limit(1);
  if (!contract) return { ok: false, reason: "contract_not_found" };

  const [customer] = await db
    .select()
    .from(schema.customers)
    .where(eq(schema.customers.id, contract.customerId))
    .limit(1);
  if (!customer) return { ok: false, reason: "no_customer" };

  // Step 1: detach any *other* customer owning this lineUserId.
  const owners = await db
    .select()
    .from(schema.customers)
    .where(eq(schema.customers.lineUserId, args.lineUserId));
  let rebound = false;
  for (const other of owners) {
    if (other.id !== customer.id) {
      await db
        .update(schema.customers)
        .set({ lineUserId: null, botActive: 0 })
        .where(eq(schema.customers.id, other.id));
      await db.insert(schema.auditLogs).values({
        customerId: other.id,
        actionType: "other",
        description: `unbind LINE userId (rebound to customerId=${customer.id})`,
        performedBy: "line-bot",
      });
      rebound = true;
    }
  }

  // Step 2: set new binding.
  await db
    .update(schema.customers)
    .set({
      lineUserId: args.lineUserId,
      lineDisplayName: args.displayName ?? customer.lineDisplayName ?? null,
      botActive: 1,
    })
    .where(eq(schema.customers.id, customer.id));

  await db.insert(schema.auditLogs).values({
    contractId: contract.id,
    customerId: customer.id,
    contractNumber: contract.contractNumber,
    actionType: "other",
    description: `bind LINE userId to contract ${contract.contractNumber}`,
    performedBy: "line-bot",
  });

  return { ok: true, customerId: customer.id, contractId: contract.id, rebound };
}
