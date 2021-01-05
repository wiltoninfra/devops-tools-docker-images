FROM prom/prometheus:v2.21.0

COPY ./alert.rules /etc/prometheus/
#COPY ./alert.rules_service-groups /etc/prometheus/
#COPY ./alert.rules_nodes /etc/prometheus/
#COPY ./alert.rules_tasks /etc/prometheus/
COPY ./prometheus.yml /etc/prometheus/