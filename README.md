# IIS

## TODO
- Prerobiť user_password.php a user_settings.php aby využíval novú User triedu. Pridať do triedy metody pre zmenu hesla a atribútov.
- Doplniť db->close? Niekde chýba.
- V user_login sa nevytvára objekt user ale kód je podobný ako v konštruktore triedy user.
- Doplniť možnosť zmeny emailu v nastaveniach. Treba skontrolovať unikátnosť.
- Pri zmene hesla v 'user_password' sa nekontroluje formát nového hesla.
- Po registrácii sa treba znova prihlasovať -> možno automaticky prihlásiť.
- Login formulár ako pop-up formulár namiesto menu? Menu sa občas skryje (napr. ak pri výbere textu vyjdem s myšou mimo menu) a je to nepríjemné.
- Kontrola chýb pri User, Database, Conferences.
- ajax call -> if (data.success) -> obsluhu vynať do funkcíí
- vyhľadávanie v mojich konferenciách
- required_once "../defines.php" nefunguje v triedach
