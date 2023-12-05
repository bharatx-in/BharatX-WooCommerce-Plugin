# Use the desired WordPress base image
FROM wordpress:6.3.2

# Set WooCommerce version
ENV WOOCOMMERCE_VERSION 8.2.1
RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y unzip

# Install WooCommerce
RUN curl -o /tmp/woocommerce.zip -L "https://downloads.wordpress.org/plugin/woocommerce.$WOOCOMMERCE_VERSION.zip" && \
    unzip /tmp/woocommerce.zip -d /var/www/html/wp-content/plugins/ && \
    rm /tmp/woocommerce.zip
