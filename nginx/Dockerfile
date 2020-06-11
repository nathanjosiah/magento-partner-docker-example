FROM magento/magento-cloud-docker-nginx:1.9
RUN sed -i 's/fpm:/fpm-73:/' /etc/nginx/conf.d/default.conf
RUN sed -i 's/localhost/magento/' /etc/nginx/conf.d/default.conf
RUN sed -i 's_!MAGENTO\_ROOT!_/magento/magento-ce_' /etc/nginx/conf.d/default.conf