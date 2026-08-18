<?php
namespace DJEP\Resources;

class Submissions extends Resource
{
    public function rfi(array $params = []): object
    {
        return $this->http->get('rfi_submissions', $params);
    }

    public function quotes(array $params = []): object
    {
        return $this->http->get('quote_submissions', $params);
    }

    public function contactUs(array $params = []): object
    {
        return $this->http->get('contactus_submissions', $params);
    }

    /**
     * Create a new RFI submission (push a lead into the submissions queue)
     *
     * @param array $data  Required: first_name, last_name, email, event_date
     *                     Optional: telephone, event_type, guest_count, source, venueid,
     *                     packageid, djemployeeid, question_1-20, q1_mapto-q20_mapto,
     *                     privacy_policy_signed, sms_consent, send_notification, etc.
     */
    public function create(array $data): object
    {
        return $this->http->post('create_submission', $data);
    }

    /**
     * Convert an RFI submission into a client and event
     *
     * @param int   $reqIdNumber  The RFI submission to convert
     * @param array $options      Optional: clientid (use an existing client), status,
     *                            event_type, event_date, start_time, end_time, inquiry_source
     */
    public function convert(int $reqIdNumber, array $options = []): object
    {
        $options['req_idnumber'] = $reqIdNumber;
        return $this->http->post('convert_submission', $options);
    }
}
