<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local language pack from https://elf.phil.muni.cz/moodledev/moodle2
 *
 * @package    mod
 * @subpackage newassignment
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actualversion'] = 'Aktuální verze';
$string['addnextsubmission'] = 'Upravit novou verzi úkolu';
$string['addsubmission'] = 'Upravit úkol';
$string['afterachievement'] = 'Po schválení';
$string['aftersubmission'] = 'Po odevzdání';
$string['allowsubmissionsfromdate'] = 'Povolit odevzdání úkolů od';
$string['allowsubmissionsfromdate_help'] = 'Pokud je položka povolena a je zadáno nějaké datum a čas, studentům nebude umožněno odevzdat svou práci dříve.

V opačném případě mohou studenti odevzdávat svou práci kdykoli od zpřístupnění úkolu.';
$string['allowsubmissionsfromdatesummary'] = 'Odevzdávání úkolů začne od: <strong>{$a}</strong>';
$string['alwaysshowdescription'] = 'Povolit zobrazení popisu';
$string['alwaysshowdescription_help'] = 'Toto nastavení lze použít jen tehdy, když je zadáno nějaké počáteční datum pro odevzdávání úkolu.

Pokud zvolíte možnost Ne, studenti neuvidí zadání úkolu, dokud nenastane termín nastavený u položky <i>Povolit odevzdávání úkolů od</i>. Při volbě Ano vidí studenti zadání ihned.';
$string['assignmentisdue'] = 'Termín odevzdání úkolu uplynul';
$string['assignmentname'] = 'Název úkolu';
$string['assignmentsperpage'] = 'Úkolů na stránku';
$string['completition'] = 'Požadovat schválení';
$string['completition_desc'] = 'Studentův úkol musí být učitelem označen jako schválený';
$string['completition_help'] = 'Zda má být činnost považována za splněnou poté, co je studentův úkol označen jako schválený. Ikona úspěšného či neúspěšného splnění je zobrazena podle toho, zda je <b>Konečný stav úkolu</b> nastaven na <i>Schváleno</i> nebo na <i>Vráceno k přepracování</i>.';
$string['confirmsubmission'] = 'Jste si absolutně jisti, že chcete odevzdat úkol v této podobě? Jakmile odešlete úkol k hodnocení, už nebudete mít možnost tuto verzi úkolu dále měnit.';
$string['confirmsubmissionok'] = 'Potvrdit odeslání';
$string['confirmsubmissioncancel'] = 'Zpět';
$string['currentgrade'] = 'Aktuální známka v klasifikaci kurzu';
$string['description'] = 'Popis';
$string['downloadactualversions'] = 'Stáhnout aktuální verze';
$string['downloadall'] = 'Stáhnout všechny verze';
$string['downloadnotgraded'] = 'Stáhnout neohodnocené';
$string['duedate'] = 'Termín odevzdání';
$string['duedate_help'] = 'Termín odevzdání určuje, do jakého termínu musí studenti odevzdat svoji práci.

