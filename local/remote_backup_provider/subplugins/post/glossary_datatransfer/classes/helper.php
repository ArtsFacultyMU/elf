<?php

namespace remotebppost_glossary_datatransfer;

class helper {
    public static function import_data($glossary, $glossarycontext, $xmlstring, $user_id) {
        global $CFG, $DB;
        
        // Large exports are likely to take their time and memory.
        require_once($CFG->libdir . "/xmlize.php");

        $xml = xmlize($xmlstring, 0);
        $xmlentries = $xml['GLOSSARY']['#']['INFO'][0]['#']['ENTRIES'][0]['#']['ENTRY'];
        $sizeofxmlentries = is_array($xmlentries) ? count($xmlentries) : 0;
        for($i = 0; $i < $sizeofxmlentries; $i++) {
            // Inserting the entries
            $xmlentry = $xmlentries[$i];
            $newentry = new \stdClass();
            $newentry->concept = trim($xmlentry['#']['CONCEPT'][0]['#']);
            $definition = $xmlentry['#']['DEFINITION'][0]['#'];
            if (!is_string($definition)) {
                print_error('errorparsingxml', 'glossary');
            }
            $newentry->definition = trusttext_strip($definition);
            if ( isset($xmlentry['#']['CASESENSITIVE'][0]['#']) ) {
                $newentry->casesensitive = $xmlentry['#']['CASESENSITIVE'][0]['#'];
            } else {
                $newentry->casesensitive = $CFG->glossary_casesensitive;
            }

            $permissiongranted = 1;
            if ( $newentry->concept and $newentry->definition ) {
                if ( !$glossary->allowduplicatedentries ) {
                    // checking if the entry is valid (checking if it is duplicated when should not be)
                    if ( $newentry->casesensitive ) {
                        $dupentry = $DB->record_exists_select('glossary_entries',
                                        'glossaryid = :glossaryid AND concept = :concept', array(
                                            'glossaryid' => $glossary->id,
                                            'concept'    => $newentry->concept));
                    } else {
                        $dupentry = $DB->record_exists_select('glossary_entries',
                                        'glossaryid = :glossaryid AND LOWER(concept) = :concept', array(
                                            'glossaryid' => $glossary->id,
                                            'concept'    => \core_text::strtolower($newentry->concept)));
                    }
                    if ($dupentry) {
                        $permissiongranted = 0;
                    }
                }
            } else {
                $permissiongranted = 0;
            }
            if ($permissiongranted) {
                $newentry->glossaryid       = $glossary->id;
                $newentry->sourceglossaryid = 0;
                $newentry->approved         = 1;
                $newentry->userid           = $user_id;
                $newentry->teacherentry     = 1;
                $newentry->definitionformat = $xmlentry['#']['FORMAT'][0]['#'];
                $newentry->definitiontrust  = 1;
                $newentry->timecreated      = time();
                $newentry->timemodified     = time();

                // Setting the default values if no values were passed
                if ( isset($xmlentry['#']['USEDYNALINK'][0]['#']) ) {
                    $newentry->usedynalink      = $xmlentry['#']['USEDYNALINK'][0]['#'];
                } else {
                    $newentry->usedynalink      = $CFG->glossary_linkentries;
                }
                if ( isset($xmlentry['#']['FULLMATCH'][0]['#']) ) {
                    $newentry->fullmatch        = $xmlentry['#']['FULLMATCH'][0]['#'];
                } else {
                    $newentry->fullmatch      = $CFG->glossary_fullmatch;
                }

                $newentry->id = $DB->insert_record("glossary_entries",$newentry);

                $xmlaliases = @$xmlentry['#']['ALIASES'][0]['#']['ALIAS']; // ignore missing ALIASES
                $sizeofxmlaliases = is_array($xmlaliases) ? count($xmlaliases) : 0;
                for($k = 0; $k < $sizeofxmlaliases; $k++) {
                /// Importing aliases
                    $xmlalias = $xmlaliases[$k];
                    $aliasname = $xmlalias['#']['NAME'][0]['#'];

                    if (!empty($aliasname)) {
                        $newalias = new \stdClass();
                        $newalias->entryid = $newentry->id;
                        $newalias->alias = trim($aliasname);
                        $newalias->id = $DB->insert_record("glossary_alias",$newalias);
                    }
                }

                // If the categories must be imported...
                $xmlcats = @$xmlentry['#']['CATEGORIES'][0]['#']['CATEGORY']; // ignore missing CATEGORIES
                $sizeofxmlcats = is_array($xmlcats) ? count($xmlcats) : 0;
                for($k = 0; $k < $sizeofxmlcats; $k++) {
                    $xmlcat = $xmlcats[$k];

                    $newcat = new \stdClass();
                    $newcat->name = $xmlcat['#']['NAME'][0]['#'];
                    $newcat->usedynalink = $xmlcat['#']['USEDYNALINK'][0]['#'];
                    if ( !$category = $DB->get_record("glossary_categories", array("glossaryid"=>$glossary->id,"name"=>$newcat->name))) {
                        // Create the category if it does not exist
                        $category = new \stdClass();
                        $category->name = $newcat->name;
                        $category->glossaryid = $glossary->id;
                        $category->id = $DB->insert_record("glossary_categories",$category);
                    }
                    if ( $category ) {
                        // inserting the new relation
                        $entrycat = new \stdClass();
                        $entrycat->entryid    = $newentry->id;
                        $entrycat->categoryid = $category->id;
                        $DB->insert_record("glossary_entries_categories",$entrycat);
                    }
                }

                // Import files embedded in the entry text.
                glossary_xml_import_files($xmlentry['#'], 'ENTRYFILES', $glossarycontext->id, 'entry', $newentry->id);

                // Import files attached to the entry.
                if (glossary_xml_import_files($xmlentry['#'], 'ATTACHMENTFILES', $glossarycontext->id, 'attachment', $newentry->id)) {
                    $DB->update_record("glossary_entries", array('id' => $newentry->id, 'attachment' => '1'));
                }

                // Import tags associated with the entry.
                if (\core_tag_tag::is_enabled('mod_glossary', 'glossary_entries')) {
                    $xmltags = @$xmlentry['#']['TAGS'][0]['#']['TAG']; // Ignore missing TAGS.
                    $sizeofxmltags = is_array($xmltags) ? count($xmltags) : 0;
                    for ($k = 0; $k < $sizeofxmltags; $k++) {
                        // Importing tags.
                        $tag = $xmltags[$k]['#'];
                        if (!empty($tag)) {
                            \core_tag_tag::add_item_tag('mod_glossary', 'glossary_entries', $newentry->id, $glossarycontext, $tag);
                        }
                    }
                }
            }
        }

        // Reset caches.
        \mod_glossary\local\concept_cache::reset_glossary($glossary);
    }
}