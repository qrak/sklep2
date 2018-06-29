# Sklep koscieya

Sklep Koscieya. Cechy:
- Wykorzystuje PHP + mySQL.
- Tworzy sam bazę danych i tabele w mysql
- Zawiera panel admina który umożliwia wrzucanie/modyfikowanie produktów naszego sklepu, przegląd klientów.
- Używa PHPMailer który wysyła e-mail z danymi dla klienta i do właściciela sklepu z danymi o zakupionych produktach.
- Używa "prepared statements" do przechowywania rekordów bazy danych.
- Używa hashowanego hasła administratora w bazie danych.

# Konfiguracja:

- Przed wrzuceniem sklepu na swój serwer należy edytować najpierw plik config.php w katalogu config.
- Zmienna $title to nazwa Twojego sklepu
- W miejscu:
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'cart');
    Ustawiamy wartości stałe naszej bazy danych mysql
- Sklep obsługuje php w wersji 7
- Należy stworzyć konto admina naszastrona/admin/register.php

# W przyszłości:
- Odzyskiwanie zapomnianego hasła administratora.
- Rejestracja nowych użytkowników by nie musieli ponownie wpisywać swoich danych przy zakupie.
- Akceptacja ciasteczek i informacja o RODO.
