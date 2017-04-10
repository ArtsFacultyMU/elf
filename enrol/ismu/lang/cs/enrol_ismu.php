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
 * Strings for component 'enrol_ismu', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package   enrol_ismu
 * @author    2012 Filip Benčo
 */

$string['course_codes'] = 'Kód předmětu';
$string['course_codes_help'] = '<p>Do tohoto pole napište kód přiřazený vašemu předmětu v Informačním systému. Tento kód neovlivní název kurzu v ELFu (nezaměňujte s polem <i>Krátký název</i> uvedeným výše na stránce nastavení kurzu), slouží pouze pro identifikace odpovídajícího předmětu v IS MU.</p>
<b>Možnosti vkládání kódu podle IS MU</b> 
<ul>
<li><i><b>FF0001,FF0002,FF0003</b></i> - Pokud potřebujete do kurzu zapsat studenty podle více předmětů v ISu, vložte jednotlivé kódy předmětů za sebou a oddělte je čárkou.</li>
<li><i><b>FF0001/A,FF0001/C</b></i> - Pokud chcete do kurzu zapsat pouze studenty z vybraných seminárních skupin (jedné i více) předmětu podle ISu, zapisujte tyto seminární skupiny přesně podle jejich označení v IS MU (typicky s lomítkem). Při použití více vybraných seminárních skupin vložte jednotlivé kódy za sebou a oddělte je čárkou (bez mezery).</li> 
<li><i><b>FF0001/A,FF0003</b></i> - Výše popsaná nastavení lze libovolně kombinovat, například použít kód pro celý předmět a doplnit jej kódem pro vybranou seminární skupinu předmětu jiného.</li>
</ul>
<b>Poznámka:</b> Pokud do pole <i>Kód předmětu</i> zapíšete kód z ISu obsahující přímé upřesnění seminární skupiny (např. FF0001/A), budou do kurzu zapsáni přesně pouze studenti této seminární skupiny. Vytvoření odpovídající skupiny v kurzu je však přesto dále závislé na nastavení položky <i>Vytvořit seminární skupiny</i> níže.';
$string['create_seminars'] = 'Vytvořit seminární skupiny';
$string['create_seminars_help'] = '<p>Použití tohoto pole je možné pouze v případě, že jste u předchozí položky <i>Automaticky zapsat studenty podle IS MU</i> nastavili volbu <i>Ano - zapsané studenty</i>.</p>

<p>Výběrem možnosti <i><b>vytvořit skupiny</b></i> budou v kurzu automaticky vytvořeny skupiny odpovídající seminárním skupinám v IS MU a zároveň do nich podle stejného klíče rozřazeni studenti. V případě výběru druhé možnosti <i><b>nevytvářet skupiny</b></i> budou studenti do kurzu zapsáni bez vytvoření jakýchkoli skupin.</p> 

<p><b>POZOR:</b> Pokud chcete v kurzu používat jak automaticky vytvořené skupiny podle ISu, tak skupiny vytvořené manuálně, je nutné před manuálním vytvářením skupin vypnout automatický zápis podle ISu (<i>Automaticky zapsat studenty podle IS MU -- Ne</i>). Pokud tak neučiníte, budou Vám ručně vytvořené skupiny periodicky mazány ve shodě s obnovou automatického zápisu (interval přibližně každých 30 minut).</p>';
$string['create_seminars_no'] = 'Nevytvářet skupiny';
$string['create_seminars_yes'] = 'Vytvořit skupiny v kurzu';
$string['enrol_enrolled'] = 'Ano - Zapsané studenty';
$string['ismu:config'] = 'Konfigurovat instance IS MU';
$string['enrol_no'] = 'Ne';
$string['enrol_registered'] = 'Ano - Registrované studenty';
$string['enrol_status'] = 'Automaticky zapsat studenty podle IS MU';
$string['enrol_status_help'] = "Zde můžete zvolit, zda chcete, aby byli jako účastníci kurzu automaticky zapisováni studenti, kteří mají kurz v aktuálním období zaregistrován/zapsán v Informačním systému MU. Pro použití automatického zápisu je nutné správně vyplnit předcházející pole <i>Kód předmětu</i>. Pokud chcete z ISu přebrat i rozdělení seminárních skupin, věnujte rovněž pozornost nastavení pole <i>Vytvořit seminární skupiny</i>.

<b>Možnosti nastavení automatického zápisu</b> 

* <b><i>NE</i></b> - Automatický zápis studentů podle ISu nebude spuštěn (bude zastaven).
* <b><i>ANO - REGISTROVANÉ STUDENTY</i></b> - Do kurzu budou zapsáni všichni studenti, kteří mají předmět <b>registrován</b> v IS MU. Toto nastavení neumožňuje automatické vytvoření skupin kurzu podle seminárních skupin v Informačním systému.
* <b><i>ANO - ZAPSANÉ STUDENTY</i></b> - Do kurzu budou zapsáni všichni studenti, kteří mají předmět v IS MU <b>zapsán</b>. Použití tohoto nastavení můžete kombinovat podle potřeby s automatickým vytvořením skupin kurzu podle seminárních skupin v Informačním systému (viz nastavení u položky <i>Vytvořit seminární skupiny</i>). 

<b>Poznámka: Tato služba je dostupná <u>pouze</u> u předmětů vyčovaných na FF, FSS a FSpS MU.</b>
Hromadné přebírání dat z IS MU pro jiné fakulty (v případě potřeby) musí být odsouhlaseno proděkanem příslušné fakulty.";
$string['groupswarning'] = '<strong>POZOR:</strong> Máte <strong>zapnutý automatický zápis podle IS MU</strong>. Všechny manuálně vytvořené skupiny budou automaticky vymazány. Pokud chcete používat manuálně vytvořené skupiny, vypněte nejprve automatický zápis v nastavení kurzu.';
$string['pluginname_desc'] = 'Nastavení automatického zápis studentů podle IS MU';
$string['students_courses'] = 'Kurzy pro studenty';
$string['students_courses_desc'] = 'Kódy kurzů (ID), oddělené čárkou, do kterých mají být automaticky zapsáni všichni studenti.';
$string['teachers_courses'] = 'Kurzy pro učitele';
$string['teachers_courses_desc'] = 'Kódy kurzů (ID), oddělené čárkou, do kterých mají být automaticky zapsáni všichni učitelé.';