FROM mysql:5.7

RUN echo 'never' > /sys/kernel/mm/transparent_hugepage/enabled && \
    echo 'never' > /sys/kernel/mm/transparent_hugepage/defrag

COPY gp.cnf /etc/mysql/conf.d/gp.cnf
RUN chmod 644 /etc/mysql/conf.d/gp.cnf
