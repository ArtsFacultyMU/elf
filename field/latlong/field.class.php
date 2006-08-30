<?php 
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-onwards Moodle Pty Ltd  http://moodle.com          //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

class data_field_latlong extends data_field_base {

    var $type = 'latlong';
    
    // This is an array of URL schemes for linking out to services, using the float values of lat and long.
    // In each scheme, the special markers @lat@ and @long@ will be replaced by the float values.
    // The config options for the field store each service name that should be displayed, in a comma-separated
    // field. Therefore please DO NOT include commas in the service names if you are adding extra services.
    var $linkoutservices = array(
          "Google Maps" => "http://maps.google.com/maps?q=@lat@,+@long@&iwloc=A&hl=en",
          "Google Earth" => "@wwwroot@/mod/data/field/latlong/kml.php?d=@dataid@&fieldid=@fieldid@&rid=@recordid@",
          "Geabios" => "http://www.geabios.com/html/services/maps/PublicMap.htm?lat=@lat@&lon=@long@&fov=0.3&title=Moodle%20data%20item",
          "OpenStreetMap" => "http://www.openstreetmap.org/index.html?lat=@lat@&lon=@long@&zoom=11",
          "Multimap" => "http://www.multimap.com/map/browse.cgi?scale=200000&lon=@long@&lat=@lat@&icon=x"
          );
    // Other map sources listed at http://kvaleberg.com/extensions/mapsources/index.php?params=51_30.4167_N_0_7.65_W_region:earth



    // Parameter data used:
    // "param1" is a comma-separated list of the linkout service names that are enabled for this instance
    // "param2" indicates the label that will be used in generating Google Earth KML files: -1 for item #, -2 for lat/long, positive number for the (text) field to use.



    function data_field_latlong($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }

    function display_add_field($recordid=0){
        global $CFG;

        $lat = '';
        $long = '';

        if ($recordid){
            if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
                $lat  = $content->content;
                $long = $content->content1;
            }
        }

        $str = '<div title="'.$this->field->description.'">';
		$str .= '<table><tr><td align="right">';
		$str .= '<label for="field_'.$this->field->id.'_0">' . get_string('latitude', 'data') . '</label>:</td><td><input type="text" name="field_'.$this->field->id.'_0" id="field_'.$this->field->id.'_0" value="'.$lat.'" size="10" />°N</td></tr>';
		$str .= '<tr><td align="right"><label for="field_'.$this->field->id.'_1">' . get_string('longitude', 'data') . '</label>:</td><td><input type="text" name="field_'.$this->field->id.'_1" id="field_'.$this->field->id.'_1" value="'.$long.'" size="10" />°E</td></tr>';
		$str .= '</table>';
        $str .= '</div>';
        
        return $str;
    }

    function display_browse_field($recordid, $template) {
        global $CFG;
        if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)){
            $lat = empty($content->content)? '':$content->content;
            $long = empty($content->content1)? '':$content->content1;

            if (empty($lat) or empty($long)) {
                return '';
            }
            
		  if($lat < 0) {
		      $compasslat = "" . sprintf('%01.4f', 0 - $lat) . '°S';
	       } else {
		      $compasslat = "" . sprintf('%01.4f', $lat) . "°N";
		  }
		  if($long < 0) {
		      $compasslong = "" . sprintf('%01.4f', 0 - $long) . '°W';
	       } else {
		      $compasslong = "" . sprintf('%01.4f', $long) . "°E";
		  }
		  
            $str = '<form style="display:inline;">';
            $str.= "$compasslat, $compasslong";
            
            
            // Now let's create the jump-to-services link
            $servicesshown = explode(',', $this->field->param1);

            // These are the different things that can be magically inserted into URL schemes
            $urlreplacements = array(
                '@lat@'=> $lat,
                '@long@'=> $long,
                '@wwwroot@'=> $CFG->wwwroot,
                '@contentid@'=> $content->id,
                '@dataid@'=> $this->data->id,
                '@courseid@'=> $this->data->course,
                '@fieldid@'=> $content->fieldid,
                '@recordid@'=> $content->recordid,
                );

            if(sizeof($servicesshown)==1 && $servicesshown[0]) {
                $str .= " <a href='"
                          . str_replace(array_keys($urlreplacements), array_values($urlreplacements), $this->linkoutservices[$servicesshown[0]])
                          ."'>$servicesshown[0]</a> ";
            } elseif (sizeof($servicesshown)>1) {
				$str .= "\n<select name='jumpto'>";
	
				foreach($servicesshown as $servicename){
					// Add a link to a service
					$str .= "\n  <option value='"
							   . str_replace(array_keys($urlreplacements), array_values($urlreplacements), $this->linkoutservices[$servicename])
							   . "'>".htmlspecialchars($servicename)."</option>";
				}
				// NB! If you are editing this, make sure you don't break the javascript reference "previousSibling"
				//   which allows the "Go" button to refer to the drop-down selector.
				$str .= "\n</select><input type='button' value='" . get_string('go') . "' onclick='if(previousSibling.value){self.location=previousSibling.value}'/>";
            }
            
            $str.= '</form>';
            
            return $str;
        }
        return false;
    }

    function update_content($recordid, $value, $name='') {
        $content = new object;
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;

        $names = explode('_', $name);
        switch ($names[2]){
            case 0:    // update lat
                $content->content = (float)$value;
                break;
            case 1:    // update long
                $content->content1 = (float)$value;
                break;
            default:
                break;
        }
        
        if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            return update_record('data_content', $content);
        } else {
            return insert_record('data_content', $content);
        }
    }

    function get_sort_sql($fieldname) {
        global $CFG;

        switch ($CFG->dbtype) {
            case 'mysql':   // string in an arithmetic operation is converted to a floating-point number
                return '('.$fieldname.'+0.0)';  

            default:
                return 'CAST('.$fieldname.' AS REAL)';  
        }
    }
}

?>