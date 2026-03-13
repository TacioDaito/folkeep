#!/bin/bash

# Keycloak Initialization Script
# This script processes the realm template with environment variables and starts Keycloak

set -e

echo "Starting Keycloak initialization..."

# Create import directory if it doesn't exist
mkdir -p /opt/keycloak/data/import

# Read environment variables with defaults
REDIRECT_URIS=${KEYCLOAK_REDIRECT_URIS}
WEB_ORIGINS=${KEYCLOAK_WEB_ORIGINS}
TEST_USER_EMAIL=${KEYCLOAK_TEST_USER_EMAIL}
TEST_USER_PASSWORD=${KEYCLOAK_TEST_USER_PASSWORD}

# Function to convert comma-separated string to JSON array
convert_to_json_array() {
    local input="$1"
    # Remove surrounding quotes if present
    input="${input%\"}"
    input="${input#\"}"
    
    # Split by comma and create JSON array
    IFS=',' read -ra ADDR <<< "$input"
    json_array="["
    first=true
    for uri in "${ADDR[@]}"; do
        # Trim whitespace
        uri=$(echo "$uri" | xargs)
        if [ "$first" = true ]; then
            json_array="${json_array}\"${uri}\""
            first=false
        else
            json_array="${json_array},\"${uri}\""
        fi
    done
    json_array="${json_array}]"
    echo "$json_array"
}

# Convert comma-separated values to JSON arrays
REDIRECT_URIS_ARRAY=$(convert_to_json_array "$REDIRECT_URIS")
WEB_ORIGINS_ARRAY=$(convert_to_json_array "$WEB_ORIGINS")

# Escape values for safe sed injection
REDIRECT_URIS_ARRAY=$(printf '%s\n' "$REDIRECT_URIS_ARRAY" | sed 's/[&/\|]/\\&/g')
WEB_ORIGINS_ARRAY=$(printf '%s\n' "$WEB_ORIGINS_ARRAY" | sed 's/[&/\|]/\\&/g')
TEST_USER_EMAIL=$(printf '%s\n' "$TEST_USER_EMAIL" | sed 's/[&/\|]/\\&/g')
TEST_USER_PASSWORD=$(printf '%s\n' "$TEST_USER_PASSWORD" | sed 's/[&/\|]/\\&/g')

# Process the realm template with environment variables
echo "Processing realm configuration template..."
# Use a more precise replacement to avoid double quotes
sed \
  -e "s|\"__REDIRECT_URIS_PLACEHOLDER__\"|${REDIRECT_URIS_ARRAY}|g" \
  -e "s|\"__WEB_ORIGINS_PLACEHOLDER__\"|${WEB_ORIGINS_ARRAY}|g" \
  -e "s|\${KEYCLOAK_TEST_USER_EMAIL}|${TEST_USER_EMAIL}|g" \
  -e "s|\${KEYCLOAK_TEST_USER_PASSWORD}|${TEST_USER_PASSWORD}|g" \
  /opt/keycloak/data/templates/realm-config-template.json \
  > /opt/keycloak/data/import/realm-export.json

# Check if the sed command succeeded
if [ $? -eq 0 ]; then
    echo "Realm configuration generated successfully"
else
    echo "Error processing realm template"
    exit 1
fi

# Verify the generated file exists and has content
if [ -f "/opt/keycloak/data/import/realm-export.json" ] && [ -s "/opt/keycloak/data/import/realm-export.json" ]; then
    echo "Realm configuration file created: /opt/keycloak/data/import/realm-export.json"
else
    echo "Error: Realm configuration file was not created or is empty"
    exit 1
fi

# Start Keycloak with the generated configuration
echo "Starting Keycloak with realm import..."
exec /opt/keycloak/bin/kc.sh start --import-realm --optimized