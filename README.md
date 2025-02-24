# Build Restful API with Laravel using TDD

This is based on MartinJoo

### Rest API

REST or RESTful stands for representational state transfer. It's just a way to build APIs that expose resources. It's not a strict protocol but a set of best practices. It heavily relies on HTTP methods and status codes.

| Method | URI                          | Description                 | Typical status codes                                          |
| ------ | ---------------------------- | --------------------------- | ------------------------------------------------------------- |
| GET    | /api/v1/employees            | Get all employees           | 200 - OK                                                      |
| GET    | /api/v1/employees/{employee} | Get one employee            | 200 - OK, 404 - NOT FOUND                                     |
| POST   | /api/v1/employees            | Create new employee         | 201 - CREATED, 422 - UNPROCESSABLE ENTITY                     |
| PUT    | /api/v1/employees            | Update an existing employee | 204 - NO CONTENT, 404 - NOT FOUND, 422 - UNPROCESSABLE ENTITY |
| DELETE | /api/v1/employees/{employee} | Delete an employee          | 204 - NO CONTENT, 404 - NOT FOUND                             |

Any endpoint can return the following status codes:

- 401 - Unauthorized: User not logged in
- 403 - Forbidden: User has no permission to perform the operation
- 409 - Conflict: Edit conflict between multiple simultaneous updates
- 429 - Too Many Request: Also known as API throthling
- 418 - I'm a teapot

### Database Design

- department:

    - it holds information about a department inside a company
    - attributes:
        - name
        - description
    - relationships:
        - employees: A department has many employees
        - paychecks: A department has many paychecks through employees

- employee:

    - it holds information about an employee and his / her salary
    - attributes:
        - first_name
        - last_name
        - department_id
        - job_title
        - payment_type
            - salary
            - hourly_rate
        - salary: only if payment type is salary
        - hourly_rate: only if payment type is hourly rate
    - relationships:
        - department: An employee belongs to a department
        - paychecks: An employee has many paychecks
        - time_logs: An employee has many time_logs

- paycheck:

    - It holds information about an employee paycheck
    - attributes:
        - employee_id
        - net_amount
        - payed_at
    - relationships:
        - employee: A paycheck belongs to an employee

- time_logs:
    - Time tracked by an employee
    - attributes:
        - employee_id
        - started_at
        - stopped_at
        - minutes
    - relationships:
        - employee: A time_log belongs to an employee
