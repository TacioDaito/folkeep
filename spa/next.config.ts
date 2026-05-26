import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  allowedDevOrigins: [
    'folkeep.app.localhost',
    'folkeep.app.localhost:80',
    '192.168.1.100:3000',
  ],
};

export default nextConfig;
