// app/page.tsx
"use client";
import { signIn, useSession } from "next-auth/react";
import MeButton from "../components/MeButton";

export default function Home() {
  const { data: session } = useSession();

  if (session) {
    return (
      <main>
        <p>Welcome, {session.user?.name}</p>
        <p>Access token: <code>{session.accessToken.slice(0, 20)}…</code></p>
        <button onClick={() => signIn()}>Re-authenticate</button>
        <MeButton />
      </main>
    );
  }

  return (
    <main>
      <h1>My App</h1>
      <button onClick={() => signIn("authentik")}>
        Log in with Authentik
      </button>
    </main>
  );
}