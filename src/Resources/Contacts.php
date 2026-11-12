<?php
namespace DJEP\Resources;

class Contacts extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('contacts', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('contacts', $params);
    }

    public function get(int $id): object
    {
        return $this->http->get('contact', ['contactid' => $id]);
    }

    /**
     * Create a contact.
     *
     * @param array $data  At least one of first_name, last_name or company is
     *                     required. Also accepts middle_name, job_title,
     *                     email_address, spouse, web_page, notes,
     *                     profile_image_url, business_phone, home_phone,
     *                     mobile_phone, pager, business_fax, home_fax, the
     *                     business_ and home_ address fields, and is_vendor.
     *
     * Administrators, master admins and salespeople only. An employee with
     * "Add Vendors To Events" can attach an existing vendor to an event with
     * $djep->vendors->linkToEvent(), but cannot create the record itself.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_contact', $data);
    }

    /**
     * Update a contact. Only the fields you pass are written.
     *
     * The name is re-checked after the edit, so clearing every one of
     * first_name, last_name and company in a single call is refused.
     */
    public function update(int $contactid, array $fields): object
    {
        $data = $fields;
        $data['contactid'] = $contactid;
        $data['fields'] = implode(',', array_keys($fields));
        return $this->http->post('update_contact', $data);
    }
}
