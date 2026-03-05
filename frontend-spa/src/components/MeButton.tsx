"use client";

import { useState } from "react";
import { useSession } from "next-auth/react";
import axios from "axios";
import { AxiosError } from "axios";

export default function MeButton() {
    const { data: session } = useSession();
    const [result, setResult] = useState(null);
    const [error, setError] = useState<string | null>(null);

    async function handleClick() {
        setResult(null);
        setError(null);
        try {
            const { data } = await axios.get(
                `${process.env.NEXT_PUBLIC_API_BASE_URL!}/api/me`,
                { headers: { Authorization: `Bearer ${session?.accessToken}` } }
            );
            setResult(data);
        } catch (error: unknown) {
            if (error instanceof AxiosError) {
                setError(error.response?.data?.message ?? error.message);
            } else if (error instanceof Error) {
                setError(error.message);
            }
        }
    }

    return (
        <div>
            <button onClick={handleClick} disabled={!session}>
                GET /api/me
            </button>

            {result && <pre>{JSON.stringify(result, null, 2)}</pre>}
            {error && <p style={{ color: "red" }}>{error}</p>}
        </div>
    );
}