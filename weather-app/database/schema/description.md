# Weather-app-db database schema

## measurements
this table stores the daily weather measurements, with each row representing one day's measurements for different parameters
- `id` INT (primary key)
- `measurement_date` DATE - *date of the measurement*
- `parameter_id` INT (foreign key) - *referencing 'parameters.id'*
- `value` INT - *measured value of the given parameter*

## parameters
this table stores information about different weather parameters
- `id` INT (primary key)
- `name` VARCHAR(20) - *name of the weather parameter*
- `unit` VARCHAR(10) - *the unit of the parameter (e.g. °C, mm)*
