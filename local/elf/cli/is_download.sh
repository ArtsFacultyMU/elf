#!/bin/bash
#
#

###############################################################
###                           FUNKCE                        ###
###############################################################

VELIKLIMIT="5120"
MMAIL="benco@phil.muni.cz,miksik@phil.muni.cz"
PREDMET="chyba wget"

rok=$1
semestr=$2

wget_error() {
 echo "Pri stahovani souboru $1 doslo k chybe."
 echo "Prikaz wget vratil nenulovou navratovou hodnotu: $2"
 echo "Zpracovavani skriptu je preruseno."
 echo "Nebyl stazen soubor $1: wget vratil chybu $2." \
 | mail -s "$PREDMET" $MMAIL
 exit $2
}
wget_is_error() {
 echo "Pri stahovani souboru $1 doslo k chybe."
 echo "Prikaz wget zapsal soubor s podezrele malou delkou: $2"
 echo "Zpracovavani skriptu je preruseno."
 echo "Nebyl stazen soubor $1: delka $2." \
 | mail -s "$PREDMET" $MMAIL
 exit 7
}

# Obalkova funkce -- prvnim argumentem je soubor, do nejz je zapisovan vystup
wget_wrapper() {
 VYSTUP=$1
 shift
 command wget -nv -O $VYSTUP "$@" || wget_error $VYSTUP $?
 VELIK=`stat --format "%s" $VYSTUP`
 [ "$VELIK" -lt "$VELIKLIMIT" ] && wget_is_error $VYSTUP $VELIK
}

###############################################################
###                          PROMENNE                       ###
###############################################################

adr='/home/moodle/ismu_data/'$semestr'_'$rok #nastaveni adresare, kam se ukladaji data z IS MU
obdobi=$semestr'%20'$rok #nastaveni obdobi studia

###############################################################
###   NACTENI DAT Z EXPORTNI APLIKACE A JEJICH ZPRACOVANI   ###
###############################################################
echo 'import is_studenti'
date
echo '-------------------------------------------------------------------------'
# import is_studenti
wget_wrapper $adr/fss_1 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1423;obd=$obdobi;format=dvojt;kodovani=il2;typ=1"
wget_wrapper $adr/ff_1 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1421;obd=$obdobi;format=dvojt;kodovani=il2;typ=1"
wget_wrapper $adr/fsps_1 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1451;obd=$obdobi;format=dvojt;kodovani=il2;typ=1"
wget_wrapper $adr/prf_1 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1431;obd=$obdobi;format=dvojt;kodovani=il2;typ=1"
cat $adr/fss_1 $adr/ff_1 $adr/fsps_1 $adr/prf_1 > $adr/is_studenti
rm $adr/fss_1 $adr/ff_1 $adr/fsps_1 $adr/prf_1
# import is_studenti

# import is_studium
echo 'import is_studium'
date
echo '-------------------------------------------------------------------------'
wget_wrapper $adr/fss_2 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1423;obd=$obdobi;format=dvojt;kodovani=il2;typ=2"
wget_wrapper $adr/ff_2 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1421;obd=$obdobi;format=dvojt;kodovani=il2;typ=2"
wget_wrapper $adr/fsps_2 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1451;obd=$obdobi;format=dvojt;kodovani=il2;typ=2"
wget_wrapper $adr/prf_2 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1431;obd=$obdobi;format=dvojt;kodovani=il2;typ=2"
cat $adr/fss_2 $adr/ff_2 $adr/fsps_2 $adr/prf_2 > $adr/is_studium
rm $adr/fss_2 $adr/ff_2 $adr/fsps_2 $adr/prf_2
# import is_studium

# import is_ucitele
echo 'import is_ucitele'
date
echo '-------------------------------------------------------------------------'
wget_wrapper $adr/fss_3 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1423;obd=$obdobi;format=dvojt;kodovani=il2;typ=3"
wget_wrapper $adr/ff_3 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1421;obd=$obdobi;format=dvojt;kodovani=il2;typ=3"
wget_wrapper $adr/fsps_3 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1451;obd=$obdobi;format=dvojt;kodovani=il2;typ=3"
wget_wrapper $adr/prf_3 -T 1800 -t 2 --no-check-certificate "https://is.muni.cz/export/studium_export_data.pl?fak=1431;obd=$obdobi;format=dvojt;kodovani=il2;typ=3"
cat $adr/fss_3 $adr/ff_3 $adr/fsps_3 $adr/prf_3 > $adr/is_ucitele
rm $adr/fss_3 $adr/ff_3 $adr/fsps_3 $adr/prf_3
# import is_ucitele

###############################################################
###           IMPORT ZPRACOVANYCH DAT DO DATABAZE ELF2      ###
###############################################################

echo 'import do database elf2'
date
echo '-------------------------------------------------------------------------'
mysql -u elf2dbusr -pns88bUX9wYWtKUUj -h navazka <<**
use 'elf2';
-- cistka v tabulkach
DELETE FROM mdl_is_students;
DELETE FROM mdl_is_teachers;
DELETE FROM mdl_is_studies;

LOAD DATA LOCAL INFILE '/$adr/is_studenti' IGNORE INTO TABLE mdl_is_students FIELDS TERMINATED BY ':' LINES TERMINATED BY '\n' (@uco,nick,prijmeni,jmeno,fakulta,studium_id,zkratka_studijniho_programu,prezence_formy_studia,zkratky_aktivnich_oboru_studia,rok_prijeti_do_studia,maximalni_ze_semestru_aktivnich_oboru_studia) SET uco = @uco, username = CONCAT(TRIM(@uco),'@muni.cz');
DELETE FROM mdl_is_students WHERE uco=0;

LOAD DATA LOCAL INFILE '/$adr/is_ucitele' IGNORE INTO TABLE mdl_is_teachers FIELDS TERMINATED BY ':'  LINES TERMINATED BY '\n' (@uco,nick,prijmeni,jmeno,@dummy,@dummy,@dummy) SET uco = @uco, username = CONCAT(TRIM(@uco),'@muni.cz');
DELETE FROM mdl_is_teachers WHERE uco=0;

LOAD DATA LOCAL INFILE '/$adr/is_studium' IGNORE INTO TABLE mdl_is_studies FIELDS TERMINATED BY ':' LINES TERMINATED BY '\n' (@kod_predmetu,id,datum_zapisu,sem_skupina) SET kod_predmetu = TRIM(@kod_predmetu);

exit
**
date

echo 'The END'
