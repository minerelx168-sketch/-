/**
 * Time-bounded "have I seen this id?" set.
 * Used to suppress duplicate LINE webhook deliveries (LINE retries on non-200).
 */
export class TtlSet {
  private readonly map = new Map<string, number>();
  constructor(private readonly ttlMs: number = 5 * 60 * 1000) {}

  has(id: string, now: number = Date.now()): boolean {
    const expiry = this.map.get(id);
    if (expiry === undefined) return false;
    if (expiry <= now) {
      this.map.delete(id);
      return false;
    }
    return true;
  }

  add(id: string, now: number = Date.now()): void {
    this.map.set(id, now + this.ttlMs);
    if (this.map.size > 5000) this.sweep(now);
  }

  /** True if id was already present; otherwise adds it and returns false. */
  seen(id: string, now: number = Date.now()): boolean {
    if (this.has(id, now)) return true;
    this.add(id, now);
    return false;
  }

  sweep(now: number = Date.now()): void {
    for (const [id, expiry] of this.map) {
      if (expiry <= now) this.map.delete(id);
    }
  }

  get size(): number {
    return this.map.size;
  }
}
