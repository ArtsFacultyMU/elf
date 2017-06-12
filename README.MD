# ABOUT

This repository is based on [Moodle main repository](https://github.com/moodle/moodle) MOODLE_31_STABLE branch and it integrates several third party and custom plugins.

# DEVELOPMENT
**OUR NEW BIBLE**: https://medium.com/@porteneuve/mastering-git-subtrees-943d29a798ec

**HOW TO MAKE PRIVATE FORK**: https://help.github.com/articles/duplicating-a-repository/

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
mod-tab | [Link](https://github.com/oohoo/moodle-mod_tab.git) | MOODLE_30_STABLE | mod/tab
mod-attendance | [Link](https://github.com/danmarsden/moodle-mod_attendance.git) | MOODLE_31_STABLE | mod/attendance
mod-choicegroup | [Link](https://github.com/ndunand/moodle-mod_choicegroup.git) | master | mod/choicegroup
mod-helixmedia | [Link](https://github.com/streamingltd/MEDIAL-Moodle-Activity.git) | master | mod/helixmedia
mod-assign-submission-helixassign | [Link](https://github.com/streamingltd/MEDIAL-Moodle-Submission.git) | master | mod/assign/submission/helixassign
mod-assign-submission-onlineaudio | [Link](https://github.com/pauln/moodle-assignsubmission_onlineaudio) | master | mod/assign/submission/onlineaudio
question-format-hotpot | [Link](https://github.com/gbateson/moodle-qformat_hotpot) | master | question/format/hotpot
course-format-grid | [Link](https://github.com/holajric/moodle-format_grid.git) | MOODLE_31 (customized fork) | course/format/grid
plagiarism-urkund | [Link](https://github.com/danmarsden/moodle-plagiarism_urkund.git) | master (last compatible https://github.com/danmarsden/moodle-plagiarism_urkund/commit/2dfc34d482cacd36733aa2f3a2fcc547df4c836c, possibly change to MOODLE_32_STABLE) | plagiarism/urkund
blocks-progress | [Link](https://github.com/deraadt/Moodle-block_progress.git) | master | blocks/progress
report-customsql | [Link](https://github.com/moodleou/moodle-report_customsql.git) | master | report/customsql
lib-editor-atto-plugins-hr | [Link](https://github.com/damyon/moodle-atto_hr.git) | master | lib/editor/atto/plugins/hr
lib-editor-atto-plugins-fontfamily | [Link](https://github.com/projectestac/moodle-atto_fontfamily.git) | master | lib/editor/atto/plugins/fontfamily
lib-editor-atto-plugins-fontsize | [Link](https://github.com/andrewnicols/moodle-atto_fontsize.git) | master | lib/editor/atto/plugins/fontsize
lib-editor-atto-plugins-helixatto | [Link](https://github.com/streamingltd/MEDIAL-Moodle-ATTO.git) | master | lib/editor/atto/plugins/helixatto
lib-editor-atto-plugins-morefontcolors | [Link](https://github.com/ndunand/moodle-atto_morefontcolors.git) | master | lib/editor/atto/plugins/morefontcolors
lib-editor-atto-plugins-htmlplus | [Link](https://github.com/andrewnicols/moodle-atto_htmlplus.git) | master | lib/editor/atto/plugins/htmlplus
lib-editor-atto-plugins-fullscreen | [Link](https://github.com/dthies/moodle-atto_fullscreen.git) | master | lib/editor/atto/plugins/fullscreen
lib-editor-tinymce-plugins-helixmedia | [Link](https://github.com/streamingltd/MEDIAL-Moodle-TinyMCE.git) | master | lib/editor/tinymce/plugins/helixmedia
repository-recordaudio | [Link](https://github.com/pauln/moodle-repository_recordaudio) | MOODLE_23_STABLE (newer than master...) | repository/recordaudio

### CUSTOMIZED CORE PLUGINS
Remote | Repository | Branch | Path
------ | ---------- | ------ | ----
mod-chat | [Link](https://github.com/ArtsFacultyMU/elf-mod-chat.git) | master (but based on MOODLE_31_STABLE fork) | mod/chat
mod-folder | [Link](https://github.com/ArtsFacultyMU/elf-mod-folder.git) | master (but based on MOODLE_31_STABLE fork) | mod/folder
mod-data | [Link](https://github.com/ArtsFacultyMU/elf-mod-data.git) | master (but based on MOODLE_31_STABLE fork) | mod/data
mod-assign-feedback-editpdf | [Link](https://github.com/ArtsFacultyMU/elf-mod-assign-feedback-editpdf.git) | master (but based on MOODLE_31_STABLE fork) | mod/assign/feedback/edifpdf
question-type-multianswer | [Link](https://github.com/ArtsFacultyMU/elf-question-type-multianswer.git) | master (but based on MOODLE_31_STABLE fork) | question/type/multianswer

### CUSTOM PLUGINS
Remote | Repository | Branch | Path
------ | ---------- | ------ | ----
theme-elf_bs | [Link](https://github.com/ArtsFacultyMU/elf-theme-elf_bs.git) | master | theme/elf_bs
mod-newassignment | [Link](https://github.com/ArtsFacultyMU/elf-mod-newassignment.git) | master | mod/newassignment
blocks-is_links | [Link](https://github.com/ArtsFacultyMU/elf-blocks-is_links.git) | master | blocks/is_links
blocks-copyblok | [Link](https://github.com/ArtsFacultyMU/elf-blocks-copyblok.git) | master | blocks/copyblok
enrol-ismu | [Link](https://github.com/ArtsFacultyMU/elf-enrol-ismu.git) | master | enrol/ismu
local-elf | [Link](https://github.com/ArtsFacultyMU/elf-local.git) | master | local

## INITIALIZATION
To initial connection with all connected repositories (by creating remotes on your local repository) run following:

```shell
# Moodle core
git remote add moodle https://github.com/moodle/moodle.git
# Third-party moduly
git remote add mod-hotpot https://github.com/gbateson/moodle-mod_hotpot.git
git remote add mod-questionnaire https://github.com/remotelearner/moodle-mod_questionnaire.git
git remote add mod-tab https://github.com/oohoo/moodle-mod_tab.git
git remote add mod-attendance https://github.com/danmarsden/moodle-mod_attendance.git
git remote add mod-choicegroup https://github.com/ndunand/moodle-mod_choicegroup.git
git remote add mod-helixmedia https://github.com/streamingltd/MEDIAL-Moodle-Activity.git
git remote add mod-assign-submission-helixassign https://github.com/streamingltd/MEDIAL-Moodle-Submission.git
git remote add mod-assign-submission-onlineaudio https://github.com/pauln/moodle-assignsubmission_onlineaudio.git
git remote add question-format-hotpot https://github.com/gbateson/moodle-qformat_hotpot.git
git remote add course-format-grid https://github.com/holajric/moodle-format_grid.git
git remote add plagiarism-urkund https://github.com/danmarsden/moodle-plagiarism_urkund.git
git remote add blocks-progress https://github.com/deraadt/Moodle-block_progress.git
git remote add report-customsql https://github.com/moodleou/moodle-report_customsql.git
git remote add lib-editor-atto-plugins-hr https://github.com/damyon/moodle-atto_hr.git
git remote add lib-editor-atto-plugins-fontfamily https://github.com/projectestac/moodle-atto_fontfamily.git
git remote add lib-editor-atto-plugins-fontsize https://github.com/andrewnicols/moodle-atto_fontsize.git
git remote add lib-editor-atto-plugins-helixatto https://github.com/streamingltd/MEDIAL-Moodle-ATTO.git
git remote add lib-editor-atto-plugins-morefontcolors https://github.com/ndunand/moodle-atto_morefontcolors.git
git remote add lib-editor-atto-plugins-htmlplus https://github.com/andrewnicols/moodle-atto_htmlplus.git
git remote add lib-editor-atto-plugins-fullscreen https://github.com/dthies/moodle-atto_fullscreen.git
git remote add lib-editor-tinymce-plugins-helixmedia https://github.com/streamingltd/MEDIAL-Moodle-TinyMCE.git
git remote add repository-recordaudio https://github.com/pauln/moodle-repository_recordaudio.git
# Core moduly
git remote add mod-chat https://github.com/ArtsFacultyMU/elf-mod-chat.git
git remote add mod-folder https://github.com/ArtsFacultyMU/elf-mod-folder.git
git remote add mod-data https://github.com/ArtsFacultyMU/elf-mod-data.git
git remote add mod-assign-feedback-editpdf https://github.com/ArtsFacultyMU/elf-mod-assign-feedback-editpdf.git
git remote add question-type-multianswer https://github.com/ArtsFacultyMU/elf-question-type-multianswer.git
# Custom moduly
git remote add theme-elf_bs https://github.com/ArtsFacultyMU/elf-theme-elf_bs.git
git remote add mod-newassignment https://github.com/ArtsFacultyMU/elf-mod-newassignment.git
git remote add blocks-is_links https://github.com/ArtsFacultyMU/elf-blocks-is_links.git
git remote add blocks-copyblok https://github.com/ArtsFacultyMU/elf-blocks-copyblok.git
git remote add enrol-ismu https://github.com/ArtsFacultyMU/elf-enrol-ismu.git
git remote add local-elf https://github.com/ArtsFacultyMU/elf-local.git
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
git filter-branch --subdirectory-filter mod/folder
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

Creating custom plugin is actually really easy (in terms of versioning), all you have to do is to create new repository, let's say we want to create custom template, in our case it is called elf_bs. We will use the same naming convention as usual, so our repository will be called elf-theme-elf_bs. Then we create folder in our local machine, for our local repository, let's call it same as our repository, so elf-theme-elf_bs, in this folder we will initialize git with command ```git init```. After this if we are creating plugin from scratch, we will copy some basic files for our plugin, if we already have plugin, we will copy content of coresponding folder, in our case theme/elf_bs into our local repository folder. Now we can commit changes with ```git add .``` and ```git commit -am "Theme elf_bs initial commit"```.

Next thing we have to do is to connect our local repository with our remote repository, let's say it has url https://github.com/ArtsFacultyMU/elf-theme-elf_bs.git, we will do this by adding remote with ```git remote add origin https://github.com/ArtsFacultyMU/elf-theme-elf_bs.git```, then we just have to push the changes to repository with ```git push -u origin master```. That's it we have repository with our custom plugin, but in our case that's just the first part. 

We also need to connect it with our main repository, we will do this same way as with third-party plugins, so let's go to our local folder with main repository. There we have to add new remote referencing to our plugin repository and fetching it, in our case:

```shell
git remote add theme-elf_bs https://github.com/ArtsFacultyMU/elf-theme-elf_bs.git
git fetch theme-elf_bs
```

Next step is to add content of plugin repository to corresponding folder with ```git read-tree --prefix=<working-directory-path> -u <remote-name>/<remote-branch-name>``` command, as we have seen in part describing integration of third-party plugins. In our case we have only master branch, but we could definitely have multiple branches, but let's keep it simple and say, that we have only master branch, in this case command will take following form:

```shell
git read-tree --prefix=theme/elf_bs -u theme-elf_bs/master
```

And we are almost there, all we need to do now is to commit and push changes to main repository and we have sucessfully integrated our custom plugin.

```shell
git commit -m "Added elf_bs theme"
git push
```

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

First thing you have to do, when you want to customize some third-party plugin is to fork the original repository on github. It is recommended to use same naming strategy as with customized core plugins, so elf- prefix plus hyphenated working directory path. Let's say we are customizing mod-attendance, so we will fork it's repository and the new repository will be called elf-mod-attendance.

When we have forked repository, we have to say our main elf repository to use the fork as a remote instead of original repository, we can use ```git remote set-url <remote-name> <remote-url>``` command for this and then we will fetch the remote for updates. So we will set-url for remote referencing to original repository and replace it with url of our fork, like this (assuming url of our fork is https://github.com/ArtsFacultyMU/elf-mod-attendance.git) :

```shell
 git remote set-url mod-attendance https://github.com/ArtsFacultyMU/elf-mod-attendance.git
 git fetch mod-attendance
```

Configuration is done for now, so what to do when we want to actually modify plugin through our fork? We have to clone the repository with ```git clone https://github.com/ArtsFacultyMU/elf-mod-attendance.git``` and in our local repository make changes. Then we commit and push them as usual. Now we have modified the plugin repository only, but how to synchronize main repository with those changes? Actually it is same principle as mentioned earlier. We have to go to our local repository with main elf repository, fetch remote, merge changes, commit and push. So use the following (assuming, that in fork we are working on same branch as with original repository, thus MOODLE_31_STABLE):

```shell
git fetch mod-attendance
git merge -s subtree --squash mod-attendance/MOODLE_31_STABLE
# if it fails START 
git reset --merge ORIG_HEAD
git merge -X subtree=mod/attendance --squash mod-attendance/MOODLE_31_STABLE
# if it fails END
git commit -m "Updated the mod attendance plugin"
git push
```

There is also possibility to make changes directly in new repository and then synchronizing plugin repository, but we will talk about this one later. Right now, we know how to make changes to customized third-party plugin and how to reflect them in main repository, but now since we have created fork and thus lost the synchronization with original plugin we need to keep our fork up to date. Let's se how to do that now.

## KEEPING CUSTOMIZED THIRD-PARTY PLUGIN UP TO DATE

To make things easier and avoid unnecessary merging problems we will merge the original plugin repository changes into our fork and not into main project. We can do this quite an easy way. First thing we need to do is to get into our local fork repository and create remote referencing to original repository, it is usually called upstream. We already now the way, so in case of our attendance plugin, we will just use ```git remote add upstream https://github.com/danmarsden/moodle-mod_attendance.git```. Now we have the connection, so as usual, let's fetch the changes with ```git fetch upstream```. We also need to make sure, that we are on the branch, that we want to update, so to make sure let's checkout that branch (in our case MOODLE_31_STABLE) with ```git checkout MOODLE_31_STABLE``` and now we can finally merge with ```git merge upstream/<branch-to-merge>```, so ```git merge upstream/MOODLE_31_STABLE``` and that's it, possibly we will have to resolve some conflicts, but there is no way to avoid that. Now we only have to commit and push changes to our plugin repository and after that also merge our plugin repository to main repository the way it was described in previous part.

## WORKING WITH CUSTOMIZED CORE PLUGIN AND KEEPING IT UP TO DATE

Approach to customized core plugins is very similar to customized third-party plugin approach. You simply clone the repository, make changes, commit and push them and then you merge it to main repository. So let's say we are working with mod-folder core plugin, we would use following commands to merge plugin repo with the main one (assuming repo has only master branch):

```shell
git fetch mod-folder
git merge -s subtree --squash mod-folder/master
# if it fails START 
git reset --merge ORIG_HEAD
git merge -X subtree=mod/folder --squash mod-folder/master
# if it fails END
git commit -m "Updated the mod folder plugin"
git push
```

As you can see, this part is basically same as with third-party plugin, but what about synchronization with original repo. First we need to clone our modified repository for example with ```git clone https://github.com/ArtsFacultyMU/elf-mod-folder.git``` and navigate to created folder let's say ```cd elf-mod-folder```. We will have to slightly modify our approach, since core plugins doesn't have separate original repository and they are extracted from original moodle repo, our upstream branch will be actually referencing to moodle repository. So first we need to create our upstream remote with ```git remote add upstream https://github.com/moodle/moodle.git```, also don't forget to fetch it with ```git fetch upstream```. So far, so good, but now we have to combine extraction and merging approach. First we checkout to branch we want to merge with, in our case MOODLE_31_STABLE, so let's use ```git checkout upstream/MOODLE_31_STABLE```.

Now we need to create new temporary branch for merging, let's call it merge-upstream, with ```git checkout -b merge-upstream```, after this we have to filter this branch to our plugin folder (same way we did during extraction) with ```git filter-branch --subdirectory-filter mod/folder```. This can take some time, but we will end up with branch containing only content of plugin working directory in latest version commited to branch we selected, which is exactly what we want to merge with our repository.

So the last step is to checkout back to master with ```git checkout master``` and merge our temporary branch with master simply with ```git merge merge-upstream```. Now just resolve merge conflicts if there are any, commit, push and clean the mess (temporary branch) with ```git branch -D merge-upstream```, also we need to merge our changes with main repository, as mentioned before, we will just use following (not in plugin local repository, but in main elf local repository):

```shell
git fetch mod-folder
git merge -s subtree --squash mod-folder/master
# if it fails START 
git reset --merge ORIG_HEAD
git merge -X subtree=mod/folder --squash mod-folder/master
# if it fails END
git commit -m "Updated the mod folder plugin"
git push
```


## WORKING WITH CUSTOM PLUGIN

This one is again the most simple case in our scenario. Since we are the ones responsible for keeping our plugin up to date, it isn't part of core and it isn't fork, so we don't have to synchronize it with any external changes, thus all we need to do is to merge our plugin into our main repository, when we make changes in it and we can be sure, that there won't be any conflicts. We already know the commands, let's assume, that we already have remote branch created in our main repository from previous steps, if we want to update version of our plugin, let's say we are talking about theme-elf_bs, we need to run following commands:

```shell
git fetch theme-elf_bs
git merge -s subtree --squash theme-elf_bs/master
# if it fails START 
git reset --merge ORIG_HEAD
git merge -X subtree=theme/elf_bs --squash theme-elf_bs/master
# if it fails END
git commit -m "Updated the theme elf_bs plugin"
git push
```

And that's all, but don't forget, that one thing is that surely there will be no conflicts, if we are not modifying our plugin from main repository, but it doesn't mean that changes in Moodle doesn't cause us any problems, our plugin will definitely use at least some kind of Moodle functionality and implements some interface and since we are the plugin developers we are the ones who have to maintain the plugin to make it work in the moment when something in Moodle that affects our plugin changes.

## KEEPING CORE MOODLE UP TO DATE

Keeping core moodle up to date is actually situation complementary to keeping core plugins up to date, what we do is that we need to merge all files from origianl repo to our main repo, but we need to omit folders coresponding to our customized core plugins. So, first we need to add remote if there is none (and also fetch), let's call it moodle, with ```git remote add moodle https://github.com/moodle/moodle.git```. But now the thing is, that we don't want to filter to one folder, we want to filter out some folders, in our case it is mod/folder and mod/chat for now. How to do this? We have to ho back to our master branch with ```git checkout master``` and put files and folder we don't want to update into .gitignore, in our case, the commands will look like this:

```shell
echo README.txt >> .gitignore
echo README.MD >> .gitignore
echo mod/folder/** >> .gitignore
echo mod/chat/** >> .gitignore
echo mod/data/** >> .gitignore
echo question/type/multianswer/** >> .gitignore
```

In this moment we won't commit the changes and we will merge our master with appropriate moodle branch in our case MOODLE_31_STABLE, we will use ```git merge moodle/MOODLE_31_STABLE``` for this, as usual let's resolve the conflicts. What is important, we need to rollback our gitignore and since we haven't commited its changes, we can use ```git checkout -- .gitignore``` for this. Now we can finally commit and push and the update is done.

## MODIFYING PLUGIN FROM MAIN REPOSITORY

Since moodle plugins doesn't work without Moodle and it is complicated to test them separately, we always have to work with them in our Moodle instance, so it would be nice to edit them inside this instance, our main project, too, otherwise we would have to make change in plugin repository, then copy it to main project and we would copy it there and back all the time, which is not really comfortable and also leaves a huge space for mistakes. But here it gets a bit tricky, how to make changes in our main repository, when we want to track changes, especially because of the merges, in our separate repositories? And what if we want to make some change in our main repository, that is not general for plugin, but is specific for our Moodle instance (like configuration)? And how to split these cases? Many questions, but fortunately it has a quite simple answer and that is powerful git cherrypick command, but let's start from the beggining.

First thing we need to know, that when using subtrees for plugins we can make changes directly in our main repository folder and simply commit and push them, we would merge these changes back to our repository anyway, so why not. The question is how to properly get them to our plugin repositories. First let's think a bit about what kinds of commits can appear in our main repository. In relation to our plugin, there are generally four cases - commits that modify only main module and not the plugin, commits modifying only plugin and they are meant to be backported to its repository, changes that modify plugin and main module and are meant to be backported and changes that modify plugin (and possibly also main module, but that is not relevant for this case) and aren't meant to be backported, which means, they are local and should not be reflected in plugin repository.

First and last case are thus not meant to be part of the plugin repository and should be ommited in them. Second case should be copied as a whole in plugin repository and third case, the most complicated one, should be copied, but not as a whole, we should take only changes that modifies our plugin. So how to solve it? We definitely should copy the files and commit them again from plugin repository, since that would make different commits and could result in some later problems with merging. So what to do?

We will use git cherry-pick command for this, this command lets us to pick commits from one branch and push them to other one. To make things clean let's create new temporary branch for this, we will call it same way as the plugin remote branch, but we'll add backport- prefix to it. This branch should be splitted from branch we want to commit changes to, so branch of the plugin we want to modify, let's say it's master branch of our mod-folder plugin, but we should be in our main repository, not plugin repository, so we will have to checkout branch from our remote, we can do this by following command:

```shell
git checkout -b backport-mod-folder mod-folder/master
```

Now we have to cherry-pick changes from branch we modified, so let's say it is our main repository master branch. Cherry-pick is quite complex command and it let's us select commits many ways as shows [git cherry-pick documentation](https://git-scm.com/docs/git-cherry-pick). We will show just the basic ones, to pick one specific commit we use ```git cherry-pick -x --strategy=subtree <commit-to-take>```, to pick multiple commits we use ```git cherry-pick -x --strategy=subtree <commit-to-take-1> <commit-to-take-2>``` and finally to take range of commits we use ```git cherry-pick -x --strategy=subtree <first-commit-to-take>..<last-commit-to-take>```. We can call cherry-pick multiple times and commits will aggregate. The question is how to specify commit, the first option is to use commits hash, the second one is to use some special commands, we will show just one of them, it takes from ```<branch-to-take-from>~<distance-from-last-commit>``` so  ```master~0``` will take the last commit from master, ```master~3``` will take fourth last commit from master and for example ```development~1``` will take second last commit from development branch. So we know how to use cherry-pick, so we can take commits we want, let's say that we have plugin changes in last six commits of main plugin master branch, but the last and third last change are just instance specific, so we don't want to commit them to plugin repository. We can use cherry-pick multiple times, it could look for example like this:

```shell
git cherry-pick -x --strategy=subtree master~1 # this takes second last change
git cherry-pick -x --strategy=subtree master~3..master~5 # this takes fourth to sixth last change
```

When we have cherry-picked all the commits we want to transfer to our temporary branch we will just call ```git push <remote-name> HEAD:<remote-branch-name>``` to apply them to our branch, so in our case:

```shell
git push mod-folder HEAD:master
```

Now we can checkout to our regular branch, let's say it's master and remove backport branch with:

```shell
git checkout master
git branch -D backport-mod-folder
```