Pokud je povoleno odevzdávání po termínu, studenti mohou odevzdávat i po zadaném termínu, ale jejich odevzdání bude označeno jako pozdní.';
$string['duedateno'] = 'Termín odevzdání není nastaven';
$string['duedatereached'] = 'Termín odevzdání pro tento úkol již proběhl.';
$string['editsubmission'] = 'Upravit mé řešení úkolu';
$string['error_feedbackstatus'] = 'U studentů označených červeně je nutné zadat STAV úkolu, jinak změny v tabulce hodnocení nebudou uloženy.';
$string['feedback'] = 'Reakce učitele';
$string['feedbackcomment'] = 'Komentář';
$string['feedbackfile'] = 'Opravený soubor';
$string['feedbackstatus'] = 'Konečný stav úkolu';
$string['feedbackstatus_accepted'] = 'Schváleno';
$string['feedbackstatus_declined'] = 'Vráceno k přepracování';
$string['filesandcomments'] = 'Soubory a komentáře';
$string['filesubmission'] = 'Soubor';
$string['filter'] = 'Filtr';
$string['filternone'] = 'Bez filtru';
$string['filterrequiregrading'] = 'Vyžaduje hodnocení';
$string['filtersubmitted'] = 'Odevzdáno';
$string['finalgrade'] = 'Výsledná známka';
$string['gradeaverage'] = 'Průměrná známka';
$string['graded'] = 'Udělena známka';
$string['gradedby'] = 'Hodnotil';
$string['gradedon'] = 'Čas hodnocení';
$string['gradefirst'] = 'První verze';
$string['gradelast'] = 'Poslední verze';
$string['gradehighest'] = 'Nejvyšší známka';
$string['grademethod'] = 'Výpočet výsledné známky';
$string['grademethod_help'] = 'Jelikož v úkolu s opravou můžete hodnotit více průběžných verzí, máte k dispozici několik způsobů, jak z průběžných hodnocení za jednotlivé verze vypočítat výslednou známku studenta za celý úkol.

<b>Nejvyšší známka</b> – jako výsledná známka se použije známka za verzi, ve které student dosáhl nejvyššího počtu bodů.

<b>Průměrná známka</b> – jako výsledná známka se použije průměrný počet bodů vypočtený ze všech odevzdaných verzí.

<b>První verze</b> – jako výsledná známka se použije počet bodů, kterého student dosáhl za první verzi úkolu (na ostatní verze se nebere ohled).

<b>Poslední verze</b> – jako výsledná známka se použije počet bodů, který student získal za poslední verzi úkolu.';
$string['grading'] = 'Hodnocení';
$string['gradingoptions'] = 'Seznam možností hodnocení';
$string['gradingstatus'] = 'Stav hodnocení';
$string['gradingstudentprogress'] = 'Hodnocený student: {$a->index} z {$a->count}';
$string['gradingsummary'] = 'Souhrn hodnocení';
$string['lastmodifiedgrade'] = 'Poslední změna (hodnocení)';
$string['lastmodifiedsubmission'] = 'Poslední změna (odevzdání)';
$string['maxfilessubmission'] = 'Maximální počet nahraných souborů';
$string['maxfilessubmission_help'] = 'Pro každou verzi úkolu bude možné odevzdat jen stanovený počet souborů.';
$string['messageprovider:newassignment_notification'] = 'Oznámení úkolu s opravou';
$string['modulename'] = 'Úkol s opravou';
$string['modulename_help'] = 'Úkoly umožňují učiteli zadávat úkoly, hodnotit odevzdané práce a komentovat je. Modul Úkol s opravou však přináší vyučujícímu oproti standardnímu Úkolu několik rozšíření.

Úkol s opravou umožňuje ke každému zadanému úkolu uchovávat průběžné verze. Každou z verzí je možné hodnotit průběžnou známkou a na základě hodnocení ji buď schválit nebo vrátit k přepracování. Za úkol jako celek pak student získává jednu výslednou známku.

Stejně jako u modulu Úkol, mohou být odevzdané práce hodnoceny jednoduchým přímým hodnocením, případně pokročilou metodou. Vedle odevzdaných prací je uchováváno vždy i hodnocení každé z průběžných verzí.

