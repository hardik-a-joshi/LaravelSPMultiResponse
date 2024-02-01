# laravel-s-p-multi-response

LaravelSPMultiResponse is a powerful Laravel package designed to streamline the execution of SQL stored procedures with the capability to handle multiple response sets effortlessly. With this package, you can seamlessly integrate stored procedures into your Laravel application, simplifying database interactions and enhancing performance. Say goodbye to complex database queries and hello to efficient, organized, and scalable database operations with LaravelSPMultiResponse.

## Installation

Use the composer to install laravel-s-p-multi-response.

```bash
composer required laravel-s-p-multi-response
```

## Usage

After installation, you can use the package by importing the SPMultiResponse class and the Laravel App facade.

Create an instance of the SPMultiResponse class using App::make().

Call the desired stored procedure using the callStoredProcedure() method, providing the procedure name and an array of parameters.

Receive and work with the multiple response sets returned by the stored procedure.

Here's a sample code snippet:

```
use laravel_hrdk\LaravelSPMultiResponse\SPMultiResponse;
use Illuminate\Support\Facades\App;

// Create an instance of SPMultiResponse
$service = App::make(SPMultiResponse::class);

// Call the stored procedure and pass parameters
$multipleRowsets = $service->callStoredProcedure('store_procedure_name', ['param1', 'param2', '...']);

// Display the multiple response sets
dd($multipleRowsets);

```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](LICENSE.md)
