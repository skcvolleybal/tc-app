# tc-app
De applicatie voor de TC van SKC.

# Installation instructions
This installation assumes you have set-up your webserver and WordPress. If you haven't done this yet, go to: https://github.com/skcvolleybal/starthier 

0. Clone this repo to your webservers root directory + a tc-app directory, so probably C:\laragon\www\tc-app
0. Ensure Node.js and Node Package Manager are installed 
1. Run `npm install` to pull all required Node packages
2. Run `composer install` and `composer dump-autoload` to pull PHP packages and activate the class autoloader  
3. Duplicate the .env.example file and rename it to .env. Open the file and fill in the right variables for your environment. Pick any database you like. 
4. TC-app does not by itself create the 3 required tables in the database (tcapp_players, tcapp_player_types, tcapp_teams). Export these tables from production or test and import into your database. 