V úkolu s opravou může vyučující použít zveřejňování odevzdaných prací ostatním studentům kurzu. Stejně tak mohou být zveřejněny komentáře vyučujícího a opravené soubory, které byly vloženy jako odpověď na odevzdané úkoly studentů. U obou položek se učitel může rozhodnout, zda má zveřejňování probíhat anonymně, a kdy mají být práce či opravy studentům zveřejněny.';
$string['modulenameplural'] = 'Úkoly s opravou';
$string['nosavebutnext'] = 'Další';
$string['nosubmission'] = 'K tomuto úkolu nebylo nic odevzdáno.';
$string['notgraded'] = 'Nehodnoceno';
$string['notsubmitted'] = 'Úkol nebyl odevzdán';
$string['numberofparticipants'] = 'Počet účastníků';
$string['numberofsubmittedassignments'] = 'Odevzdali';
$string['numwords'] = '({$a} slov)';
$string['onlinetextfilename'] = 'Online text';
$string['onlinetextsubmission'] = 'Online text';
$string['onlyfiles'] = 'Pouze soubory';
$string['overdue'] = '<font color="red">Úkol má zpoždění: {$a}</font>';
$string['pluginadministration'] = 'Správa úkolu s opravou';
$string['pluginname'] = 'Úkol s opravou';
$string['preventlatesubmissions'] = 'Zakázat odevzdávání po termínu';
$string['preventlatesubmissions_help'] = 'Lze nastavit jen v případě, že je zadán nějaký <i>Termín odevzdání</i>. Pokud zvolíte Ano, studenti nebudou mít možnost odevzdat úkol, jakmile proběhne <i>Termín odevzdání</i>.';
$string['previous'] = 'Předchozí';
$string['publish'] = 'Nastavení zobrazování úkolů a oprav';
$string['publishafterduedate'] = 'Po uplynutí termínu';
$string['publishaftersubmission'] = 'Po odevzdání';
$string['publishfeedbacks'] = 'Zobrazit opravy učitele ostatním';
$string['publishfeedbacks_help'] = 'Podobně jako odevzdané úkoly studentů je možné zveřejňovat i opravy a komentáře vyučujícího k jednotlivým úkolům.

<b>Ne</b> – komentáře učitele ani opravené soubory se nezveřejňují.

<b>Pouze soubory</b> – bude zveřejněn pouze opravený soubor od vyučujícího.

<b>Soubory a komentáře</b> – bude zveřejněn opravený soubor i hodnotící komentář od vyučujícího.';
$string['publishfeedbacksanonymously'] = 'Zobrazit opravy učitele anonymně';
$string['publishfeedbacksanonymously_help'] = 'Pokud zaškrtnete, ostatní studenti neuvidí jméno vyučujícího, který zadával komentář či opravený soubor k danému úkolu.';
$string['publishnow'] = 'Kdykoli';
$string['publishsubmissions'] = 'Zobrazit odevzdané úkoly ostatním';
$string['publishsubmissions_help'] = 'Učitel volí, jestli mají být zveřejňovány práce studentů, a zda budou zveřejňovány průběžné verze, nebo až výsledná podoba práce.

<b>Ne</b> – práce studentů se nezveřejňují.

<b>Po odevzdání</b> – práce studentů se zobrazí ihned, jakmile je studenti odevzdají. Práce jsou tedy zveřejněny už před tím, než je vyučující ohodnotí. Zveřejňují se pouze aktuální verze úkolu.

<b>Po schválení</b> – práce studentů se zobrazí teprve tehdy, až jsou vyučujícím označeny jako schválené. Zveřejňují se tudíž hotové práce ve své konečné podobě.';
$string['publishsubmissionsanonymously'] = 'Zobrazit odevzdané úkoly anonymně';
$string['publishsubmissionsanonymously_help'] = 'Pokud zaškrtnete, ostatní studenti neuvidí jméno studenta, který odevzdal daný úkol.';
$string['publishsubpage'] = 'Přehled všech úkolů';
$string['publishtime'] = 'Zobrazit odkaz pro prohlédnutí';
$string['publishtime_help'] = 'Tato položka určuje, kdy mohou studenti vidět práce ostatních.

<b>Kdykoli</b> – student uvidí práce ostatních studentů ihned, jakmile někteří studenti své práce odevzdají (tzn. nezávisle na tom, zda student již odevzdal vlastní práci).

<b>Po odevzdání</b> – student neuvidí práce ostatních studentů, dokud neodevzdá práci vlastní.

<b>Po uplynutí termínu</b> – student uvidí práce ostatních studentů až poté, co uplyne termín pro odevzdání úkolu.';
$string['quickgrading'] = 'Rychlé hodnocení';
$string['quickgrading_help'] = 'Rychlé hodnocení hodnocení umožňuje zadávat známky přímo v tabulce studentů a odevzdaných prací.

