import "dotenv/config";
import mysql from "mysql2/promise";
import { drizzle } from "drizzle-orm/mysql2";
import { migrate } from "drizzle-orm/mysql2/migrator";

async function main() {
  const url = process.env.DATABASE_URL;
  if (!url) throw new Error("DATABASE_URL is not set");
  const pool = mysql.createPool({ uri: url, multipleStatements: true });
  const db = drizzle(pool);
  await migrate(db, { migrationsFolder: "./drizzle/migrations" });
  // eslint-disable-next-line no-console
  console.log("[migrate] complete");
  await pool.end();
}

main().catch((err) => {
  // eslint-disable-next-line no-console
  console.error(err);
  process.exit(1);
});
