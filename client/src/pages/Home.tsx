import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Link } from "wouter";
import { getLoginUrl } from "@/const";
import { Shield, Smartphone, Zap, CreditCard, CheckCircle, ArrowRight } from "lucide-react";

export default function Home() {
  const { user, isAuthenticated } = useAuth();

  return (
    <div className="min-h-screen bg-background">
      {/* Navigation */}
      <nav className="border-b border-border/50 backdrop-blur-sm sticky top-0 z-50 bg-background/80">
        <div className="container flex items-center justify-between h-16">
          <div className="flex items-center gap-2">
            <Shield className="h-7 w-7 text-primary" />
            <span className="text-xl font-bold tracking-tight">imeihub</span>
          </div>
          <div className="flex items-center gap-4">
            {isAuthenticated ? (
              <>
                <Link href="/dashboard">
                  <Button variant="ghost" size="sm">Dashboard</Button>
                </Link>
                <Link href="/topup">
                  <Button size="sm">Top Up</Button>
                </Link>
              </>
            ) : (
              <a href={getLoginUrl()}>
                <Button size="sm">Sign In</Button>
              </a>
            )}
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-transparent" />
        <div className="container relative py-24 md:py-32">
          <div className="max-w-3xl">
            <div className="inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5 text-sm text-primary mb-6">
              <Zap className="h-3.5 w-3.5" />
              Instant IMEI Verification
            </div>
            <h1 className="text-4xl md:text-6xl font-extrabold tracking-tight leading-tight mb-6">
              Check Any Device.
              <br />
              <span className="text-primary">Know Everything.</span>
            </h1>
            <p className="text-lg md:text-xl text-muted-foreground max-w-2xl mb-8">
              Professional IMEI check and device analytics platform. Verify carrier lock status, 
              warranty info, blacklist status, and more — all in seconds.
            </p>
            <div className="flex flex-wrap gap-4">
              <Link href="/topup">
                <Button size="lg" className="gap-2">
                  Get Started <ArrowRight className="h-4 w-4" />
                </Button>
              </Link>
              {!isAuthenticated && (
                <a href={getLoginUrl()}>
                  <Button size="lg" variant="outline">Sign In</Button>
                </a>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Features */}
      <section className="py-20 border-t border-border/50">
        <div className="container">
          <h2 className="text-2xl md:text-3xl font-bold text-center mb-12">
            Why Choose imeihub?
          </h2>
          <div className="grid md:grid-cols-3 gap-6">
            <Card className="bg-card border-border/50">
              <CardContent className="pt-6">
                <Smartphone className="h-10 w-10 text-primary mb-4" />
                <h3 className="text-lg font-semibold mb-2">Device Analytics</h3>
                <p className="text-muted-foreground">
                  Comprehensive device information including model, storage, color, 
                  and manufacturing details.
                </p>
              </CardContent>
            </Card>
            <Card className="bg-card border-border/50">
              <CardContent className="pt-6">
                <Shield className="h-10 w-10 text-primary mb-4" />
                <h3 className="text-lg font-semibold mb-2">Blacklist Check</h3>
                <p className="text-muted-foreground">
                  Instantly verify if a device is reported lost, stolen, or blacklisted 
                  by any carrier worldwide.
                </p>
              </CardContent>
            </Card>
            <Card className="bg-card border-border/50">
              <CardContent className="pt-6">
                <Zap className="h-10 w-10 text-primary mb-4" />
                <h3 className="text-lg font-semibold mb-2">Instant Results</h3>
                <p className="text-muted-foreground">
                  Get results in seconds with our high-speed verification engine. 
                  No waiting, no delays.
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* Pricing Preview */}
      <section className="py-20 border-t border-border/50">
        <div className="container text-center">
          <h2 className="text-2xl md:text-3xl font-bold mb-4">Simple Credit-Based Pricing</h2>
          <p className="text-muted-foreground mb-8 max-w-xl mx-auto">
            Purchase credits and use them for any check. No subscriptions, no hidden fees.
          </p>
          <div className="flex flex-wrap justify-center gap-4 mb-8">
            {["$25", "$50", "$100", "$250"].map((price) => (
              <div key={price} className="flex items-center gap-2 rounded-lg border border-border/50 bg-card px-5 py-3">
                <CreditCard className="h-4 w-4 text-primary" />
                <span className="font-semibold">{price}</span>
              </div>
            ))}
          </div>
          <Link href="/topup">
            <Button size="lg" variant="outline" className="gap-2">
              View All Packages <ArrowRight className="h-4 w-4" />
            </Button>
          </Link>
        </div>
      </section>

      {/* Trust Indicators */}
      <section className="py-16 border-t border-border/50">
        <div className="container">
          <div className="flex flex-wrap justify-center gap-8 text-muted-foreground">
            <div className="flex items-center gap-2">
              <CheckCircle className="h-5 w-5 text-green-500" />
              <span>Secure Payments</span>
            </div>
            <div className="flex items-center gap-2">
              <CheckCircle className="h-5 w-5 text-green-500" />
              <span>Instant Delivery</span>
            </div>
            <div className="flex items-center gap-2">
              <CheckCircle className="h-5 w-5 text-green-500" />
              <span>24/7 Support</span>
            </div>
            <div className="flex items-center gap-2">
              <CheckCircle className="h-5 w-5 text-green-500" />
              <span>No Subscription</span>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="border-t border-border/50 py-8">
        <div className="container flex flex-col md:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-2">
            <Shield className="h-5 w-5 text-primary" />
            <span className="font-semibold">imeihub</span>
          </div>
          <p className="text-sm text-muted-foreground">
            &copy; {new Date().getFullYear()} imeihub. All rights reserved.
          </p>
        </div>
      </footer>
    </div>
  );
}
