sample-tools-php
================

PHP-based sample application demonstrating how to parse a Europass document and store the information in a DB.

Features
=========
This is a web-based PHP tool to demonstrate how the information contained in a Europass document (Europass PDF+XML CV) generated using the online editor, can be extracted and imported into a custom database schema (MySQL DB) or fill-in an HTML form.
The functionalities covered by this tool are:

- Upload a Europass document generated from the online tool in PDF+XML format.
- Extract the XML from the document.
- Parse the XML and extract the data.
- Use the extracted data to fill in a web form and present it to the end user, or, store the data in a custom schema and present a report to the end user.

Technologies used
=================
- PHP and the "mod_php" Apache module
- Web Services by Europass REST API (http://interop.europass.cedefop.europa.eu/web-services/rest-api-reference) for extracting the Europass XML attachment from the PDF.

System requirements
====================
- Apache web server (https://httpd.apache.org/)
- Php (http://php.net/). PHP + Apache have been installed via XAMPP (https://www.apachefriends.org/download.html - XAMPP for Windows, PHP 5.6.32, Apache 2.4.29)
- MySQL DB server (https://dev.mysql.com/downloads/mysql/)

Run application
===============
1. Start Apache server (before you should include all PHP files/ static pages/images/ etc of the app under under phpRootPath\htdocs\appFolder)
2. Open main php page. e.g for localhost: http://localhost/appFolder/.
This page includes the 2 main actions : Upload PDF+XML file to database or Upload PDF+XML file to html forms.  
