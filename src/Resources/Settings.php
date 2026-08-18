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
}
