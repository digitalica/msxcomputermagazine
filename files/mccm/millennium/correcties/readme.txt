MSX4PC
------

MSX2 Emulator voor PC's 
Door Adriaan van Doorn

Versie 3.3
30 maart 1996

MSX Computer & Club Magazine
Aktu Publications BV
Amsterdam, Nederland

-----------------------------------------------------------------------------

Informatiebestand README.TXT, 26 maart 1996

Dit bestand bevat informatie over het installeren en gebruiken van de
emulator MSX4PC, beperkingen en bekende problemen.

1. Bestanden
2. Installeren
3. MSX diskettes
4. Starten onder MS-DOS
5. Starten onder Windows
6. Emulatoropties
7. Toetsenbord
8. Problemen
9. Upgrades

-----------------------------------------------------------------------------

1. Bestanden

MSX4PC 3.0 bestaat uit de volgende bestanden:

Emulator
  MSX.EXE         Emulator in real mode
  MSXPROT.EXE     Emulator in protected mode

Informatie
  README.TXT      De tekst die u nu leest
  KNOWNBUG.TXT    Beschrijving van een bekende fout
  MSX.C           Hoofdbestand C-project met versieinformatie
  ALL.TXT         Overzicht van software die bij test werkte

Utility's
  SAVEROMS.BAS    MSX BASIC programma om ROM's op disk te plaatsen
  Om de snelheid te testen van de emulator met de standaard MSX;
  elk van de programma's levert op de gewone MSX de waarde 100:
  SPDCHK1.BAS     Telt met integers
  SPDCHK2.BAS     Berekent een aantal vierkantswortels
  SPDCHK3.BAS     Trekt een groot antal lijnen
  SPDCHK4.BAS     Voert een groot aantal COPY opdrachten uit
  SPDCHK5.BAS     Laat een sliert sprites over het scherm trekken
  KEYBOARD.BAS    Simpel programmaatje om te testen waar de diverse toetsen
                  van de MSX op het PC toetsenbord zijn terug te vinden

Trojka van Koert van Mensvoort om gelijk op PC te kunnen MSX'en
  TROJKA.ZIP      Uitpakken met PKZIP (of PKUNZIP) op de PC. AUTOEXEC.BAS
                  start het spel.


2. Installeren

MSX4PC wordt op een PC geïnstalleerd door de bestanden MSX.EXE en
MSXPROT.EXE te kopiëren naar de gewenste directory op de harddisk. Op de 
harddisk moet een vrije ruimte van ongeveer 1.5 MB beschikbaar zijn.

Voor emulatie zijn de ROM's van een MSX computer noodzakelijk. 
Omdat deze ROM's worden beschermd door auteursrechten, worden ze niet met de
emulator meegeleverd. Het programma SAVEROMS.BAS bewaart de inhoud van de
ROM's van een originele MSX computer in bestanden op een diskette. De 
MSX4PC diskette biedt hiervoor voldoende ruimte. Plaats de diskette in de 
A-drive van de MSX en voer onder BASIC in:

  RUN "SAVEROMS.BAS"

Druk vervolgens op de Return-toets. Het programma plaatst de volgende vier
bestanden op de disk:

BIOS.ROM
BASIC.ROM
EXTENDED.ROM
DISK.ROM

Kopieer deze bestanden naar dezelfde directory op de harddisk van de PC als
de emulator.

Hoewel eenvoudig bleken bovenstaande instructies voor sommigen toch lastig.
`Mijn MSX staat op zolder en mijn PC beneden' of `mijn MSX leende ik even uit
aan mijn neefje' en dergelijke. Heeft u dergelijke problemen, kunt u verder
worden geholpen op Internet. Zelf zoeken, want het blijkt te vaak te wijzigen
dat wij hier zinvol iets kunnen publiceren, maar ook diverse BBS'en kunnen u
helpen. Wij noemen er een waarvan de sysop ons garandeerde hulp te bieden:
Games BBS met nummer (0412) 640358.

Zie verder paragraaf 4 en 5 voor systeeminstellingen.


3. MSX diskettes

Onder de emulator kunnen MSX diskettes worden gebruikt via de diskdrive van
de PC. Hierbij zijn zowel dubbelzijdige als enkelzijdige diskettes te 
gebruiken.

De emulator maakt voor het formatteren van diskettes gebruik van een quick
format algoritme. Daarbij wordt alleen de logische informatie op de diskette
aangepast. De diskettes moeten dan ook al eerder in HETZELFDE formaat zijn
geformatteerd, bijvoorbeeld op een MSX of vanuit MS-DOS (alleen 720 kB).

De emulator maakt slechts gebruik van één fysieke diskdrive. Daarnaast wordt
er als B-drive een virtuele disk aangemaakt in de vorm van een bestand.
Dit wordt aangemaakt bij het starten van de emulator, in de op
dat moment actieve directory. Het is daardoor niet mogelijk MSX4PC van
diskette te starten, omdat een floppy onvoldoende ruimte biedt voor het
aanmaken van dit bestand. Het is mogelijk binnen de emulator de naam van
het als diskette gebruikte bestand te veranderen. Daardoor kunnen
verscheidene virtuele diskettes op de harddisk van de PC worden bewaard.
Op deze manier is het mogelijk de grote capaciteit van de harddisk ten bate
van de MSX in te zetten.


4. Starten onder MS-DOS

Onder MS-DOS maakt MSX4PC gebruik van EMS; er is 64 kB benodigd. Van het
gewone geheugen is 520 kB vrij noodzakelijk. De geëmuleerde MSX heeft dan 
de beschikking over 128 kB RAM.

