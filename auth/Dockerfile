FROM quay.io/keycloak/keycloak:26.1.2

# Bake the build into the image
RUN /opt/keycloak/bin/kc.sh build --db=postgres

ENTRYPOINT ["/opt/keycloak/bin/kc.sh"]