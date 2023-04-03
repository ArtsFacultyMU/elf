@plugin @plagiarism @plagiarism_urkund
Feature: Enable Ouriginal for modules
  In order to add plagiarism checking for supported modules
  As a teacher
  I need to be able to enable Ouriginal for individual items in those modules

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following config values are set as admin:
      | enableplagiarism | 1 |
    And the following config values are set as admin:
      | enabled | 1 | plagiarism_urkund |
      | api | https://secure.urkund.com | plagiarism_urkund |
      | username | 1 | plagiarism_urkund |
      | password | 1 | plagiarism_urkund |
      | enable_mod_assign | 1 | plagiarism_urkund |
      | enable_mod_forum | 1 | plagiarism_urkund |
      | enable_mod_workshop | 1 | plagiarism_urkund |
    And I log in as "admin"
    And I navigate to "Plugins > Plagiarism > Ouriginal plagiarism plugin" in site administration
    And I follow "Ouriginal defaults"
    And I set the field "Show similarity score to student" to "Always"
    And I set the field "Show similarity report to student" to "Always"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Create an assignment and enable Ouriginal for it
    When I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Test assignment for Ouriginal |
      | Require students to click the submit button | Yes |
      | Enable Ouriginal                        | Yes |
      | Show similarity score to student     | Always |
      | Show similarity report to student    | Always |
      | Receiver address                     | test@analysis.urkund.com |
    Then I should see "This is not a valid receiver address."

  @javascript
  Scenario: Create a forum and enable Ouriginal for it
    When I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum |
      | Description | Test forum for Ouriginal |
      | Enable Ouriginal                        | Yes |
      | Show similarity score to student     | Always |
      | Show similarity report to student    | Always |
      | Receiver address                     | test@analysis.urkund.com |
    Then I should see "This is not a valid receiver address."

  @javascript
  Scenario: Create a forum and enable Ouriginal for it
    When I add a "Workshop" to section "1" and I fill the form with:
      | Workshop name | Test Workshop |
      | Description | Test Workshop for Ouriginal |
      | Enable Ouriginal                        | Yes |
      | Show similarity score to student     | Always |
      | Show similarity report to student    | Always |
      | Receiver address                     | test@analysis.urkund.com |
    Then I should see "This is not a valid receiver address."
