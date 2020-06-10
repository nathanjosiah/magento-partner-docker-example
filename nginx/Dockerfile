FROM magento/magento-cloud-docker-nginx:1.9
COPY app.conf /etc/nginx/conf.d/
RUN sed -i 's/localhost/magento/' /etc/nginx/conf.d/default.conf
RUN sed -i 's_!MAGENTO\_ROOT!_/themount/magento-ce_' /etc/nginx/conf.d/default.conf