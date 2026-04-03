FROM alpine:latest

# Install cron and docker CLI
RUN apk add --no-cache dcron docker-cli

WORKDIR /app

COPY . .

# Create logs directory
RUN mkdir -p /app/logs && chmod 777 /app/logs

# Create cron job that executes the cleanup script in the PHP container
# Using container name from docker-compose: haarlem-festival-php-1
RUN echo "*/15 * * * * docker exec haarlem-festival-php-1 php /app/cli/cleanup-abandoned-carts.php >> /app/logs/cron.log 2>&1" | crontab -

# Verify crontab was installed
RUN crontab -l

# Create startup script that ensures log file exists, starts cron, and tails logs
RUN printf '#!/bin/sh\nset -e\necho "[INFO] Starting cron daemon..."\ntouch /app/logs/cron.log\nchmod 666 /app/logs/cron.log\n/usr/sbin/crond -f -l 2 &\nsleep 2\necho "[INFO] Cron daemon started. Monitoring logs..."\ntail -f /app/logs/cron.log\n' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]