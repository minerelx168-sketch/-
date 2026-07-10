import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Link } from "wouter";
import { Shield, CheckCircle, CreditCard, ArrowRight } from "lucide-react";
import { trpc } from "@/lib/trpc";

export default function Success() {
  const { isAuthenticated } = useAuth();
  const { data: balanceData } = trpc.credits.getBalance.useQuery(undefined, {
    enabled: isAuthenticated,
    refetchInterval: 3000, // Poll every 3s to catch webhook update
  });

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
        </div>
      </nav>

      {/* Success Content */}
      <div className="container py-20">
        <div className="max-w-md mx-auto text-center">
          <div className="mb-8">
            <div className="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-500/10 mb-6">
              <CheckCircle className="h-10 w-10 text-green-500" />
            </div>
            <h1 className="text-3xl font-bold mb-3">Payment Successful!</h1>
            <p className="text-muted-foreground">
              Your credits have been added to your account. Thank you for your purchase!
            </p>
          </div>

          {isAuthenticated && balanceData && (
            <Card className="bg-gradient-to-br from-primary/10 to-transparent border-primary/20 mb-8">
              <CardContent className="pt-6">
                <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground mb-2">
                  <CreditCard className="h-4 w-4" /> Updated Balance
                </div>
                <div className="text-4xl font-bold text-primary">
                  ${((balanceData.balance || 0) / 100).toFixed(2)}
                </div>
                <p className="text-sm text-muted-foreground mt-2">
                  {(balanceData.balance || 0).toLocaleString()} credits available
                </p>
              </CardContent>
            </Card>
          )}

          <div className="flex flex-col gap-3">
            <Link href="/dashboard">
              <Button className="w-full gap-2">
                Go to Dashboard <ArrowRight className="h-4 w-4" />
              </Button>
            </Link>
            <Link href="/topup">
              <Button variant="outline" className="w-full">
                Buy More Credits
              </Button>
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
