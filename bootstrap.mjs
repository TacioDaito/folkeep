import { readFileSync, writeFileSync, existsSync } from 'fs';
import { randomBytes } from 'crypto';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const envPath = resolve(__dirname, '.', '.env');

if (!existsSync(envPath)) {
  writeFileSync(envPath, 'NEXTAUTH_SECRET=\n', 'utf8');
}

const env = readFileSync(envPath, 'utf8');
const match = env.match(/^NEXTAUTH_SECRET=(.+)$/m);

if (match && match[1].length > 0) {
  console.log('NEXTAUTH_SECRET already set, skipping');
  process.exit(0);
}

const secret = randomBytes(32).toString('base64');
const updated = env.replace(/^NEXTAUTH_SECRET=.*$/m, `NEXTAUTH_SECRET=${secret}`);
writeFileSync(envPath, updated, 'utf8');
console.log('NEXTAUTH_SECRET generated and written to .env');