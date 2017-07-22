# Jira - Reporting tool to show logged time per user
This is a quick solution to show the time logged in JIRA, because it's not very easy to do this in JIRA itself. 

#### Requirements
1. JIRA Cloud
2. Some webspace to host this PHP script on

#### Installation
1. Clone this GIT repository.
2. Run `composer install` to install PHP dependencies.
3. Run `bower install` to install Javascript dependencies.
3. Rename settings_demo.php to settings.php
4. Edit the variables inside settings.php to match your own JIRA domain / user login.

#### Usage
1.  Select a time period.
2.  Click run report, if all went well you see an overview of logged time. 

![time_report](https://user-images.githubusercontent.com/11563020/28211425-e2becea6-689c-11e7-99b1-fcdad77ca57a.png)

#### What it does
This tool uses the JIRA REST API, however it requires your username and password for basic authentication, and this user would require permissions to view the project and obtain the data requested - so please be mindful of this fact.

#### Future updates
If you need a feature, feel free to request it, or alternatively submit a pull request.

### Questions
If you have any questions, just ask!

#### Disclaimer
Sorry, I can't be held liable for any problems this script may cause to your system, use at your own peril!
