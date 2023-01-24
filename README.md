# tc-app
De applicatie voor de TC van SKC

# Installation instructions
0. Ensure Node.js and Node Package Manager are installed
1. Run `npm install` to pull all required Node packages
2. PHP packages are included; no composer install needed. 
3. TC-app uses the configuration.php file from Joomla 3 to connect to a DB. Make sure the file is there. 
4. TC-app does not by itself create the 3 required tables in the database (tcapp_players, tcapp_player_types, tcapp_teams). Make sure your local DB has these tables! Look at the live SKC database for the table structure. 
