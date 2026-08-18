<?php
namespace DJEP\Resources;

class Planner extends Resource
{
    /**
     * List planner items (paginated)
     *
     * Covers appointments, contacts, and to-do items, attached to an event or standalone.
     *
     * @param array $params  Optional: page, per_page, sort_by, sort_dir, date_from, date_to,
     *                       djemployeeid, type (A/C/T), eventid (0 = standalone only), completed
     */
    public function list(array $params = []): object
    {
        return $this->http->get('planner_items', $params);
    }

    /**
     * Auto-paginate through all planner items
     */
    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('planner_items', $params);
    }

    /**
     * Get a single planner item by ID
     */
    public function get(int $plannerId): object
    {
        return $this->http->get('planner_items', ['planner_id' => $plannerId]);
    }

    /**
     * Create a planner item
     *
     * @param array $data  Required: type (A = Appointment, C = Contact, T = Task/To-Do), title
     *                     Conditional: start_time (required for types A and C, ignored for T)
     *                     Optional: date (defaults to today; the due date when type is T), length,
     *                     details, eventid, clientid, venueid, contactid, employee_djemployeeid,
     *                     djemployeeid, additional_employees, meetinglocationid, client_visible,
     *                     calendar_background, calendar_text
     *
     * Note: a type C item must be linked to at least one of clientid, employee_djemployeeid,
     * venueid, or contactid, or the API returns a 400.
     *
     * Note: djemployeeid is the employee the item is ASSIGNED TO.
     *       employee_djemployeeid is the employee the item is ABOUT.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_planner_item', $data);
    }

    /**
     * Update a planner item (PATCH-style)
     *
     * @param int   $plannerId  The planner item to update
     * @param array $fields     Associative array of field => value pairs to update
     */
    public function update(int $plannerId, array $fields): object
    {
        $data = $fields;
        $data['planner_id'] = $plannerId;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_planner_item', $data);
    }

    /**
     * Mark a planner item complete or incomplete
     */
    public function complete(int $plannerId, bool $completed = true): object
    {
        return $this->update($plannerId, ['completed' => $completed ? 'true' : 'false']);
    }
}
