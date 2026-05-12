import { useState } from "react";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { Route, Switch, Redirect } from "wouter";
import { trpc, makeTrpcClient } from "./lib/trpc";
import { DashboardLayout } from "./components/DashboardLayout";
import { AuthGuard } from "./components/AuthGuard";
import { LoginPage } from "./pages/Login";
import { HomePage } from "./pages/Home";
import { ProductsPage } from "./pages/Products";
import { CustomersPage } from "./pages/Customers";
import { ContractsPage } from "./pages/Contracts";
import { ContractDetailPage } from "./pages/ContractDetail";
import { NewContractPage } from "./pages/NewContract";
import { MessagesPage } from "./pages/Messages";
import { LineConnectionsPage } from "./pages/LineConnections";
import { BroadcastPage } from "./pages/Broadcast";

export function App() {
  const [queryClient] = useState(() => new QueryClient({
    defaultOptions: { queries: { retry: 1, refetchOnWindowFocus: false } },
  }));
  const [trpcClient] = useState(() => makeTrpcClient());

  return (
    <trpc.Provider client={trpcClient} queryClient={queryClient}>
      <QueryClientProvider client={queryClient}>
        <Switch>
          <Route path="/login" component={LoginPage} />
          <Route>
            <AuthGuard>
              <DashboardLayout>
                <Switch>
                  <Route path="/" component={HomePage} />
                  <Route path="/products" component={ProductsPage} />
                  <Route path="/customers" component={CustomersPage} />
                  <Route path="/contracts" component={ContractsPage} />
                  <Route path="/contracts/new" component={NewContractPage} />
                  <Route path="/contracts/:id">
                    {(params) => <ContractDetailPage id={Number(params.id)} />}
                  </Route>
                  <Route path="/messages" component={MessagesPage} />
                  <Route path="/line-connections" component={LineConnectionsPage} />
                  <Route path="/broadcast" component={BroadcastPage} />
                  <Route>
                    <Redirect to="/" />
                  </Route>
                </Switch>
              </DashboardLayout>
            </AuthGuard>
          </Route>
        </Switch>
      </QueryClientProvider>
    </trpc.Provider>
  );
}
