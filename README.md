
<img src="https://tommyguo.dev/ribbon_images/banner.png" width="500px"> <sup>v1.0</sup>
## What it is:
Ribbon is a self-hosted pastebin for links, text & code snippets and files.

## Some background:
This project was born out of my need to bookmark articles I would find on my phone but want to read on my PC, instead of saving it to the endless void that is my notes, I decided to use this. I personally use it to transfer files and other things across local devices, as well of course save interesting articles from any device whether it be a tablet, phone, or any other device.


## Some more extra & unnecessary background: 
This project is by no means "secure" or safe enough to run publicly, security was never a concern while building this as it was meant to be ran locally. Aside from that, I'm still learning on the way and this was a great project as a reintroduction to PHP & MySQL.

I'm currently running this on a Raspberry Pi 3b+ using Apache & MySQL.

## If you want to host this yourself:
Download and extract the contents into your web directory

You'll then need to run the following sql script to setup the database & table that Ribbon needs:

    CREATE DATABASE IF NOT EXISTS ribbon;
    USE ribbon;
    CREATE TABLE `ribbons` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `type` enum('link','text','code','file') NOT NULL,
     `alias` varchar(32) NOT NULL,
     `title` text NOT NULL,
     `content` longblob NOT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8

following that, you'll need to edit the included `mySQL_details.php` file with your credentials

    <?php
        $sql_host =     "";
        $sql_user =     "";
        $sql_password = "";
    ?>


## These are some planned updates: <sub><sup>in no particular order</sub></sup>
- Delete ribbons
- Dark mode
- General fixin' upings:
  - Auto generate alias url if left blank
  - Generate file/code/text name if left blank


## Screenshots:
Links tab
<kbd>![links tab](https://tommyguo.dev/ribbon_images/links_tab.png)</kbd>
Text tab
<kbd>![text tab](https://tommyguo.dev/ribbon_images/text_tab.png)</kbd>
Code tab
<kbd>![code tab](https://tommyguo.dev/ribbon_images/code_tab.png)</kbd>
Files tab
<kbd>![files tab](https://tommyguo.dev/ribbon_images/files_tab.png)</kbd>
Ribbons
<kbd>![ribbon tab](https://tommyguo.dev/ribbon_images/ribbon_tab.png)</kbd>
