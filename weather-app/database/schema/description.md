# Weather-app-db database schema

## measurement_day
this table stores each unique measurement date
- `id` INT (primary key)
- `date` DATE - *date of the measurement*

## measurements
this table stores the weather measurement values
- `id` INT (primary key)
- `measurement_day_id` INT (foreign key) - *referencing 'measurement_day.id'*
- `parameter_id` INT (foreign key) - *referencing 'parameters.id'*
- `value` INT - *measured value of the given parameter*

## parameters
this table stores information about different weather parameters
- `id` INT (primary key)
- `name` VARCHAR(20) - *name of the weather parameter*
- `unit` VARCHAR(10) - *the unit of the parameter (e.g. °C, mm)*