Bij de opstartprocedure van de PC moet EMS worden aangemaakt. Eventueel 
moet daarom de optie `NOEMS' in CONFIG.SYS worden verwijderd. Als MSX4PC
niet zonder meer op uw PC werkt, kunt u de volgende procedure volgen.

Een kale versie van AUTOEXEC.BAT en CONFIG.SYS is op de diskette te vinden
onder de namen AUTOEXEC.DOS en CONFIG.DOS.
Deze files zullen in ieder geval voor uw eigen PC moeten worden
aangepast: de directory-verwijzingen naar `dos' moeten naar uw eigen dos-
directory verwijzen; `msx' moet verwijzen naar de directory waar u MSX4PC
hebt geïnstalleerd. Kopieer ALTIJD eerst CONFIG.SYS en AUTOEXEC.BAT van
uw eigen PC, voor u de bestanden van de diskette daar overheen zet. Na
bovengenoemde aanpassingen kunt u de computer resetten en moet MSX4PC
werken. Vervolgens kunt u uw eigen, oude CONFIG.SYS en AUTOEXEC.BAT stap
voor stap aanpassen door deze in overeenstemming te brengen met de `kale'
versies.

De emulator kan worden gestart door het programma MSX.EXE uit te voeren.
In deze versie van MSX4PC draait de processor in real mode.


5. Starten onder Windows

Bij gebruik vanuit Windows heeft MSX4PC geen EMS nodig; door gebruik te maken
van de protected mode van de 386 processor, krijgt de geëmuleerde MSX de
beschikking over 1 MB RAM.

De emulator kan onder Windows 3.x worden gestart door vanuit het menu
Bestand (File) in Programmabeheer (Program Manager) de optie Starten (Run)
te kiezen. Windows 95-gebruikers klikken op de Startknop (Start) en kiezen
dan Uitvoeren (Execute). Vervolgens moet het volledige pad naar de emulator
worden ingevoerd, of kan worden gezocht met de optie Bladeren (Browse).
Het te starten bestand heet MSXPROT.EXE.

Alternatief is het openen van een zogenaamde DOS-box in Windows. In die box
kunnen zowel de normale versie, als er EMS is, als de protected mode versie
worden opgestart.

Als MSX4PC niet zonder meer werkt, kunt u de procedure van de vorige
paragraaf volgen als u gebruik maakt van MS-DOS in combinatie met versie 3.x
van Windows. Draait uw computer onder Windows 95, volg dan de volgende
procedure.

Een kale versie van AUTOEXEC.BAT en CONFIG.SYS is op de diskette te vinden
onder de namen AUTOEXEC.W95 en CONFIG.W95.
Deze files zullen in ieder geval voor uw eigen PC moeten worden
aangepast: de directory-verwijzingen naar `dos' moeten naar uw eigen dos-
directory verwijzen; `msx' moet verwijzen naar de directory waar u MSX4PC
hebt geïnstalleerd en de verwijzingen naar `windows' moeten kloppen met
de plaats waar Windows 95 is geïnstalleerd. Hebt u de standaardinstallatie
van Windows 95 gebruikt, zullen de verwijzingen naar windows al correct
zijn. Kopieer ALTIJD eerst CONFIG.SYS en AUTOEXEC.BAT van uw eigen PC, voor
u de bestanden van de diskette daar overheen zet. Na bovengenoemde
aanpassingen kunt u de computer resetten en moet MSX4PC werken. Vervolgens
kunt u uw eigen, oude CONFIG.SYS en AUTOEXEC.BAT stap voor stap aanpassen
door deze in overeenstemming te brengen met de `kale' versies.


6. Emulatoropties

De emulator heeft een ingebouwde monitor/disassembler, waarmee de status van
de MSX is te bekijken. Deze monitor is op te roepen door de toetscombinatie
Alt-End.

Vanuit het menu kunnen de Z80-instructies voor de MSX één voor één worden
uitgevoerd (single stepping). Een overzicht van de opties is op te roepen
door een vraagteken in te voeren. Een andere mogelijkheid van het menu is
het kiezen van het bestand dat als tweede diskdrive fungeert.

De emulator is te verlaten door een q in te voeren.


7. Toetsenbord

De emulator werd ontwikkeld op een PC met een UK toetsenbord. Als u een
ander toetsenbord hebt, kan het zijn dat u bepaalde toetsen `kwijt' bent.
In de toekomst zal er vermoedelijk een configuratiemogelijkheid komen
waarbij ook andere toetsenborden gekozen kunnen worden. Vooralsnog moet u
zelf uitzoeken waar bepaalde toetsen zitten. Op de schijf staat het
programma KEYBOARD.BAS dat u kan helpen bij uw speurtocht. Een paar
toetsen die moeilijk te vinden zijn geven wij alvast.

	[GRAPH]   -  [F12]
	[CODE]    -  [PAGE DOWN]
	[END]     -  [STOP]
	[SELECT]  -  [PAGE UP]	


8. Problemen

Bekende problemen zijn de volgende.

* De karakters op scherm nul zijn acht beeldpunten breed, tegen zes op een
  MSX. Hergedefinieerde tekens kunnen daardoor onderbrekingen te zien geven.

* Split screens worden niet volledig ondersteund.

Een completer overzicht van de mogelijkheden in verschillende versies van
MSX4PC is te vinden in het bestand MSX.C; zie ook de tekst KNOWNBUG.TXT.


9. Upgrades

Upgrades van MSX4PC worden aan kopers verstrekt tegen vergoeding van de 
verzendkosten. Het verschijnen van een nieuwe versie wordt bekend gemaakt via
MSX Computer & Club Magazine. Daarbij wordt telkens het adres gepubliceerd
waarheen de originele diskette, samen met een geadresseerde en voldoende
gefrankeerde retourenvelop, kan worden opgestuurd.

-----------------------------------------------------------------------------
