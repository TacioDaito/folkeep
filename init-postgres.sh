#!/bin/bash
set -e

# Use CREATE DATABASE ... IF NOT EXISTS equivalent in PostgreSQL
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
    SELECT 'CREATE DATABASE keycloak'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'keycloak')\gexec

    SELECT 'CREATE DATABASE folkeep'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'folkeep')\gexec
EOSQL