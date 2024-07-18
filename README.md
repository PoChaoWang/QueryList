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

## Ace Editor
The editor settings are located in CodeArea.jsx. If you wish to customize it on your website, please ensure you have configured the height and width.
You can refer to the table here
[text](https://ace.c9.io/#nav=howto)

## Google Sheets API
Please ensure you have set up the Google Sheets API in the Google API Console. You need to create a Service Account in the credentials. After downloading the credential file from the service account, place it in the storage folder or your preferred location, and ensure the path is correctly set in GoogleService.php.

## Authentication
The authentication is currently invalid. If you want to use it, you need to remove the comment code below.

### AuthenticatedLayout
```{/* <Dropdown>
    \\code
   </Dropdown> */}
```

```{/* <div className="px-4">
    \\code
    </div> */}
```

### web.php
Remove the code below.
```
Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
Route::get('/queries', [QueryController::class, 'index'])->name('query.index');
Route::get('/queries/create', [QueryController::class, 'create'])->name('query.create');
Route::get('/queries/{id}/edit', [QueryController::class, 'edit'])->name('query.edit');
Route::get('/queries/{id}', [QueryController::class, 'show'])->name('query.show');

Route::post('/queries', [QueryController::class, 'store'])->name('query.store');
Route::put('/queries/{id}', [QueryController::class, 'update'])->name('query.update');
Route::delete('/queries/{id}', [QueryController::class, 'destroy'])->name('query.destroy');
Route::post('/query/verify', [QueryController::class, 'verify'])->name('query.verify');

Route::post('/recordings/execute/{query}', [RecordingController::class, 'recordQueryExecution'])->name('recording-execution');

Route::post('/outputting/store/{query}', [OutputtingController::class, 'store'])->name('outputting.store');
Route::put('/outputting/update/{outputting}', [OutputtingController::class, 'update'])->name('outputting.update');
Route::delete('/outputting/destroy/{outputting}', [OutputtingController::class, 'destroy'])->name('outputting.destroy');
```

and remove the comment code in the file.

### Dashboard.jsx, Query/Index.jsx, Query/Create.jsx, Query/Edit.jsx, Query/Show.jsx
Remove the commented code in the file.
`user={auth.user}`

## Database
Please ensure that the .env and config/database.php files have been set up with the correct database configurations you require.

### Tables
You can refer to the table details in the database migrations. Ensure that the queries, recordings, schedules, and output tables are correctly configured in your database.

# Note
- If you want to output the data to the Google Sheet, you must to set up the Google ID and Sheet name after you create the query.
- The schedule function is currently invalid, with only frontend visuals and no backend functionality implemented.
