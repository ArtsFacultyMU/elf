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
 * Czech language strings for local ELF login form.
 *
 * @package    local_elf_login
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Přihlašovací stránka pro ELF';
$string['unified_login'] = 'Jednotné přihlášení';

$string['teachers_and_students'] = 'Učitelé a studenti';
$string['others_and_guests'] = 'Ostatní a hosté';

$string['cannot_login'] = 'Nemohu se přihlásit';
$string['first_time_here'] = 'Jste tady poprvé?';

$string['in_ismuni'] = 'podle IS MUNI';
$string['outside_ismuni'] = 'mimo IS MUNI';

$string['teachers_students_info_1'] = 'Pokud máte aktivní účet v 
        <a href="{$a->ismunilink}" target="_blank">Informačním systému
        Masarykovy univerzity (IS MUNI)</a>, pro vstup do ELFu zadejte do
        přihlašovacího formuláře (po kliknutí) své <b>UČO</b> a své
        <b>sekundární heslo</b>. V případě problémů si sekundární heslo můžete
        nastavit či obnovit <b><a href="{$a->ismunipasslink}"
        target="_blank">přímo na tomto místě</a></b>.';
$string['teachers_students_info_2'] = '<b>PRVNÍ PŘIHLÁŠENÍ:</b> Studenti a
        učitelé <b>FF</b>, <b>FSS</b> a <b>FSpS</b> mají v ELFu <b>účet
        založen automaticky</b>. Ostatním uživatelům, kteří mají účet v IS MUNI,
        se <b>účet v ELFu vytvoří v okamžiku prvního přihlášení</b> pomocí UČO
        a sekundárního hesla: po úspěšné autentizaci budete přesměrováni na
        stránku pro úpravu osobního profilu.';

$string['others_guests_info_1'] = 'Tuto metodu přihlášení do e-learningového
        prostředí FF MUNI (ELF) použijte v případě, že jste od učitele kurzu či
        správce systému obdrželi přístupové údaje (tj. uživatelské jméno a
        heslo), ale <b>nevlastníte aktivní účet v <a href="{$a->ismunilink}"
        target="_blank">Informačním systému Masarykovy university.</a></b>
        Jedná se např. o studenty jiných institucí, externí vyučující,
        přípravné kurzy apod.';
$string['others_guests_info_2'] = 'Stejným způsobem můžete k ELFu přistupovat
        jako <b>hosté</b> (tlačítko "Přihlásit se jako host") - některé kurzy
        mohou umožňovat vstup hostům, případně hostům s klíčem k zápisu
        (přiděluje vždy vedoucí kurzu).';

$string['unknown_error'] = 'Neznámá chyba ({$a}).';