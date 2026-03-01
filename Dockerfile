FROM php:8.2-apache

# ၁။ လိုအပ်သော System libraries များနှင့် Composer ကို သွင်းခြင်း
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ၂။ Project files များကို copy ကူးခြင်း
COPY . /var/www/html/

# ၃။ Composer dependencies များကို install လုပ်ခြင်း
WORKDIR /var/www/html
RUN composer install --no-interaction --optimize-autoloader

# ၄။ Permission ပေးခြင်း
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# --- ဤနေရာတွင် အောက်ပါ line ကို အသစ်ထည့်ပါ ---
# Apache ရော PHP Worker ရော တစ်ပြိုင်တည်း run ခိုင်းခြင်း
CMD apache2-foreground & php /var/www/html/worker.php
