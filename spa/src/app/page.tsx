// app/page.tsx
"use client";
import { signIn, signOut, useSession } from "next-auth/react";
import MeButton from "../components/MeButton";

export default function Home() {
  const { data: session } = useSession();

  if (session) {
    return (
      <main>
        <p>Welcome, {session.user?.name}</p>
        <p>Access token: <code>{session.accessToken.slice(0, 20)}…</code></p>
        <button onClick={() => signIn()}>Re-authenticate</button>
        <button onClick={async () => {
          const logoutUrl: string = process.env.NEXT_PUBLIC_KEYCLOAK_URL
           + "/realms/" + process.env.NEXT_PUBLIC_KEYCLOAK_REALM
           + "/protocol/openid-connect/logout?redirect_uri="
           + window.location.origin
           + "&id_token_hint=" + session.idToken;
          await signOut({ callbackUrl: logoutUrl});
        }}>Logout</button>
        <MeButton />
      </main>
    );
  }

  return (
    <main>
      <h1>My App</h1>
      <button onClick={() => signIn("keycloak")}>
        Log in with Keycloak
      </button>
    </main>
  );
}