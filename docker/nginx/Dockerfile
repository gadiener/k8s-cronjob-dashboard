###### START BASE BUILD ######

FROM nginx:1.15.7-alpine AS base

ENTRYPOINT ["entrypoint"]

CMD ["nginx", "-g", "daemon off;"]

EXPOSE 8080

WORKDIR /app

RUN touch /var/run/nginx.pid && \
    chown nginx:nginx /var/run/nginx.pid && \
    chown -R nginx:nginx /var/cache && \
    chown -R nginx:nginx /etc/nginx

COPY --chown=nginx:nginx docker/nginx/entrypoint.sh /usr/bin/entrypoint

COPY --chown=nginx:nginx docker/nginx/confs/default.template /etc/nginx/conf.d/

COPY --chown=nginx:nginx src/public public

USER nginx
