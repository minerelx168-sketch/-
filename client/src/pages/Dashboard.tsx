import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { Link, useLocation } from "wouter";
import { getLoginUrl } from "@/const";
import { Shield, CreditCard, Plus, History, LogOut } from "lucide-react";
import { trpc } from "@/lib/trpc";
import { useEffect } from "react";

export default function Dashboard() {
  const { user, isAuthenticated, loading, logout } = useAuth();
  const [, setLocation] = useLocation();

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!loading && !isAuthenticated) {
      window.location.href = getLoginUrl("/dashboard");
    }
  }, [loading, isAuthenticated]);

  const { data: balanceData, isLoading: balanceLoading } = trpc.credits.getBalance.useQuery(
    undefined,
    { enabled: isAuthenticated }
  );

  const { data: txData, isLoading: txLoading } = trpc.credits.getTransactions.useQuery(
    undefined,
    { enabled: isAuthenticated }
  );

  if (loading || !isAuthenticated) {
    return (
      <div className="min-h-screen bg-background flex items-center justify-center">
        <div className="text-center">
          <Shield className="h-12 w-12 text-primary mx-auto mb-4 animate-pulse" />
          <p className="text-muted-foreground">Loading...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Navigation */}
      <nav className="border-b border-border/50 backdrop-blur-sm sticky top-0 z-50 bg-background/80">
        <div className="container flex items-center justify-between h-16">
          <Link href="/">
            <div className="flex items-center gap-2 cursor-pointer">
              <Shield className="h-7 w-7 text-primary" />
              <span className="text-xl font-bold tracking-tight">imeihub</span>
            </div>
          </Link>
          <div className="flex items-center gap-4">
            <Link href="/topup">
              <Button size="sm" className="gap-2">
                <Plus className="h-4 w-4" /> Top Up
              </Button>
            </Link>
            <Button variant="ghost" size="sm" onClick={() => logout()} className="gap-2">
              <LogOut className="h-4 w-4" /> Logout
            </Button>
          </div>
        </div>
      </nav>

      {/* Content */}
      <div className="container py-8">
        <div className="mb-8">
          <h1 className="text-2xl font-bold mb-1">Welcome back{user?.name ? `, ${user.name}` : ""}</h1>
          <p className="text-muted-foreground">Manage your credits and view transaction history.</p>
        </div>

        {/* Balance Card */}
        <div className="grid md:grid-cols-2 gap-6 mb-8">
          <Card className="bg-gradient-to-br from-primary/10 to-transparent border-primary/20">
            <CardHeader className="pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground flex items-center gap-2">
                <CreditCard className="h-4 w-4" /> Credit Balance
              </CardTitle>
            </CardHeader>
            <CardContent>
              {balanceLoading ? (
                <Skeleton className="h-10 w-32" />
              ) : (
                <div className="text-4xl font-bold text-primary">
                  ${((balanceData?.balance || 0) / 100).toFixed(2)}
                </div>
              )}
              <p className="text-sm text-muted-foreground mt-2">
                {(balanceData?.balance || 0).toLocaleString()} credits available
              </p>
            </CardContent>
          </Card>

          <Card className="bg-card border-border/50">
            <CardHeader className="pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground flex items-center gap-2">
                <History className="h-4 w-4" /> Quick Actions
              </CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-3">
              <Link href="/topup">
                <Button className="w-full gap-2">
                  <Plus className="h-4 w-4" /> Add Credits
                </Button>
              </Link>
              <p className="text-xs text-muted-foreground text-center">
                Credits are added instantly after payment
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Transaction History */}
        <Card className="bg-card border-border/50">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <History className="h-5 w-5" /> Transaction History
            </CardTitle>
          </CardHeader>
          <CardContent>
            {txLoading ? (
              <div className="space-y-3">
                {[1, 2, 3].map((i) => (
                  <Skeleton key={i} className="h-12 w-full" />
                ))}
              </div>
            ) : !txData?.transactions?.length ? (
              <div className="text-center py-12 text-muted-foreground">
                <History className="h-12 w-12 mx-auto mb-4 opacity-30" />
                <p>No transactions yet.</p>
                <p className="text-sm mt-1">Purchase credits to get started.</p>
              </div>
            ) : (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Date</TableHead>
                    <TableHead>Description</TableHead>
                    <TableHead>Type</TableHead>
                    <TableHead className="text-right">Amount</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {txData.transactions.map((tx) => (
                    <TableRow key={tx.id}>
                      <TableCell className="text-sm text-muted-foreground">
                        {new Date(tx.createdAt).toLocaleDateString()}
                      </TableCell>
                      <TableCell className="text-sm">
                        {tx.description || "Credit top-up"}
                      </TableCell>
                      <TableCell>
                        <Badge variant={tx.type === "topup" ? "default" : "secondary"}>
                          {tx.type}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-right font-medium text-green-500">
                        +${(tx.amount / 100).toFixed(2)}
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
