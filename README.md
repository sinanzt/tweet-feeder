## Tweet-Feeder

Uygulama kapsamında ;

- Kullanıcı kayıt,
- Kullanıcı sisteme giriş yapma,
- Kullanıcı telefon ve email doğrulaması yapma,
- Kullanıcının kullanıcı adına göre Twitter dan son twitlerini getirip kaydetme,
- Kullanıcının kullanıcı adına göre(başkasının) veya kullanıcı adı girmeden kendi tweetlerini listeleyebilme,
- Sisteme eklenmiş tweeti güncelleme,
- Sisteme eklenmiş tweeti paylaşma,

gibi işlemleri yapar.

## Projenin Gereklilikleri

#### Veri Tabanı

.env dosyası oluşturma
    
    cp .env.example .env

.env dosyası içeriği

    DB_CONNECTION=pgsql
    DB_HOST=postgres
    DB_PORT=5432
    DB_DATABASE=db_name
    DB_USERNAME=user_name
    DB_PASSWORD=password
    
    TWITTER_CONSUMER_KEY=JCPU7dT9zo12faGbDMVduV6LH
    TWITTER_CONSUMER_SECRET=n0OIHTKYPWwweezcwKRvo9R5w0NWlNaaW1uBjILmqMX9CtR99w
    TWITTER_ACCESS_TOKEN=
    TWITTER_ACCESS_TOKEN_SECRET=
    
veri tabanı tabloları oluşturma ve içlerini doldurma

    php artisan migrate:refresh --seed
    

#### Composer ile paketleri alma 
    composer install
    

#### Testleri çalıştırma
    phpunit
    
#### Swagger dökümantasyonu linki
    http://your-domain/api/documentation