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

namespace enrol_ismu\helpers;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class to handle the single semester.
 *
 * @package    enrol_ismu
 * @copyright  2021 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */
class semester {
    const SEMESTER_AUTUMN = 'autumn';
    const SEMESTER_SPRING = 'spring';

    /**
     * Year in which semester belongs.
     * 
     * @var int Full (four digit) representation of the (eg. 2010, not 10).
     */
    protected $year;

    /**
     * Semester of the given year.
     * 
     * @var string Only "spring" or "autumn".
     */
    protected $semester;

    // Object construction.
    public static function get_current_semester() {
        // If there is no current semester set, choose one based on current date.
        if (!($currentperiod = get_config('enrol_ismu', 'currentperiod'))) {
            $year = (int)(new \DateTime())->format('Y');
            $month = (new \DateTime())->format('m');

            // In January/February choose autumn semester of the previous year.
            if (in_array($month, ['01', '02'])) {
                $currentperiod = ($year - 1) . '_' . self::SEMESTER_AUTUMN;
            
            // From September onwards choose current year's autumn semester. 
            } elseif (in_array($month, ['09', '10', '11', '12'])) {
                $currentperiod = $year . '_' . self::SEMESTER_AUTUMN;

            // Between March and August choose current year's spring semester.
            } else {
                $currentperiod = $year . '_' . self::SEMESTER_SPRING;
            }

            // Saves newly created value.
            set_config('currentperiod', $currentperiod, 'enrol_ismu');
        }
        
        return self::get_instance_by_semester_code($currentperiod);
    }

    /**
     * Returns instance based on semester selection start admin setting.
     * 
     * If not provided, returns current semester instead.
     * 
     * @return self
     */
    public static function get_semesterselection_start() : self {
        if ($code = get_config('enrol_ismu', 'periodselectionstart')) {
            return self::get_instance_by_semester_code($code);
        }
        return self::get_current_semester();
    }

    /**
     * Returns instance based on semester selection end admin setting.
     * 
     * If not provided, returns current semester instead.
     * 
     * @return self
     */
    public static function get_semesterselection_end() : self {
        if ($code = get_config('enrol_ismu', 'periodselectionend')) {
            return self::get_instance_by_semester_code($code);
        }
        return self::get_current_semester();
    }

    /**
     * Constructs an instance based on the semester code.
     * 
     * @param string $code Code provided by full() method (for example "2020_autumn").
     */
    public static function get_instance_by_semester_code(string $code) {
        list($year, $period) = explode('_', $code);
        return new self((int)$year, $period);
    }

    /**
     * Constructs a new semester object.
     * 
     * @param int $year Year in which semester belongs.
     * @param string $semester Whether it is autumn or spring semester.
     */
    public function __construct(int $year, string $semester) {
        // Check whether semester is valid.
        if (!in_array($semester, [self::SEMESTER_SPRING, self::SEMESTER_AUTUMN])) {
            throw new \moodle_exception('exception_semester_invalid_semester', 'enrol_ismu');
        }

        // Rough check whether year is in reasonable bounds.
        if ($year < 1980 || $year > 2060) {
            throw new \moodle_exception('exception_semester_invalid_year', 'enrol_ismu');
        }

        // Saving parameters.
        $this->year = $year;
        $this->semester = $semester;
    }

    // Getters.
    /**
     * Returns semester (period) within year.
     * 
     * @return string "spring" or "autumn".
     */
    public function semester() : string {
        return $this->semester;
    }

    /**
     * Returns year semester belong into.
     * 
     * @return int Full year (eg. 2010, not only 10).
     */
    public function year() : int {
        return $this->year;
    }

    /**
     * Returns full semester code, for example "2020_autumn".
     */
    public function full() : string {
        return $this->year . '_' . $this->semester;
    }

    /**
     * Returns human readable (and localized) representation of semester,
     * for example "Autumn 2020".
     * 
     * @return string
     */
    public function human_readable() : string {
        return get_string($this->semester(), 'enrol_ismu') . ' ' . $this->year();
    }

    // Changing semesters.
    /**
     * Returns object of the previous semester.
     * 
     * @return self
     */
    public function previous() : self {
        // Autumn semester returns spring semester of the same year.
        if ($this->semester == self::SEMESTER_AUTUMN) {
            return new self($this->year, self::SEMESTER_SPRING);
        
        // Spring semester returns autumn semester of the previous year.
        } else {
            return new self($this->year - 1, self::SEMESTER_AUTUMN);
        }
    }
    
    /**
     * Returns object of the next semester.
     * 
     * @return self
     */
    public function next() : self {
        // Spring semester returns autumn semester of the same year.
        if ($this->semester == self::SEMESTER_SPRING) {
            return new self($this->year, self::SEMESTER_AUTUMN);
        
        // Autumn semester returns spring semester of the next year.
        } else {
            return new self($this->year + 1, self::SEMESTER_SPRING);
        }
    }

    // Comparing semesters.
    /**
     * Compares two semester order.
     * 
     * If current semester is before the other (in time), function returns -1.
     * If current semester is after the other (in time), function returns 1.
     * If it is the same semester, function returns 0.
     * 
     * @param self $other Semester to compare with.
     * 
     * @return int Integer between -1 and 1, see description for further info.
     */
    public function compare(self $other) : int {
        // Compares if the semester is the same.
        if ($this->full() === $other->full()) {
            return 0;
        
        // If they are in the same year, compare by semester.
        } elseif ($this->year() === $other->year()) {
            if ($this->semester() === self::SEMESTER_SPRING) return -1;
            if ($this->semester() === self::SEMESTER_AUTUMN) return 1;
        
        // If they are not in the same year, compare years.
        } else {
            if ($this->year() < $other->year()) return -1;
            if ($this->year() > $other->year()) return 1;
        }
    }

    // Other (utility) functions.
    /**
     * Returns array of semester objects for the admin settings.
     * 
     * @return array Array of semester instances.
     */
    public static function get_adminsettings_semesters() : array {
        $currentsemester = self::get_current_semester();
        
        $availablesemesters = [];

        // Process future semesters.
        $semester = $currentsemester;
        for ($i = 0; $i < 5; $i++) {
            $semester = $semester->next();
            $availablesemesters[$semester->full()] = $semester->human_readable();
        }
        $availablesemesters = array_reverse($availablesemesters);

        // Process current semester.
        $semester = $currentsemester;
        $availablesemesters[$semester->full()] = $semester->human_readable();

        //Process past semesters.
        for ($i = 0; $i < 5; $i++) {
            $semester = $semester->previous();
            $availablesemesters[$semester->full()] = $semester->human_readable();
        }

        return $availablesemesters;
    }

    public static function get_coursesettings_semesters() : array {
        $current = self::get_current_semester();
        $start = self::get_semesterselection_start();
        $end = self::get_semesterselection_end();

        // If start is older than end, swap them.
        if ($start->compare($end) === -1) list($start, $end) = [$end, $start];
        
        // If current semester is newer than start, extend array up to current.
        if ($current->compare($start) === 1) $start = $current;
        
        // If current semester is older than end, extend array up to current.
        if ($current->compare($end) === -1) $end = $current;
        
        // Build final array.
        $availablesemesters = [];
        $semester = $start;
        while (True) {
            $availablesemesters[$semester->full()] = $semester->human_readable();

            if ($semester->compare($end) === 0) break;
            $semester = $semester->previous();
        }

        return $availablesemesters;
    }
}