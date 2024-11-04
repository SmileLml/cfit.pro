FROM registry.cfit.cn/ted/public/php:v7.2.38-nginx
MAINTAINER lgy
COPY ./ /var/www/zentao/

#设置时区
RUN ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
RUN echo 'Asia/Shanghai' >/etc/timezone
RUN chmod -R 777 /var/www/zentao/*

EXPOSE 80