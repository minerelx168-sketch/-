import { Toaster } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import NotFound from "@/pages/NotFound";
import { Route, Switch } from "wouter";
import ErrorBoundary from "./components/ErrorBoundary";
import { ThemeProvider } from "./contexts/ThemeContext";
import Home from "./pages/Home";
import TopUp from "./pages/TopUp";
import Dashboard from "./pages/Dashboard";
import Success from "./pages/Success";
import CreditHistory from "./pages/CreditHistory";

function Router() {
  return (
    <Switch>
      <Route path={"/"} component={Home} />
      <Route path={"/topup"} component={TopUp} />
      <Route path={"/dashboard"} component={Dashboard} />
      <Route path={"/success"} component={Success} />
      <Route path={"/credit-history"} component={CreditHistory} />
      <Route path={"/404"} component={NotFound} />
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <ErrorBoundary>
      <ThemeProvider defaultTheme="dark">
        <TooltipProvider>
          <Toaster />
          <Router />
        </TooltipProvider>
      </ThemeProvider>
    </ErrorBoundary>
  );
}

export default App;
