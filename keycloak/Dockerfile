FROM quay.io/keycloak/keycloak:26.1.2

# Bake the build into the image
RUN /opt/keycloak/bin/kc.sh build --db=postgres

# Copy initialization script and make it executable
COPY init-keycloak.sh /opt/keycloak/init-keycloak.sh
USER root
RUN chmod +x /opt/keycloak/init-keycloak.sh

# Copy realm templates
COPY /data/templates/ /opt/keycloak/templates/

ENTRYPOINT ["/opt/keycloak/bin/kc.sh"]
