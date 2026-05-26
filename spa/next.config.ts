import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  allowedDevOrigins: [
    process.env.NEXTAUTH_URL!.replace(/^https?:\/\//, ''),
    '192.168.1.100:3000',
  ],
};

export default nextConfig;
