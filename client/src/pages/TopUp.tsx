import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Link } from "wouter";
import { getLoginUrl } from "@/const";
import { Shield, CreditCard, Star, ArrowLeft } from "lucide-react";
import { CREDIT_PACKAGES, LEMON_SQUEEZY_STORE_URL } from "@shared/const";
import { trpc } from "@/lib/trpc";
import { useEffect } from "react";

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
  const { data: balanceData } = trpc.credits.getBalance.useQuery(undefined, {
    enabled: isAuthenticated,
  });

  // Initialize Lemon.js on mount with robust script-load handling
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

    // Try immediately
    initLemon();

    // Also retry after script loads (in case it hasn't loaded yet)
    const interval = setInterval(() => {
      if (window.createLemonSqueezy) {
        initLemon();
        clearInterval(interval);
      }
    }, 200);

    return () => clearInterval(interval);
  }, []);

  const handleCheckout = (variantId: string) => {
    if (!isAuthenticated) {
      window.location.href = getLoginUrl("/topup");
      return;
    }

    // Build Lemon Squeezy checkout URL with overlay
    const successUrl = `${window.location.origin}/success`;
    const params = new URLSearchParams({
      "checkout[custom][user_id]": String(user?.id || ""),
      "checkout[email]": user?.email || "",
      "checkout[success_url]": successUrl,
      embed: "1",
      media: "0",
      dark: "1",
    });

    const checkoutUrl = `${LEMON_SQUEEZY_STORE_URL}/buy/${variantId}?${params.toString()}`;

    // Open Lemon Squeezy overlay
    if (window.LemonSqueezy) {
      window.LemonSqueezy.Url.Open(checkoutUrl);
    } else {
      // Fallback: open in new tab
      window.open(checkoutUrl, "_blank");
    }
  };

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
                <span className="font-medium">${(balanceData.balance / 100).toFixed(2)}</span>
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
      <div className="container py-12">
        <Link href="/">
          <Button variant="ghost" size="sm" className="gap-2 mb-6">
            <ArrowLeft className="h-4 w-4" /> Back
          </Button>
        </Link>

        <div className="text-center mb-12">
          <h1 className="text-3xl md:text-4xl font-bold mb-4">Top Up Credits</h1>
          <p className="text-muted-foreground max-w-lg mx-auto">
            Choose a credit package below. Credits are added instantly after payment.
          </p>
          {isAuthenticated && balanceData && (
            <p className="mt-4 text-sm">
              Current balance: <span className="font-semibold text-primary">${(balanceData.balance / 100).toFixed(2)}</span>
            </p>
          )}
        </div>

        {/* Credit Packages */}
        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto">
          {CREDIT_PACKAGES.map((pkg) => (
            <Card
              key={pkg.id}
              className={`relative bg-card border-border/50 transition-all hover:border-primary/50 hover:shadow-lg hover:shadow-primary/5 ${
                pkg.popular ? "border-primary ring-1 ring-primary/20" : ""
              }`}
            >
              {pkg.popular && (
                <Badge className="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary text-primary-foreground">
                  <Star className="h-3 w-3 mr-1" /> Popular
                </Badge>
              )}
              <CardHeader className="text-center pb-2">
                <CardTitle className="text-3xl font-bold">{pkg.price}</CardTitle>
                <p className="text-sm text-muted-foreground">{pkg.description}</p>
              </CardHeader>
              <CardContent className="text-center">
                <div className="text-sm text-muted-foreground mb-4">
                  {(pkg.amount / 100).toLocaleString()} credits
                </div>
                <Button
                  className="w-full"
                  variant={pkg.popular ? "default" : "outline"}
                  onClick={() => handleCheckout(pkg.variantId)}
                >
                  <CreditCard className="h-4 w-4 mr-2" />
                  Buy Now
                </Button>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Info */}
        <div className="mt-12 text-center text-sm text-muted-foreground max-w-lg mx-auto">
          <p>
            Payments are processed securely by Lemon Squeezy. Credits are added to your account 
            automatically after successful payment.
          </p>
        </div>
      </div>
    </div>
  );
}
