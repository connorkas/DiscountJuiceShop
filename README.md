# Discount Juice Shop 🧃
Website I created for my Web Application Security course in college. Originally built to be hosted on an apache2 server with PHP and MySQL modules installed. Written in HTML, CSS, and PHP. Majority of pages interact with a created MySQL database. It's not meant to look pretty by any means, it was just created to gain a deeper understand on how some more advanced website functionality works and how certain types of attacks could be exploited. This website was built to demonstrate common vulnerablities and how to patch them. It contains multiple implementations with security built in mind and should be protected against Session Hijacking, SQL Injection, XSS, CSRF, Header Injection, and Local File Inclusion. This website should be fairly easy to get going, I reinstalled this web application into a Kali 2022.1 VM by only installing apache2, mysql-server, and libapache2-mod-php. To launch the required services run _sudo systemctl start apache2 && systemctl start mysql_.

# Developer Notes 
- This was simply a class project. The code is not going to be perfect or exactly clean. Please keep this in mind if you intend to actually use anything to build off of. If you find a vulnerability PLEASE LET ME KNOW! Not because I want to keep this code super up-to-date, but I want to learn more! I scanned this entire web app using the [SAST tool progpilot](https://github.com/designsecurity/progpilot) and this web app should be (mostly) good in terms of vuln sanitization, but I'm confident that something has probably slipped through the cracks.
- 95% of the /cart/ shopping code was NOT written by me. It was given by my instrutor to implement into this web application.
- The DocumentRoot is configured to be **/var/www/discojuice**, NOT /var/www/html
- Repository contains apache configuration files that I modified and a SQL database dump.
- This website absolutely does NOT follow any real PCI-DSS requirments. Hell, the credit card information is stored in PLAIN TEXT on a SQL table. Please keep this in mind. I really don't recommend you build too much off this.

# Created User Information
If you'd like to clone this repository to explore it, here's the following information you may need:

MySQL DB: 
(This can be changed in the **db.inc.php** file)
| username      | password      
| ------------- |:-------------
| terry         | P@ssw0rd123  

Web App Login:
(These are the uname/passwd combos if you dump the .sql I have in the repo)
| username      | password      
| ------------- |:-------------
| admin         | admin
| bob           | bob

## Web App Structure:
**/ & Products.php:** This is the 'public facing' shop page. Users can read the Juice Shop's memo & goal and shop for products.
- / (home)
  - Contains nothing but an introduction and link to visit the Products page.
- /Products.php
  - Pulls list of all available products for 'purchase' from the MySQL database.  

**/admin**: This is the /admin centre. Contains CRUD (Create, Read, Update, Delete) & login pages which interact with the MySQL database and manipulate what appears on the public-facing portion of the website.
- /admin/create.php
  - Creates juices which are then put into the SQL database and displayed on the website. Input is sanitized.
- /admin/read.php
  -  Allows an administrative user to read what's currently in the database and what's being displayed on the website. Users can either update a specific product or delete it from the database.
- /admin/update.php
  - Users can update specific information for a product including its Name, Price, Calories, and Percentage of Juice Contents.
- /admin/delete.php
  - Simply deletes the product from the database and website. 
- /admin/login.php
  - Admin login which unlocks CRUD functionality.

**Misc:** These files are supporting files that help functionality on the web pages listed above.
- db.inc.php
  - This file connects the database to whichever .php file that needs it.
- force_login.inc
  -  Checks if there is currently a user logged in for the current session. If not, they are redirected back to the login page.

<img src="https://juice-shop.herokuapp.com/assets/public/images/JuiceShop_Logo.png" height="200" width="170"></img>
