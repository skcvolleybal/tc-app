# tc-app
De applicatie voor de TC van SKC.

# Installation instructions
0. Make sure you're running a webserver, PHP and MySQL database on your local system. We usually use Laragon (Windows) or XAMPP/MAMP (Mac)
0. Clone this repo into your webservers website serving directory, called usually htdocs or www 
0. Ensure Node.js, Node Package Manager and Composer are installed on your local system
2. Run `npm install` to pull all required Node packages
3. Run `composer install` and `composer dump-autoload` to pull PHP packages and activate the class autoloader  
4. Duplicate the .env.example file and rename it to .env. Open the file and fill in the right variables for your environment. 
5. TC-app does not by itself create the three required tables in the database (tcapp_players, tcapp_player_types, tcapp_teams). Make sure the database you specified in your .env has these tables. Run the following commands to create the database tables (see file database_install.sql)
