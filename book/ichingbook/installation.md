The site can be duplicated by the following instructions.  I think/hope this is complete.

### **Prepare**

---

You will need to have the following installed

* apache2
* mariabd
* php7
* php7.0-mbstring
* php7.0-mysql
* libapache2-mod-php
* npm
* wkhtmltopdf
* curl
* git
* composer
* xvfb \(if running on headless server\)
* phantomjs

---

### **The Base Code**

---

**Clone from GitHub**

`git clone`[`https://github.com/baardev/iching.git`](https://github.com/baardev/iching.git)

**cd to 'iching' dir and run**

`composer install`

**From here on I refer to this 'iching' directory as both the 'DOC\_ROOT' and the '~/ '  **

---

### Tweak Apache2

---

**enable mods **`expires`** and **`rewrite`

`a2enmod expires`

`a2enmod rewrite`

**Create a vhost config.  This is mine.**

> &lt;VirtualHost \*:80&gt;
>
> ServerName slider.com
>
> &lt;Directory "/home/jw/src/iching"&gt;
>
> ```
> Require all granted
>
> Options +Indexes
>
> AllowOverride all
> ```
>
> &lt;/Directory&gt;
>
> \# needed for reading the iching.ini PHP config file
>
> \# it can be either "dev" or "prod"
>
> SetEnv runtime dev
>
> ServerAdmin duncan.stroud@gmail.com
>
> DocumentRoot  "/home/jw/src/iching"
>
> ErrorLog      "/var/log/httpd/babelbrowser\_error\_log"
>
> CustomLog     "/var/log/httpd/babelbrowser\_access\_log" common
>
> ExpiresActive On
>
> ExpiresDefault "access plus 1 seconds"
>
> \#RewriteEngine on
>
> \#RewriteCond %{REQUEST\_METHOD} ^\(TRACE\|TRACK\)
>
> \#RewriteRule .\* - \[F\]&lt;/VirtualHost&gt;
>
> \# Make sure all cache is really off \*/
>
> &lt;filesMatch ".\(php\|html\|js\|css\|tpl\)$"&gt;
>
> ```
> FileETag None
>
> &lt;ifModule mod\_headers.c&gt;
>
>   Header unset ETag
>
>   Header set Cache-Control "max-age=0, no-cache, no-store, must-revalidate"
>
>   Header set Pragma "no-cache"
>
>   Header set Expires "Wed, 11 Jan 1984 05:00:00 GMT"
>
> &lt;/ifModule&gt;
> ```
>
> &lt;/filesMatch&gt;
>
> &lt;/VirtualHost&gt;

**you will need to set up a password file for this.**

`~/cignite/.htaccess`

> AuthType Basic
>
> AuthName "Editor Access"
>
> AuthUserFile /home/jw/sites/iching/.htpasswd
>
> Require valid-user
>
> RewriteEngine off

**Edit the**`/etc/hosts`** file to add a locel server name for testing**

`127.0.0.1 iching.com`

---

### **Create the database**

---

`mysql -e "create database iching"`

**Clear FULLTEXT stop word list BEFORE inporting the data**

_NOTE: I have only tried this on INNODB tables, and even then with flakey results after much hassle._

_NOTE: Only MySQL: 5.6 supports INNODB FULLTEXT searches.  If you are running &lt;5.6 you will  need to either upgrade or convert to MyISAM._

Because the MySQL/MariaDB fulltext search's use of stop words is incompatible with this site's search, you'll need to disable them by add the following line in the `/etc/mysql/my.cnf` \(or where ever yours is\) , inside the `[mysqld]` section:

_for INNODB _

`[mysqld]`

`innodb_ft_enable_stopword=1`

`innodb_ft_user_stopword_table=iching/site_stopwords`

`innodb_ft_server_stopword_table=iching/site_stopwords`

_for MyISAM_

`[mysqld]`

`ft_stopword_file=''`

**Import the data**

`mysql iching < database_noopt.sql`

**Run the following commands in MySql client \(edit as needed\)**

`create user 'ichingDBuser'@'localhost' identified by 'aJU6sk1w3e';`

`grant all on hexagrams.* to 'ichingDBuser'@'localhost';`

`grant all on iching.* to 'ichingDBuser'@'%';`

`FLUSH PRIVILEGES;`

---

### Code Igniter \(for CRUD\)

---

**install code igniter under the DOC\_ROOT \(~/\).  Download it here -&gt; **[https://www.codeigniter.com/userguide3/installation/downloads.html](https://www.codeigniter.com/userguide3/installation/downloads.html)

**I renamed the dir to '**`~/cignite`**'**

`cd ~/cignite`

`composer install`

**Now you need to install **`grocery_crud`** \(**[**https://www.grocerycrud.com/**](https://www.grocerycrud.com/%29**%29\).  download it to any tmp dir, unzip up, 'cd' into the dir it made \(in my case 'grocery-crud-1.5.9', and create a zip file\*\*

`zip -r gcrud.zip *`

**Move that zip to your Code Igniter dir  \(**`~/cignite`**\), and unzip it there.**

`cd ${WEBROOT}/cignite`

`unzip gcrud.zip`

**Then edit the **`application/config/database.php`** file**

---

### PHP.INI

---

**Create a file called **`iching.ini`** in the php modules directory \(or whereever you think is best\), usually under PHP config folder \(in my case that was '**`/etc/php/7.0/mods-available/`**'**

**If you have various versions of **`php.ini`** under different directories, such as **`/etc/php5/cli`**, **`/etc/php5/apache`**, etc, create symlinks to each respective dir... much easier to manage.**

`ln -fs /etc/php/7.0/mods-available/iching.ini /etc/php/7.0/apache/conf.d/20-iching.ini`

`ln -fs /etc/php/7.0/mods-available/iching.ini /etc/php/7.0/cgi/conf.d/20-iching.ini`


*IMPORTANT! The config file for cignite default to the 'prod' database of 'babelbrowser'. To change this you need to manually edit `./cignite/application/config/database.php`*

> \[iching\]
>
> iching.dev.root         = "/home/jw/sites/iching"
>
> iching.dev.testServer   = "iching.cloudlixt.com"
>
> iching.dev.user         = "jw"
>
> iching.dev.db.name      = "iching"
>
> iching.dev.db.server    = "localhost"
>
> iching.dev.db.user      = "ichingDBuser"
>
> iching.dev.db.pass      = "1q28sjnk75GHw3e"
>
> iching.prod.root        = "/home/jw/sites/babelbrowser"
>
> iching.prod.testServer  = "babelbrowser.com"
>
> iching.prod.user        = "jw"
>
> iching.prod.db.name     = "babelbrowser"
>
> iching.prod.db.server   = "localhost"
>
> iching.prod.db.user     = "ichingDBuser"
>
> iching.prod.db.pass     = "1q2sldjcnd\*&w3e"
>
> ;End

_**NOTE: The database import only imports the DEV database "iching", so only the \*.dev.\* vars are used, by default.**_

The server name is only used when testing from the command line.  You may not need it but it must be there.

The `iching.*.user` is the user that the HTML to PDF converter will run as. This should be your normal user id.

**But you must add the following line to **`/etc/sudoers`** file for it to have permission to run from the web user.**

`www-data ALL=(jw) NOPASSWD: /home/jw/src/iching/utils/makePdf.sh`

_where _`www-data`_ is the user the web server runs under._

`jw`_= the username to run the script under, followed by the entire path to the _`makePdf.sh`_script._

**To test that it is being read**

`php --ini|grep iching`

**you should get back a line that looks something like**

`/etc/php/7.0/cli/conf.d/20-iching.ini,`

**To test the site for the command line, run**

`php-cgi -f  ./index.php flipped=1 f_tossed=23 f_final=11`

**This way it is easy to see the errors.**

**If that works, you are ready to go to the website and test it out there.  You might need to fix all the permissions as well.  This script can be run from  ~/**

> \#!/bin/bash
>
> shopt -u dotglob
>
> sudo chown -R jw \*
>
> sudo chown -R jw .git
>
> sudo chown -R www-data questions
>
> sudo chmod -R a+rw questions
>
> sudo chown -R www-data id
>
> sudo chmod -R a+rw  id
>
> sudo chown -R www-data astro
>
> sudo chmod -R a+rw  astro
>
> sudo chmod -R 777 log

---

### PDF

---

**The code to build the PDFs **

`cd ~/lib/md2pdf`

`cp composer.json.UPDATED composer.json`

`composer install`

---

### GITBOOK

---

**'cd'  to **`~/book`

git clone [https://github.com/baardev/iching\_book.git](https://github.com/baardev/iching_book.git)

**the script **`./book/MAKE`**  (run from the server root) is how the book is compiled.**

---

### MISC

---

**PDF generation: To run on headless server you have to create a wrapper for wkhtmltopdf as follows**

> `echo -e '#!/bin/bash\nxvfb-run -a --server-args="-screen 0, 1024x768x24" /usr/bin/wkhtmltopdf -q $*' > /usr/bin/wkhtmltopdf.sh`
>
> `chmod a+x /usr/bin/wkhtmltopdf.sh`
>
> `ln -s /usr/bin/wkhtmltopdf.sh /usr/local/bin/wkhtmltopdf`

You will also need to add the following lines to your `/etc/sudoers`' files to give them persmission to run as the web server user.

`http ALL=(jw) NOPASSWD: /home/jw/src/iching/utils/makePdf.sh`

where `http` is the user the web server runs under.  It could be `www-data` as well, and `jw` is the username it will run as \(use your own\)

**Planetary calculations**

Also put this line in `/etc/sudoers` as well

`http ALL=(jw) NOPASSWD: /home/jw/src/iching/astro/getJson.sh`

The enables enables the astrological functions.

You will you will also have to install `phantomjs`\(&gt;2.1.1-8\) which is a  'Headless WebKit with JavaScript API', because all the astro calculations are done in javascript which, in this case, requites a display device.  phantomjs is easily installed with

`sudo apt-get install phantomjs` \(Ubuntu,Debian, etc\)

or

`sudo pacman -S phantomjs` \(Arch\)

But if you can't you can download it here -&gt; [http://phantomjs.org/download.html](http://phantomjs.org/download.html)

It is expected to be in the `/usr/bin` dir

**HotBits Server Testing**

Note: To get access to the  radioactive decay random number generator you need a 'HotBits' API key from the Fermi Lab.

Put something like this

`2,17,32,47 * * * * /home/jw/sites/iching/utils/testHotBits.sh >>/tmp/cron.log 2>&1`

into your crontab to ensure that the status of the HotBits server is up to date.  When the serve is down, the option is removed from the web form.

**Replacing database editor**

Because the default CKeditor in Grocery\_CRUB inserts HTML tags into the content automatically, it needs to be relaced with an editor that does not do that.  I use **MarkItUp**.

from a temp folder check out MarkItUp.

`git clone https://github.com/markitup/1.x.git`

This creates the subdir 1.x \(terrible name for a sub dir, but I'll keep it\)

`cd 1.x`

`bower install`

`mv 1.x/markitup ~/cignite/assets/grocery_crud/texteditor`

`mv 1.x/images ~/cignite/assets/grocery_crud/` \(not sure iof this is necessary\)

edit `~/application/config/grocery_crud.php` and change

`$config['grocery_crud_default_text_editor'] = 'ckeditor';`

to

`$config['grocery_crud_default_text_editor'] = 'markitup';`

The new editor is installed

**show.php**

Because show.php reads the formatted html pages and extracts the DOM objects, in orde to get the imags to work and not give a 404, you need to symlink the images files to the assets folder

`ln -fs ${DOC_ROOT}/images/hex/small ${DOC_ROOT}/assets`

**Testing**

There is a fairly crude node.js script in `~/test/crawler/testcrawler.sh` \(call `testcrawler.js` and `testcrawler200.js`\) that looks for a "200" response from the pages, searches for a word on the page, and checks the existance of a PDF file.  You need to edit the shell script and change the SITE variable.

This requires `node.js` and `npm`.

`pacman -S nodejs`

or

`apt-get install nodejs`

in`~/test/crawler`install all the packages listed in`package.json`

`npm install`

Best not to install globally as it confuses the package manager.

You can also easily use somethng like `wget` to crawl tthe site and test for bad links

**Sitemap.xml**

To create a `sitemal.xml` I used the following Perl script

`${DOCROOT}/test/crawler/sitemap/sitemapgen.pl --config=${DOCROOT}/test/crawler/sitemap/config.xml`

The code and a `MAKE` script are in `${DOCROOT}/test/crawler/sitemap/`

