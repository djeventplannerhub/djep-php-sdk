<?php
namespace DJEP\Resources;

class Events extends Resource
{
    /**
     * List events (paginated)
     *
     * @param array $params  Optional: page, per_page, sort_by, sort_dir, status, event_type,
     *                       clientid, assigned_dj, date_from, date_to, search, search_by
     */
    public function list(array $params = []): object
    {
        return $this->http->get('events', $params);
    }

    /**
     * Auto-paginate through all events
     */
    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('events', $params);
    }

    /**
     * Get a single event by ID
     */
    public function get(int $eventid): object
    {
        return $this->http->get('event', ['eventid' => $eventid]);
    }

    /**
     * Create a new event
     *
     * @param array $data  Required: event_date, plus clientid OR (first_name + last_name)
     *                     Optional: event_type, event_name, status, pkg_idnumber, start_time,
     *                     end_time, guest_count, bookedby, assigned_dj, addons, etc.
     *                     Financials are auto-calculated from the assigned package.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_event', $data);
    }

    /**
     * Update an existing event (PATCH-style)
     *
     * @param int   $eventid  The event to update
     * @param array $fields   Associative array of field => value pairs to update
     * @param bool  $recalculate  Recalculate financials based on current package/times/venue
     */
    public function update(int $eventid, array $fields, bool $recalculate = false): object
    {
        $data = $fields;
        $data['eventid'] = $eventid;
        $data['fields'] = implode(',', array_keys($fields));
        if ($recalculate) {
            $data['recalculate'] = 'true';
        }
        return $this->http->post('update_event', $data);
    }

    /**
     * Delete an event and all associated records (cascading delete)
     */
    public function delete(int $eventid): object
    {
        return $this->http->post('delete_event', ['eventid' => $eventid]);
    }

    /**
     * Get payments for a specific event
     */
    public function payments(int $eventid): object
    {
        return $this->http->get('event_payments', ['eventid' => $eventid]);
    }

    /**
     * Get music requests for a specific event
     */
    public function musicRequests(int $eventid): object
    {
        return $this->http->get('event_music_requests', ['eventid' => $eventid]);
    }

    /**
     * Get planning form data for a specific event
     *
     * Returns the primary and additional planning forms with their question/answer data.
     * For to-do items, use tasks() instead.
     */
    public function planning(int $eventid): object
    {
        return $this->http->get('event_planning', ['eventid' => $eventid]);
    }

    /**
     * Get task / to-do items for a specific event
     *
     * @param array $params  Optional: completed, sort_by, sort_dir
     */
    public function tasks(int $eventid, array $params = []): object
    {
        $params['eventid'] = $eventid;
        return $this->http->get('event_tasks', $params);
    }

    /**
     * Quick status update on an event
     *
     * @param int    $eventid  The event to update
     * @param string $status   The new status value
     * @return object Response with old_status and new_status
     */
    public function updateStatus(int $eventid, string $status): object
    {
        return $this->http->post('update_status', ['eventid' => $eventid, 'status' => $status]);
    }

    /**
     * Assign or unassign an employee to an event position
     *
     * @param int    $eventid      The event
     * @param int    $position     Position number (1-15)
     * @param int    $employeeid   Employee ID to assign, or 0 to unassign
     * @param array  $options      Optional: fee, role
     */
    public function assignEmployee(int $eventid, int $position, int $employeeid, array $options = []): object
    {
        $data = array_merge($options, [
            'eventid' => $eventid,
            'position' => $position,
            'djemployeeid' => $employeeid,
        ]);
        return $this->http->post('assign_employee', $data);
    }

    /**
     * Update one or more note fields on an event
     *
     * @param int   $eventid  The event to update
     * @param array $notes    Associative array with any of: comments, service_description,
     *                        post_event_notes, next_action, next_action_date
     */
    public function updateNotes(int $eventid, array $notes): object
    {
        $notes['eventid'] = $eventid;
        return $this->http->post('update_notes', $notes);
    }

    /**
     * Set which systems are assigned to an event.
     *
     * @param int   $eventid
     * @param array $systemids  System IDs. Pass an empty array to clear.
     *
     * This REPLACES the whole list. Send every system you want on the event,
     * not just the additions. Maximum of 10.
     */
    public function assignSystems(int $eventid, array $systemids): object
    {
        return $this->http->post('assign_event_systems', [
            'eventid' => $eventid,
            'systems' => implode(',', $systemids),
        ]);
    }

    /**
     * Set which standalone equipment is assigned to an event.
     *
     * @param int   $eventid
     * @param array $equipment  Either a flat list of ids, or equipmentid => qty.
     *                          Pass an empty array to clear.
     *
     * This REPLACES the whole list, the same as assignSystems().
     */
    public function assignEquipment(int $eventid, array $equipment): object
    {
        // A list is [318, 412]; a quantity map is [318 => 2, 412 => 1]. PHP casts
        // numeric string keys to integers, so is_int() cannot tell the two apart.
        // Compare the keys against a plain 0..n sequence instead.
        $isList = array_keys($equipment) === range(0, count($equipment) - 1);
        $parts = [];
        foreach ($equipment as $key => $value) {
            $parts[] = $isList ? (string) $value : "{$key}={$value}";
        }
        return $this->http->post('assign_event_equipment', [
            'eventid'   => $eventid,
            'equipment' => implode(',', $parts),
        ]);
    }
}
