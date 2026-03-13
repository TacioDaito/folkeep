FROM quay.io/keycloak/keycloak:26.1.2

# Bake the build into the image
RUN /opt/keycloak/bin/kc.sh build --db=postgres

# Copy initialization script and make it executable
COPY scripts/init-keycloak.sh /opt/keycloak/scripts/init-keycloak.sh
RUN chmod +x /opt/keycloak/scripts/init-keycloak.sh

# Copy realm templates
COPY keycloak-templates/ /opt/keycloak/templates/

ENTRYPOINT ["/opt/keycloak/bin/kc.sh"]
