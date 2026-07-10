import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Link } from "wouter";
import { getLoginUrl } from "@/const";
import { Shield, CreditCard, ArrowLeft, Loader2, DollarSign, Percent } from "lucide-react";
import { TOPUP_PRESETS, PAYMENT_METHODS, LEMON_SQUEEZY_STORE_URL } from "@shared/const";
import { trpc } from "@/lib/trpc";
import { useEffect, useState, useCallback } from "react";
import { nanoid } from "nanoid";
import { toast } from "sonner";

declare global {
  interface Window {
    createLemonSqueezy?: () => void;
    LemonSqueezy?: {
      Setup: (config: { eventHandler: (event: any) => void }) => void;
      Url: {
        Open: (url: string) => void;
        Close: () => void;
      };
    };
  }
}

export default function TopUp() {
  const { user, isAuthenticated } = useAuth();
  const [selectedAmount, setSelectedAmount] = useState<number | null>(null);
  const [customAmount, setCustomAmount] = useState("");
  const [isProcessing, setIsProcessing] = useState(false);

  const { data: balanceData } = trpc.credits.getBalance.useQuery(undefined, {
    enabled: isAuthenticated,
  });

  const createOrderMutation = trpc.credits.createOrder.useMutation();

  const method = PAYMENT_METHODS[0]; // card
  const feePct = method.feePct;
  const minAmount = method.minUsd;
  const maxAmount = method.maxUsd;

  // Computed values
  const activeAmount = selectedAmount ?? (customAmount ? parseFloat(customAmount) : 0);
  const feeAmount = activeAmount > 0 ? activeAmount * (feePct / 100) : 0;
  const totalCharge = activeAmount + feeAmount;
  const isValidAmount = activeAmount >= minAmount && activeAmount <= maxAmount;

  // Initialize Lemon.js on mount
  useEffect(() => {
    const initLemon = () => {
      if (window.createLemonSqueezy) {
        window.createLemonSqueezy();
      }
      if (window.LemonSqueezy) {
        window.LemonSqueezy.Setup({
          eventHandler: (event: any) => {
            if (event.event === "Checkout.Success") {
              window.location.href = "/success";
            }
          },
        });
      }
    };

    initLemon();
    const interval = setInterval(() => {
      if (window.createLemonSqueezy) {
        initLemon();
        clearInterval(interval);
      }
    }, 200);

    return () => clearInterval(interval);
  }, []);

  const handlePresetClick = (amount: number) => {
    setSelectedAmount(amount);
    setCustomAmount("");
  };

  const handleCustomAmountChange = (value: string) => {
    setCustomAmount(value);
    setSelectedAmount(null);
  };

  const handleCheckout = useCallback(async () => {
    if (!isAuthenticated) {
      window.location.href = getLoginUrl("/topup");
      return;
    }

    if (!isValidAmount) {
      toast.error(`Amount must be between $${minAmount} and $${maxAmount}`);
      return;
    }

    setIsProcessing(true);

    try {
      // Step 1: Create order on server
      const idempotencyKey = `${user?.id}_${activeAmount}_${nanoid(8)}`;
      const successUrl = `${window.location.origin}/success`;

      const result = await createOrderMutation.mutateAsync({
        amount: activeAmount,
        idempotencyKey,
        successUrl,
      });

      if (!result.checkoutUrl) {
        toast.error("Order already processed or failed");
        setIsProcessing(false);
        return;
      }

      // Step 2: Open Lemon Squeezy checkout overlay
      const overlayUrl = `${result.checkoutUrl}&embed=1&media=0&dark=1`;

      if (window.LemonSqueezy) {
        window.LemonSqueezy.Url.Open(overlayUrl);
      } else {
        // Fallback: open in new tab
        window.open(result.checkoutUrl, "_blank");
      }
    } catch (err: any) {
      toast.error(err?.message || "Failed to create order");
    } finally {
      setIsProcessing(false);
    }
  }, [isAuthenticated, isValidAmount, activeAmount, user, createOrderMutation]);

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
            {isAuthenticated && balanceData && (
              <div className="flex items-center gap-2 text-sm">
                <CreditCard className="h-4 w-4 text-primary" />
                <span className="font-medium">${balanceData.balance}</span>
              </div>
            )}
            {isAuthenticated ? (
              <Link href="/dashboard">
                <Button variant="ghost" size="sm">Dashboard</Button>
              </Link>
            ) : (
              <a href={getLoginUrl("/topup")}>
                <Button size="sm">Sign In</Button>
              </a>
            )}
          </div>
        </div>
      </nav>

      {/* Content */}
      <div className="container py-12 max-w-4xl mx-auto">
        <Link href="/">
          <Button variant="ghost" size="sm" className="gap-2 mb-6">
            <ArrowLeft className="h-4 w-4" /> Back
          </Button>
        </Link>

        <div className="text-center mb-10">
          <h1 className="text-3xl md:text-4xl font-bold mb-4">Top Up Credits</h1>
          <p className="text-muted-foreground max-w-lg mx-auto">
            Select an amount or enter a custom value. Credits are added instantly after payment.
          </p>
          {isAuthenticated && balanceData && (
            <p className="mt-4 text-sm">
              Current balance: <span className="font-semibold text-primary">${balanceData.balance}</span>
            </p>
          )}
        </div>

        {/* Preset Amounts */}
        <div className="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-8">
          {TOPUP_PRESETS.map((amount) => (
            <button
              key={amount}
              onClick={() => handlePresetClick(amount)}
              className={`relative rounded-xl border-2 p-4 text-center transition-all hover:border-primary/70 hover:shadow-md cursor-pointer ${
                selectedAmount === amount
                  ? "border-primary bg-primary/5 shadow-md ring-2 ring-primary/20"
                  : "border-border/50 bg-card"
              }`}
            >
              <div className="text-lg font-bold">${amount}</div>
              {amount === 50 && (
                <Badge variant="secondary" className="absolute -top-2 left-1/2 -translate-x-1/2 text-[10px] px-1.5">
                  Popular
                </Badge>
              )}
            </button>
          ))}
        </div>

        {/* Custom Amount */}
        <Card className="mb-8 bg-card border-border/50">
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex-1">
                <label className="text-sm font-medium text-muted-foreground mb-2 block">
                  Custom Amount (USD)
                </label>
                <div className="relative">
                  <DollarSign className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <Input
                    type="number"
                    min={minAmount}
                    max={maxAmount}
                    step="1"
                    placeholder={`${minAmount} - ${maxAmount}`}
                    value={customAmount}
                    onChange={(e) => handleCustomAmountChange(e.target.value)}
                    className="pl-9"
                  />
                </div>
              </div>
            </div>
            {customAmount && !isValidAmount && parseFloat(customAmount) > 0 && (
              <p className="text-xs text-destructive mt-2">
                Amount must be between ${minAmount} and ${maxAmount}
              </p>
            )}
          </CardContent>
        </Card>

        {/* Order Summary & Checkout */}
        {activeAmount > 0 && (
          <Card className="mb-8 bg-card border-border/50">
            <CardHeader className="pb-3">
              <CardTitle className="text-lg">Order Summary</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <div className="flex justify-between items-center">
                  <span className="text-muted-foreground">Credits</span>
                  <span className="font-medium">${activeAmount.toFixed(2)}</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-muted-foreground flex items-center gap-1">
                    <Percent className="h-3 w-3" /> Processing Fee ({feePct}%)
                  </span>
                  <span className="font-medium text-muted-foreground">${feeAmount.toFixed(2)}</span>
                </div>
                <div className="border-t border-border/50 pt-3 flex justify-between items-center">
                  <span className="font-semibold">Total Charge</span>
                  <span className="text-xl font-bold text-primary">${totalCharge.toFixed(2)}</span>
                </div>
              </div>

              <Button
                className="w-full mt-6"
                size="lg"
                disabled={!isValidAmount || isProcessing}
                onClick={handleCheckout}
              >
                {isProcessing ? (
                  <>
                    <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                    Processing...
                  </>
                ) : (
                  <>
                    <CreditCard className="h-4 w-4 mr-2" />
                    Pay ${totalCharge.toFixed(2)}
                  </>
                )}
              </Button>

              <p className="text-xs text-muted-foreground text-center mt-3">
                Secure payment via Lemon Squeezy. You will receive ${activeAmount.toFixed(2)} in credits.
              </p>
            </CardContent>
          </Card>
        )}

        {/* Info */}
        <div className="text-center text-sm text-muted-foreground max-w-lg mx-auto space-y-2">
          <p>
            Payments are processed securely by Lemon Squeezy. Credits are added to your account
            automatically after successful payment via webhook.
          </p>
          <p className="text-xs opacity-70">
            Demo mode: Only $25 and $50 variants are fully configured. Other amounts use placeholder checkout.
          </p>
        </div>
      </div>
    </div>
  );
}
