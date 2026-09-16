<?php
namespace DJEP\Resources;

class Employees extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('employees', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('employees', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('employee', ['djemployeeid' => $id]);
    }

    /**
     * Update an employee.
     *
     * Writable: active, disabled, email, cell_phone, telephone, work_telephone,
     * fax_phone, first_name, last_name, middle_name, stage_name,
     * employment_category through employment_category5, address, address2,
     * city, state, zipcode, country, emergency_contact,
     * employee_display_order, notes, start_date, birthday.
     *
     * Permission flags, passwords, usernames, wage settings and mail
     * credentials are deliberately NOT writable through the API at any
     * permission level. Naming one returns a 400 and nothing is written,
     * including the fields that would have been allowed.
     *
     * There is no create method. Employees are created in DJEP.
     */
    public function update(int $djemployeeid, array $fields): object
    {
        $data = $fields;
        $data['djemployeeid'] = $djemployeeid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_employee', $data);
    }
}
