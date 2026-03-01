FROM php:8.2-apache

# Redis extension နဲ့ တခြား လိုအပ်တာတွေ သွင်းခြင်း
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/

# Apache ရော Worker ရော တစ်ပြိုင်နက် run ဖို့ script တစ်ခု သုံးခြင်း
CMD php /var/www/html/worker.php & apache2-foreground
