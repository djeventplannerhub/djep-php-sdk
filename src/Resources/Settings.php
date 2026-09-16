<?php
namespace DJEP\Resources;

class Settings extends Resource
{
    /**
     * Get website tools settings (form config, field labels, validation, reCAPTCHA)
     *
     * @param string $tool  Tool name: "request_info", "check_availability", etc.
     */
    public function websiteTools(string $tool): object
    {
        return $this->http->get('settings', ['type' => 'websitetools', 'tool' => $tool]);
    }

    /**
     * Get company settings
     */
    public function company(): object
    {
        return $this->http->get('settings', ['type' => 'company']);
    }

    /**
     * Get custom field configuration
     */
    public function customFields(): object
    {
        return $this->http->get('settings', ['type' => 'custom_fields']);
    }

    /**
     * Get the account's picklists: event types, event statuses, booked
     * statuses, inquiry sources, client types, payment methods, employee roles
     * and music request types, plus the currency symbol, tax rate and date
     * format.
     *
     * Read this rather than hardcoding values. Statuses in particular are
     * validated on write and differ from account to account.
     */
    public function lists(): object
    {
        return $this->http->get('settings', ['type' => 'lists']);
    }
}
