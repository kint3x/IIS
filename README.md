# IIS

## TODO
- Doplniť db->close? Niekde chýba.
- V user_login sa nevytvára objekt user ale kód je podobný ako v konštruktore triedy user.
- SESSION['user'] je uložené 'id' a 'role'. V kóde sa potom na rôznych miestach vytvárajú z id objekty user -> možno uložiť do SESSION['user'] rovno objekt user?
- Doplniť možnosť zmeny emailu v nastaveniach. Treba skontrolovať unikátnosť.
- Pri zmene hesla v 'user_password' sa nekontroluje formát nového hesla.
- Po registrácii sa treba znova prihlasovať -> možno automaticky prihlásiť.
- Login formulár ako pop-up formulár namiesto menu? Menu sa občas skryje (napr. ak pri výbere textu vyjdem s myšou mimo menu) a je to nepríjemné.
- V ajaxoch máme volania DB. Prečo to nehodiť rovno ku classam ktoré dané tabulky spravujú?
