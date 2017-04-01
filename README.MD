# ABOUT

This repository is based on [Moodle main repository](https://github.com/moodle/moodle) MOODLE_31_STABLE branch and it integrates several third party and custom plugins.

# DEVELOPMENT
**OUR NEW BIBLE**: https://medium.com/@porteneuve/mastering-git-subtrees-943d29a798ec

## STRUCTURE
There are several remotes that corresponds to repositories containing third-party, custom or customized core plugins. Content of these repositories (from specified branch) corresponds to content of specific folder, list of remotes with their path, repository and branch is listed in following tables:

### CORE
Remote | Repository | Branch | Path
------ | ---------- | ------ | ----
moodle |  [Link](https://github.com/moodle/moodle) | MOODLE_31_STABLE | root directory

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

## ADDING THIRD-PARTY MODULE
When we want to add third party plugin to the project, we need to do few important steps. First we need to create new remote that references to repository with that plugin. For this we use command ```git remote add <remote-name> <remote-url>```, remote name is local for you, but it is strongly recommended to use same remote name as mentioned above. There is simple rule for creating new remote name, it coresponds to hyphenated path to plugin working directory inside the project. For example then we have module atto_helixatto, which corresponds to repository located on https://github.com/streamingltd/MEDIAL-Moodle-ATTO.git and its working directory in moodle is lib/editor/atto/plugins/helixatto we use name lib-editor-atto-plugins-helixatto for the remote, so we will use following command to add remote:

```shell
git remote add lib-editor-atto-plugins-helixatto https://github.com/streamingltd/MEDIAL-Moodle-ATTO.git
```

Next thing we need to do is to fetch the repository -- that means load all the changes and commit informations to our local repository. This can take some time. We use  ```git fetch <remote-name>``` command for this, so for latter plugin it would be following:

```shell
git fetch lib-editor-atto-plugins-helixatto
```

After we have those changes, we need to add the remote repository content as a part of our main repository into correct working directory. Command we can use for this is following ```git read-tree --prefix=<working-directory-path> -u <remote-name>/<remote-branch-name>```. It is important to set correct working directory, otherwise plugin won't work correctly, also be careful about selecting branch. Usually we select master branch, but since we are using moodle's MOODLE_31_STABLE branch, we should also choose MOODLE 3.1 version corresponding branch if there is such. In our example with atto_helixatto, there is only one branch, master, so we choose this one as is shown in following code:

```shell
git read-tree --prefix=lib/editor/atto/plugins/helixatto -u lib-editor-atto-plugins-helixatto/master
```

In this moment, in our local repository, we have third-party plugin incorporated in our main repository, so now we just need to commit and push changes to our origin, for example like this:

```shell
git commit -m "Added helixatto, atto editor plugin"
git push
```

## EXTRACTING PLUGIN FROM CORE
Situation is a bit more complicated when we want to extract plugin, which is part of original Moodle repository, so it is "core plugin",  but there are some similarities. First we need to create new repository for the plugin. Naming the repository should follow the same principle as naming the remotes with only one difference, we add elf- prefix to make it clear, that this is elf specific variant of module, possibly we could add elf-core- prefix to distinct modified core modules from full custom modules.

When we have a repository we can start with extraction process. To make this clean we will use special temporary branch for this. First make sure you have commited and pushed all changes and there are no uncommited changes on your current branch, you can use ```git status``` for this. Then you can creat new branch and check it out, we can use one command for this -- ```git checkout -b <new-branch-name>```. This will create and checkout new branch, to make things clear, it is recommend to call branch something like split or extraction followed by hyphenated path to plugin working directory, for example when we want to extract folder module, we could use split-mod-folder or extraction-mod-folder name for branch. Let's say we choose the first one, so we will use following command:

```shell
git checkout -b split-mod-folder
```

So we have our special migration branch, now we can filter repository only to plugin working directory and also filter commits to those which involves this directory, we can use ```git filter-branch --subdirectory <working-directory-path>``` command for both of this. In our case, the command would look like this:

```shell
git filter-branch --subdirectory mod/folder
```

Now we have files and commits we want to migrate, so we need to add remote referencing to repository we created at the beggining, again using ```git remote add <remote-name> <remote-url>``` command. So in our case, assuming that we called the repository elf-mod-folder and that it is located on following url: https://github.com/ArtsFacultyMU/elf-mod-folder.git, command would look like this:

```shell
git remote add mod-folder https://github.com/ArtsFacultyMU/elf-mod-folder.git
```

Last thing we need to do is to push filtered content and commit to this remote, more precisely to its specific branch, structure of command for this is following: ```git push -u <remote-name> <local-branch>:<remote-branch>```, where local branch is the branch we push from, so our current splitting branch (in our case split-mod-folder), and the remote branch is the branch we commit to located on remote repository defined by remote name. For initial commit we usually use the master branch, so the final command will look like this:

```shell
git push -u mod-folder split-mod-folder:master
```

In this moment we have our core plugin extracted and we can work with it similarly to third-party plugins, except we have to maintain plugin repository on our own. Now we can get back to our original branch, let's say master and optionally we can delete the migration branch. To switch back to other branch we can use ```git checkout <branch-name>``` command, for deletion of the migration branch we can use ```git branch -d <branch-name>```, in case that git doesn't allow you to delete it this way (for example saying, that not all changes were merged from the branch), you can force it by ```git branch -D <branch-name>```. So in our case switching back and cleaning would look like this (we will use force delete immediately to make sure everything will go as we want):

```shell
git checkout master
git branch -D split-mod-folder
```

## CREATING CUSTOM PLUGIN

TODO

## KEEPING THIRD-PARTY PLUGIN UP TO DATE

Updating your third party plugin is simple (assuming we have the remote initialized). First you have to fetch it to make sure, you have the most recent state info, so let's say we are updating our helixatto plugin:

```shell
git fetch lib-editor-atto-plugins-helixatto
```

When we have the most recent info we simply merge the branch we want to module working directory. There are actually two options to do it. First one is using git subtree merging strategy. This is the prefered one, since it is faster. We can use ```git merge -s subtree --squash <remote-name>/<remote-branch-name>``` command to achieve this. Usually we will use same branch as we used originally, but in some cases (like update to new major) we can use other. As you can see, in this command path to working directory isn't mention, because git heuristics should be able to find it out. 

If it won't, this method will fail, but don't worry you can just reset the merge ```git reset --merge ORIG_HEAD``` and use the second method. Second method is explicit, but a bit slower and you have to specify the path here. It looks like this: ```git merge -X subtree=<working-directory-path> --squash <remote-name>/<remote-branch-name>```, so to sum it up, here is what we would do in our case:

```shell
git merge -s subtree --squash lib-editor-atto-plugins-helixatto/master
# if it fails
git reset --merge ORIG_HEAD
git merge -X subtree=lib/editor/atto/plugins/helixatto --squash lib-editor-atto-plugins-helixatto/master
```
Assuming we are not modifying this plugin, there should be no conflicts (case when we want to modify third-party plugin will be described later), so we can simply commit and push changes with following:

```shell
 git commit -m "Updated the helixatto atto editor plugin"
 git push
```

## CUSTOMIZING THIRD-PARTY PLUGIN

TODO

## KEEPING CUSTOMIZED THIRD-PARTY OR CUSTOM PLUGIN UP TO DATE

TODO

## KEEPING CUSTOMIZED CORE PLUGIN UP TO DATE

TODO

## KEEPING CORE MOODLE UP TO DATE

TODO

## MODIFYING PLUGIN FROM MAIN REPOSITORY

TODO

