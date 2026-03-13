#!/bin/bash
set -e

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL to be ready..."
until pg_isready -U postgres; do
  echo "PostgreSQL is unavailable - sleeping"
  sleep 2
done

echo "PostgreSQL is ready, creating authentik database..."

# Create the authentik database
psql -v ON_ERROR_STOP=1 -U postgres -d postgres <<-EOSQL
    CREATE DATABASE authentik;
    GRANT ALL PRIVILEGES ON DATABASE authentik TO postgres;
    \c authentik;
    CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
    CREATE EXTENSION IF NOT EXISTS "pgcrypto";
EOSQL

echo "Authentik database created successfully!"