Rychlé hodnocení nelze použít u pokročilých metod hodnocení jako je Rubrika a Průvodce hodnocením.';
$string['quickgradingchangessaved'] = 'Změny hodnocení byly uloženy.';
$string['quickgradingresult'] = 'Rychlé hodnocení';
$string['recentgrade'] = 'Aktuální známka';
$string['savechanges'] = 'Uložit změny';
$string['savenext'] = 'Uložit a zobrazit další';
$string['saveprevious'] = 'Uložit a zobrazit předchozí';
$string['sendlatenotifications'] = 'Upozorňovat učitele při pozdním odevzdání';
$string['sendlatenotifications_help'] = 'Pokud zvolíte Ano, vyučujícímu bude poslána zpráva, kdykoli nějaký student odevzdá svou práci  pozdě (tj. po stanoveném termínu odevzdání).';
$string['sendnotifications'] = 'Upozorňovat učitele emailem';
$string['sendnotifications_help'] = 'Pokud zvolíte Ano, vyučujícímu bude poslána zpráva, kdykoli nějaký student odevzdá svou práci.';
$string['settings'] = 'Nastavení úkolu';
$string['showallversions'] = 'Všechny verze';
$string['showfull'] = 'Zobrazit';
$string['showotherstudentssubmissions'] = 'Zobrazit úkoly ostatních';
$string['studentsallversions'] = 'Přehled všech verzí';
$string['submission'] = 'Odevzdaný úkol';
$string['submissioncomments'] = 'Komentáře k odevzdání';
$string['submissioncomments_help'] = 'Komentáře k odevzdání umožňují, aby vyučující a student vedli průběžnou diskuzi nad řešením úkolu.

Nejde o součást hodnocení, komentáře k odevzdávání slouží spíše jako místo pro rychlou komunikaci mezi studentem a učitelem.';
$string['submissionfile'] = 'Soubor';
$string['submissionmaxfilesize'] = 'Maximální velikost odevzdaného úkolu';
$string['submissionmaxfilesize_help'] = 'Studenti budou moci nahrát soubor jen do stanovené velikosti.';
$string['submissiononlinetext'] = 'Online text';
$string['submissions'] = 'Odevzdané úkoly';
$string['submissionsettings'] = 'Nastavení odevzdávání úkolů';
$string['submissionslocked'] = 'K tomuto úkolu již není možné přidávat další verze.';
$string['submissionstatus'] = 'Stav odevzdání úkolu';
$string['submissionstatus_'] = 'Neodesláno';
$string['submissionstatus_submitted'] = 'Odesláno k hodnocení';
$string['submissionstatusheading'] = 'Stav odevzdání/hodnocení úkolu';
$string['submissiontype'] = 'Typ úkolu';
$string['submissiontype_help'] = 'Vyučující může vybrat, jakým způsobem mají studenti odevzdávat úkol.

<b>Soubor</b> – studenti odevzdávají úkol v podobě nahrání souboru ze svého počítače.

<b>Online text</b> – studenti upravují obsah úkolu přímo na stránce úkolu ve vestavěném textovém editoru.';
$string['submissionversion'] = 'Verze';
$string['submitassignment'] = 'Uložit a odevzdat';
$string['submitted'] = 'Odevzdáno';
$string['submittedearly'] = 'Úkol byl odevzdán {$a} před termínem';
$string['submittedlate'] = 'Úkol byl odevzdán {$a} po termínu';
$string['submittedlateshort'] = '{$a} po termínu';
$string['timemodified'] = 'Poslední změna';
$string['timeremaining'] = 'Zbývá';
$string['timesubmitted'] = 'Čas odevzdání';
$string['updatetable'] = 'Aktualizovat tabulku';
$string['viewgradebook'] = 'Zobrazit známky';
$string['viewgrading'] = 'Zobrazit/hodnotit úkoly';
