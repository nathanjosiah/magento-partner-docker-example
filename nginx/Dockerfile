FROM magento/magento-cloud-docker-nginx:1.9
COPY app.conf /etc/nginx/conf.d/
RUN sed -i 's/localhost/mage-nginx/' /etc/nginx/conf.d/default.conf
RUN sed -i 's%/app%/app/magento-ce%' /etc/nginx/conf.d/default.conf