# Tietokantasovelluksen esittelysivu

Yleisiä linkkejä:

* [Linkki sovellukseeni](https://haih.users.helsinki.fi/tsoh/)
* [Linkki dokumentaatiooni](https://github.com/Hansuzu/Tsoha-Bootstrap/blob/master/doc/dokumentaatio.pdf)

## Työn aihe

Tarkoituksena on luoda wikipedian kaltainen sivusto, jossa on käyttäjiä eri oikeuksilla, artikkeleita, joita voi lukea, muokata ja tarvittaessa poistaa, ja joiden versiohistoriaa voi tarkastella. Jokaiselle artikkelille on myös keskustelusivu. Artikkeleita voi myös olla eri kielillä.

## Sivut:

[Etusivu](https://haih.users.cs.helsinki.fi/tsoh/)

[Kirjautumissivu](https://haih.users.cs.helsinki.fi/tsoh/login)

[Rekisteröitymissivu](https://haih.users.cs.helsinki.fi/tsoh/signup)

[Artikkelihakusivu](https://haih.users.cs.helsinki.fi/tsoh/page) (Tyhjällä hakusanalla saa listattua kaikki artikkelit)

[Esimerkki artikkelisivust](https://haih.users.cs.helsinki.fi/tsoh/page/abb/diipadaapa)

[Artikkelin muokkaussivu](https://haih.users.cs.helsinki.fi/tsoh/page/edit)

[Artikkelin keskustelusivu](https://haih.users.cs.helsinki.fi/tsoh/page/discussion)

[Käyttäjä](https://haih.users.cs.helsinki.fi/tsoh/user/root)

## Käyttö- ja käynnistysohje.

Sovellusta käytetään selaimella [täällä](https://haih.users.helsinki.fi/tsoh/).
Kirjautuminen:
    Admin: root, tyhjä salasana
    Moderaattori: moderator, tyhjä salasana
    Tavallinen käyttäjä: user, tyhjä salasana
    (Tyhjä salasana sen vuoksi, että en itse jaksanut kirjoittaa salasanaa joka kerta, kun testatessa kirjaudun)
Lisäksi voi halutessaan itse kokeilla rekisteröidä uusia käyttäjiä.

Hakutoiminto etsii haettavaa merkkijonoa artikkeleiden otsikoista. Tyhjä hakusana listaa kaikki tietokannassa olevat artikkelit.

Artikkeleiden muokkauksessa voi muokata nimeä ja sisältöä. Muokkaus vaatii kirjautumisen. Tekstiä voi muotoilla [b], [i], [u] ja [s] -tageilla. Artikkeleihin voi lisätä linkkejä muihin artikkeleihin näin [[Artikkelin nimi]] tai [[Artikkelin nimi|linkissä näytettävä teksti]]. Artikkelille voi lisätä yläluokkia lisäämällä tekstikenttään ylä-artikkelin tupla-aaltosulkeisiin {{Artikkelin nimi}}. Uutta artikkelia lisättäessä voi lisäksi valita kielen. Artikkelin muokkaukseen liittyvät moderaattoritoimintoja ei vielä ole toteutettu.

Artikkelin osoite on muotoa /page/view/KIELI/NIMI/ tai /page/view/KIELI/NIMI/ID (jälkimmäinen versio on olemassa lähinnä siksi, koska tietokanta itsessään ei estä samannimisten artikkeleiden olemassaoloa. Pelkän nimen määrääminen uniikiksi ei olisi mielekästä, koska eri kielisissä artikkeleissa voi olla sama nimi. KIELI-NIMI-parin määrääminen uniikiksi ehkä onnistuisi, mutta olisi ikävän paljon monimutkaisempaa)

Moderaattori ja admin voivat muokata muiden käyttäjien oikeuksia näiden käyttäjien sivulla /tsoh/user/_käyttäjänimi_ ()

Moderaattorit voivat lisäksi muokata kielitietokantaa. Muokattaessa on muokkausten lisäksi halutulta riviltä valittava checkbox edit, jotta muokkaukset huomioidaan, kun lomake lähetetään. (Kielitietokannan muokkauksessa päällekkäisten arvojen virheenhallintaa ei ole vielä toteutettu. Jos yrittää esim. luoda kielen, jolla on sama nimi kuin olemassaolevalla kielellä, Mysql heittää virheen, koska taulun sarake on määrätelty olevan UNIQUE.)

Keskustelualueelle voi tällä hetkellä vain kirjoittaa viestejä. Artikkelisivujen ylälaidassa on linkki osioon Discussion.