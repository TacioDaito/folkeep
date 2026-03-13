import NextAuth, { NextAuthOptions } from "next-auth";
import KeycloakProvider from "next-auth/providers/keycloak";

export const authOptions: NextAuthOptions = {
    providers: [
        KeycloakProvider({
            clientId: process.env.NEXT_PUBLIC_KEYCLOAK_CLIENT_ID!,
            clientSecret: process.env.NEXT_PUBLIC_KEYCLOAK_CLIENT_SECRET!,
            issuer: `${process.env.NEXT_PUBLIC_KEYCLOAK_URL}/realms/${process.env.NEXT_PUBLIC_KEYCLOAK_REALM}`,
            authorization: {
                params: {
                    scope: "openid email profile",
                },
            },
            checks: ["pkce", "state"],
        }),
    ],

    callbacks: {
        // NextAuth's predefined JWT callback. The account object is the
        // response from the provider after a successful authentication.
        // The token object is the JWT representing the user's session.
        async jwt({ token, account }) {
            if (account) {
                token.accessToken = account.access_token ?? "";
                token.idToken = account.id_token ?? "";
                token.refreshToken = account.refresh_token ?? "";
                token.expiresAt = account.expires_at ?? 0;
            }
            return token;
        },
        // NextAuth's predefined session callback. The session object
        // is updated with the JWT and returned to the client through
        // the useSession() hook.
        async session({ session, token }) {
            session.accessToken = token.accessToken as string;
            session.idToken = token.idToken as string;
            session.refreshToken = token.refreshToken as string;
            return session;
        },
    },

    pages: {
        error: "/auth/error",
    },

    session: {
        strategy: "jwt",
        maxAge: 30 * 60, // 30 minutes
    },
    
    jwt: {
        maxAge: 30 * 60, // 30 minutes
    }
};

const handler = NextAuth(authOptions);
export { handler as GET, handler as POST };
