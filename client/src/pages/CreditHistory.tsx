import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { Link } from "wouter";
import { getLoginUrl } from "@/const";
import { Shield, CreditCard, History, ArrowLeft, ChevronLeft, ChevronRight } from "lucide-react";
import { trpc } from "@/lib/trpc";
import { useEffect, useState, useMemo } from "react";

const FILTER_OPTIONS = [
  { label: "All", value: "ALL" },
  { label: "Top-Up", value: "TOPUP" },
  { label: "Usage", value: "USAGE" },
  { label: "Refund", value: "REFUND" },
  { label: "Bonus", value: "BONUS" },
  { label: "Adjustment", value: "ADJUSTMENT" },
] as const;

const PAGE_SIZE = 15;

export default function CreditHistory() {
  const { user, isAuthenticated, loading } = useAuth();
  const [activeFilter, setActiveFilter] = useState<string>("ALL");
  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    if (!loading && !isAuthenticated) {
      window.location.href = getLoginUrl("/credit-history");
    }
  }, [loading, isAuthenticated]);

  const { data: txData, isLoading: txLoading } = trpc.credits.getTransactions.useQuery(
    undefined,
    { enabled: isAuthenticated }
  );

  const { data: balanceData } = trpc.credits.getBalance.useQuery(
    undefined,
    { enabled: isAuthenticated }
  );

  // Filter transactions
  const filteredTransactions = useMemo(() => {
    if (!txData?.transactions) return [];
    if (activeFilter === "ALL") return txData.transactions;
    return txData.transactions.filter((tx) => tx.type === activeFilter);
  }, [txData, activeFilter]);

  // Pagination
  const totalPages = Math.max(1, Math.ceil(filteredTransactions.length / PAGE_SIZE));
  const paginatedTransactions = useMemo(() => {
    const start = (currentPage - 1) * PAGE_SIZE;
    return filteredTransactions.slice(start, start + PAGE_SIZE);
  }, [filteredTransactions, currentPage]);

  // Reset page when filter changes
  useEffect(() => {
    setCurrentPage(1);
  }, [activeFilter]);

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
            {balanceData && (
              <div className="flex items-center gap-2 text-sm">
                <CreditCard className="h-4 w-4 text-primary" />
                <span className="font-medium">${balanceData.balance}</span>
              </div>
            )}
            <Link href="/dashboard">
              <Button variant="ghost" size="sm">Dashboard</Button>
            </Link>
          </div>
        </div>
      </nav>

      {/* Content */}
      <div className="container py-8 max-w-5xl mx-auto">
        <Link href="/dashboard">
          <Button variant="ghost" size="sm" className="gap-2 mb-6">
            <ArrowLeft className="h-4 w-4" /> Back to Dashboard
          </Button>
        </Link>

        <div className="mb-8">
          <h1 className="text-2xl font-bold mb-1 flex items-center gap-2">
            <History className="h-6 w-6" /> Credit History
          </h1>
          <p className="text-muted-foreground">
            View all credit transactions with filters.
            {filteredTransactions.length > 0 && (
              <span className="ml-2 text-xs">({filteredTransactions.length} transactions)</span>
            )}
          </p>
        </div>

        {/* Filter Pills */}
        <div className="flex flex-wrap gap-2 mb-6">
          {FILTER_OPTIONS.map((option) => (
            <button
              key={option.value}
              onClick={() => setActiveFilter(option.value)}
              className={`px-4 py-1.5 rounded-full text-sm font-medium transition-all ${
                activeFilter === option.value
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "bg-muted text-muted-foreground hover:bg-muted/80"
              }`}
            >
              {option.label}
            </button>
          ))}
        </div>

        {/* Transactions Table */}
        <Card className="bg-card border-border/50">
          <CardContent className="pt-6">
            {txLoading ? (
              <div className="space-y-3">
                {[1, 2, 3, 4, 5].map((i) => (
                  <Skeleton key={i} className="h-12 w-full" />
                ))}
              </div>
            ) : paginatedTransactions.length === 0 ? (
              <div className="text-center py-12 text-muted-foreground">
                <History className="h-12 w-12 mx-auto mb-4 opacity-30" />
                <p>No transactions found.</p>
                {activeFilter !== "ALL" && (
                  <p className="text-sm mt-1">Try changing the filter.</p>
                )}
              </div>
            ) : (
              <>
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead className="w-[140px]">Date</TableHead>
                      <TableHead>Description</TableHead>
                      <TableHead className="w-[100px]">Type</TableHead>
                      <TableHead className="w-[100px] text-right">Amount</TableHead>
                      <TableHead className="w-[100px] text-right">Balance</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {paginatedTransactions.map((tx) => {
                      const amount = parseFloat(tx.amount);
                      const isPositive = amount >= 0;
                      return (
                        <TableRow key={tx.id}>
                          <TableCell className="text-sm text-muted-foreground">
                            {new Date(tx.createdAt).toLocaleString(undefined, {
                              dateStyle: "short",
                              timeStyle: "short",
                            })}
                          </TableCell>
                          <TableCell className="text-sm">
                            {tx.description || tx.referenceType || tx.type}
                          </TableCell>
                          <TableCell>
                            <Badge
                              variant={
                                tx.type === "TOPUP" ? "default" :
                                tx.type === "USAGE" ? "secondary" :
                                tx.type === "REFUND" ? "outline" :
                                "secondary"
                              }
                              className="text-xs"
                            >
                              {tx.type}
                            </Badge>
                          </TableCell>
                          <TableCell className={`text-right font-medium ${isPositive ? "text-green-500" : "text-red-500"}`}>
                            {isPositive ? "+" : ""}${amount.toFixed(2)}
                          </TableCell>
                          <TableCell className="text-right text-sm text-muted-foreground">
                            ${tx.balanceAfter}
                          </TableCell>
                        </TableRow>
                      );
                    })}
                  </TableBody>
                </Table>

                {/* Pagination */}
                {totalPages > 1 && (
                  <div className="flex items-center justify-between mt-4 pt-4 border-t border-border/50">
                    <p className="text-sm text-muted-foreground">
                      Page {currentPage} of {totalPages}
                    </p>
                    <div className="flex items-center gap-2">
                      <Button
                        variant="outline"
                        size="sm"
                        disabled={currentPage === 1}
                        onClick={() => setCurrentPage((p) => p - 1)}
                      >
                        <ChevronLeft className="h-4 w-4" />
                      </Button>
                      <Button
                        variant="outline"
                        size="sm"
                        disabled={currentPage === totalPages}
                        onClick={() => setCurrentPage((p) => p + 1)}
                      >
                        <ChevronRight className="h-4 w-4" />
                      </Button>
                    </div>
                  </div>
                )}
              </>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
