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
 * Javascript helper function for Folder module
 *
 * @package    mod
 * @subpackage folder
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_folder = {};

M.mod_folder.init_tree = function(Y, id, expand_all, display) {
	Y.use('yui2-treeview', function(Y) {
		var tree = new Y.YUI2.widget.TreeView(id);

		tree.subscribe("clickEvent", function(node, event){
		var file_links = Y.all('.mod-folder-filename a');

		file_links.on('click', enableDownload);

//	  console.log(node.node.srcElement);
			if(display == 1) {
//    we want normal clicking which redirects to url
				return false;
			} else {
//    var image_src = node.node.html.match("src=\"(.*)\\?")[1];

	var gallery_list = Y.all('.fp-thumbnail');

	gallery_list.on('click', handleBoxClick);

	return true}});

if(expand_all){tree.expandAll()}else tree.getRoot().children[0].expand();

tree.render()})}

var handleBoxClick=function(e){

    var picture_div = document.getElementById("picture_here");
	var clicked = "clicked";

	if (e.currentTarget.one('img').getAttribute('class') == "smallicon")
	{
		clicked = "smallicon";
	} else {
		clicked = e.currentTarget.one('img').getAttribute('src').match("(.*)\\?")[1];
	}

		var display = "class = 'folder-mod-a-visible'";
		var i = this.size();
		var text = "";
		var img_src = "";
	
		for (var i = 0; i < this.size(); i++) { 
			img_src = "img_src";
			var this_img_class = this.item(i).one('img').getAttribute('class')
			if(this_img_class != "smallicon") {
				img_src = this.item(i).one('img').getAttribute('src').match("(.*)\\?");
				if(img_src != null)
					img_src = img_src[1];
			}
			if (img_src == clicked) { 
				display = "class = 'folder-mod-a-visible'";
			} else {
				display = "class = 'folder-mod-a-hidden'";
			}
			if(this_img_class != "smallicon") {
				text += "<a href='"+img_src+"' data-toggle='lightbox' data-gallery='multiimages'  "+ display +"><img class='mod-folder-image-view' src='"+img_src+"' /></a>";
			}
		}
		picture_div.innerHTML = text;

    //e.currentTarget.setHTML('ouch!');
//    e.currentTarget.setStyle('border','2px solid red');

    };

var enableDownload=function(e){
    console.log(e.currentTarget.getAttribute('href'));
	window.location = e.currentTarget.getAttribute('href');
};

