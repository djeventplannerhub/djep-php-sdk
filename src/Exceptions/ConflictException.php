<?php
namespace DJEP\Exceptions;

/**
 * Thrown on HTTP 409, where the request conflicts with something that already
 * exists or is still in use.
 *
 * The API returns useful context alongside a 409 and this exception preserves
 * it. create_addon on a duplicate name returns the existing addonid, so you can
 * reuse the record rather than searching for it:
 *
 *     try {
 *         $addon = $djep->addons->create(['name' => 'Uplighting', 'price' => 300]);
 *     } catch (ConflictException $e) {
 *         $addon = $djep->addons->get($e->getExistingId());
 *     }
 */
class ConflictException extends DJEPException
{
    /** @var object|null The full error object returned by the API */
    protected $errorDetails;

    public function __construct(string $message, int $code = 409, $errorDetails = null)
    {
        parent::__construct($message, $code);
        $this->errorDetails = $errorDetails;
    }

    /**
     * The id of the existing record that caused the conflict, or null when the
     * API did not name one.
     */
    public function getExistingId(): ?int
    {
        foreach (['addonid', 'packageid', 'venueid', 'equipmentid', 'systemid', 'eventid', 'clientid'] as $key) {
            if (isset($this->errorDetails->$key)) {
                return (int) $this->errorDetails->$key;
            }
        }
        return null;
    }

    /** The full error object, for conflicts carrying other context. */
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }
}
