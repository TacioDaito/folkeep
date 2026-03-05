"use client";
import { useSearchParams } from "next/navigation";
import Link from "next/link";

const ERROR_MESSAGES: Record<string, string> = {
    OAuthSignin: "Could not start the sign-in flow.",
    OAuthCallback: "Error during the OAuth callback.",
    OAuthCreateAccount: "Could not create account.",
    OAuthAccountNotLinked: "Account already linked to another user.",
    Callback: "Invalid callback — possible CSRF attack (state mismatch).",
    Default: "An authentication error occurred.",
};

export default function AuthError() {
    const params = useSearchParams();
    const error = params.get("error") ?? "Default";

    return (
        <main>
            <h1>Authentication Error</h1>
            <p>{ERROR_MESSAGES[error] ?? ERROR_MESSAGES.Default}</p>
            <Link href="/">← Back to home</Link>
        </main>
    );
}