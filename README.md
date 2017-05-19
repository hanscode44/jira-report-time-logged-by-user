# jira-report-time-logged-by-user
This is quick solution to JIRA not having an out the box way of reporting time logged per user, instead of just a project. This can be a bit of a road block for project managers. This was never intended to be 100% polished, as it was made quickly and I don't intend on updating this repo unless there is genuine interest, you're welcome to do what you want with it.

#### Requirements
1. JIRA Cloud
2. Some webspace to host this PHP script on

#### Installation
1. Clone this GIT repository.
2. Run `composer install` to install PHP dependencies.
3. Rename settings_demo.php to settings.php
4. Edit the variables inside settings.php to match your own JIRA domain / user login.

#### Usage
1.  Select a time period.
2.  Click run report, if all went well you see an overview of logged time. 



#### What it does
This tool uses the JIRA REST API, however it requires your username and password for basic authentication, and this user would require permissions to view the project and obtain the data requested - so please be mindful of this fact.

#### Future updates
If you need a feature, feel free to request it (I can try find time to help you), or alternatively submit a pull request.

#### Disclaimer
Sorry, I can't be held liable for any problems this script may cause to your system, use at your own peril!
