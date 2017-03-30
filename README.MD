# ABOUT

This repository is based on [Moodle main repository](https://github.com/moodle/moodle) MOODLE_31_STABLE branch and it integrates several third party and custom plugins.

# DEVELOPMENT
**OUR NEW BIBLE**: https://medium.com/@porteneuve/mastering-git-subtrees-943d29a798ec

## STRUCTURE
There are several remotes that corresponds to repositories containing third-party, custom or customized core plugins. Content of these repositories (from specified branch) corresponds to content of specific folder, list of remotes with their path, repository and branch is listed in following tables:

### THIRD-PARTY PLUGINS
Remote | Repository | Branch | Path
------ | ---------- | ------ | ----
mod-hotpot | [Link](https://github.com/gbateson/moodle-mod_hotpot.git) | master | mod/hotpot
mod-questionnaire | [Link](https://github.com/remotelearner/moodle-mod_questionnaire.git) | MOODLE_31_STABLE | mod/questionnaire
mod-tab | [Link](https://github.com/oohoo/moodle-mod_tab.git) | master | mod/tab
mod-attendance | [Link](https://github.com/danmarsden/moodle-mod_attendance.git) | MOODLE_31_STABLE | mod/attendance
mod-choicegroup | [Link](https://github.com/ndunand/moodle-mod_choicegroup.git) | master | mod/choicegroup
mod-helixmedia | [Link](https://github.com/streamingltd/MEDIAL-Moodle-Activity.git) | master | mod/helixmedia
mod-assign-submission-helixassign | [Link](https://github.com/streamingltd/MEDIAL-Moodle-Submission.git) | master | mod/assign/submission/helixassign
course-format-grid | [Link](https://github.com/gjb2048/moodle-format_grid.git) | MOODLE_31 | course/format/grid
plagiarism-urkund | [Link](https://github.com/danmarsden/moodle-plagiarism_urkund.git) | master | plagiarism/urkund
blocks-progress | [Link](https://github.com/deraadt/Moodle-block_progress.git) | master | blocks/progress
report-customsql | [Link](https://github.com/moodleou/moodle-report_customsql.git) | master | report/customsql
lib-editor-atto-plugins-hr | [Link](https://github.com/damyon/moodle-atto_hr.git) | master | lib/editor/atto/plugins/hr
lib-editor-atto-plugins-fontfamily | [Link](https://github.com/projectestac/moodle-atto_fontfamily.git) | master | lib/editor/atto/plugins/fontfamily
lib-editor-atto-plugins-fontsize | [Link](https://github.com/andrewnicols/moodle-atto_fontsize.git) | master | lib/editor/atto/plugins/fontsize
lib-editor-atto-plugins-helixatto | [Link](https://github.com/streamingltd/MEDIAL-Moodle-ATTO.git) | master | lib/editor/atto/plugins/helixatto
lib-editor-tinymce-plugins-helixmedia | [Link](https://github.com/streamingltd/MEDIAL-Moodle-TinyMCE.git) | master | lib/editor/tinymce/plugins/helixmedia

### CUSTOMIZED CORE PLUGINS
Remote | Repository | Branch | Path
------ | ---------- | ------ | ----
mod-chat | [Link](https://github.com/ArtsFacultyMU/elf-mod-chat.git) | master (but based on MOODLE_31_STABLE fork) | mod/chat
mod-folder | [Link](https://github.com/ArtsFacultyMU/elf-mod-folder.git) | master (but based on MOODLE_31_STABLE fork) | mod/folder

### CUSTOM PLUGINS
Remote | Repository | Branch | Path
------ | ---------- | ------ | ----

## INITIALIZATION
To initial connection with all connected repositories (by creating remotes on your local repository) run following:
```shell
# Third-party moduly
git remote add mod-hotpot https://github.com/gbateson/moodle-mod_hotpot.git
git remote add mod-questionnaire https://github.com/remotelearner/moodle-mod_questionnaire.git
git remote add mod-tab https://github.com/oohoo/moodle-mod_tab.git
git remote add mod-attendance https://github.com/danmarsden/moodle-mod_attendance.git
git remote add mod-choicegroup https://github.com/ndunand/moodle-mod_choicegroup.git
git remote add mod-helixmedia https://github.com/streamingltd/MEDIAL-Moodle-Activity.git
git remote add mod-assign-submission-helixassign https://github.com/streamingltd/MEDIAL-Moodle-Submission.git
git remote add course-format-grid https://github.com/gjb2048/moodle-format_grid.git
git remote add plagiarism-urkund https://github.com/danmarsden/moodle-plagiarism_urkund.git
git remote add blocks-progress https://github.com/deraadt/Moodle-block_progress.git
git remote add report-customsql https://github.com/moodleou/moodle-report_customsql.git
git remote add lib-editor-atto-plugins-hr https://github.com/damyon/moodle-atto_hr.git
git remote add lib-editor-atto-plugins-fontfamily https://github.com/projectestac/moodle-atto_fontfamily.git
git remote add lib-editor-atto-plugins-fontsize https://github.com/andrewnicols/moodle-atto_fontsize.git
git remote add lib-editor-atto-plugins-helixatto https://github.com/streamingltd/MEDIAL-Moodle-ATTO.git
git remote add lib-editor-tinymce-plugins-helixmedia https://github.com/streamingltd/MEDIAL-Moodle-TinyMCE.git
# Core moduly
git remote add mod-chat https://github.com/ArtsFacultyMU/elf-mod-chat.git
git remote add mod-folder https://github.com/ArtsFacultyMU/elf-mod-folder.git
# načtení nově vytvořených remote branches
git fetch --all
```

