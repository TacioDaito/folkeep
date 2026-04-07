// app/providers.tsx
"use client";  // ✅ this makes it a Client Component

import { SessionProvider } from "next-auth/react";

export default function Providers({ children }: { children: React.ReactNode }) {
    return <SessionProvider>{children}</SessionProvider>;
}