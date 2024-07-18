
# About Query List
This is a side project designed to allow users to query tables from a MySQL database and transmit the query results to Google Sheets.

## Key Features:
- **Framework:** Built using Laravel 11.
- **Database Query:** Connects to MySQL databases, allowing users to perform queries directly.
- **Result Transmission:** Sends the results of these queries to Google Sheets via the Google Sheets API.
- **Embedded Code Editor:** Utilizes Ace, an embeddable code editor written in JavaScript, with MySQL specified as the language for the editor.
  https://ace.c9.io/#nav=about
  
This project showcases the integration of database querying with cloud-based spreadsheet management, providing a seamless workflow for handling and sharing data.

# Package
This project requires certain packages for full functionality. The packages are categorized into "Must To Do" and "Suggested To Do."

## Must To Do
To ensure the project works correctly, you need to install the following packages:

### npm
`npm install react-ace ace-builds sql-formatter`

### composer
`composer require google/apiclient`
Please make sure you have set up the API in Google.
https://developers.google.com/sheets/api/guides/concepts

## Suggested To Do
If you want to utilize all features and code within the project, consider installing the following packages:

### npm
`npm install @fortawesome/fontawesome-free react-resizeable-panels`

### composer
`composer require brooze`

`php artisan breeze:install`

I use React with Inertia, Dark mode, and PHPUnit to install Breeze.

# Preparing


