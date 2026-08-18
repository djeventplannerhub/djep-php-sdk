<?php
namespace DJEP;

use DJEP\Resources\Events;
use DJEP\Resources\Clients;
use DJEP\Resources\Venues;
use DJEP\Resources\Employees;
use DJEP\Resources\Packages;
use DJEP\Resources\Addons;
use DJEP\Resources\Contacts;
use DJEP\Resources\Vendors;
use DJEP\Resources\Payments;
use DJEP\Resources\Expenses;
use DJEP\Resources\MusicRequests;
use DJEP\Resources\BookingHelpers;
use DJEP\Resources\Availability;
use DJEP\Resources\Settings;
use DJEP\Resources\Submissions;
use DJEP\Resources\ClosedDates;
use DJEP\Resources\Equipment;
use DJEP\Resources\Systems;
use DJEP\Resources\EventPlanning;
use DJEP\Resources\Planner;
use DJEP\Resources\HourlyTracker;

class Client
{
    public const VERSION = '1.2.0';

    private HttpClient $http;

    public Events $events;
    public Clients $clients;
    public Venues $venues;
    public Employees $employees;
    public Packages $packages;
    public Addons $addons;
    public Contacts $contacts;
    public Vendors $vendors;
    public Payments $payments;
    public Expenses $expenses;
    public MusicRequests $musicRequests;
    public BookingHelpers $bookingHelpers;
    public Availability $availability;
    public Settings $settings;
    public Submissions $submissions;
    public ClosedDates $closedDates;
    public Equipment $equipment;
    public Systems $systems;
    public EventPlanning $planning;
    public Planner $planner;
    public HourlyTracker $hourlyTracker;

    /**
     * Create a new DJEP API client
     *
     * @param string $apiKey   Your full API key (e.g. "secretkey_25077" or "secretkey_25077_48113")
     * @param string $domain   Your DJEP domain (e.g. "djep21.com")
     * @param array  $options  Optional: timeout (int), ssl_verify (bool), base_url (string)
     */
    public function __construct(string $apiKey, string $domain, array $options = [])
    {
        $this->http = new HttpClient($apiKey, $domain, $options);

        $this->events = new Events($this->http);
        $this->clients = new Clients($this->http);
        $this->venues = new Venues($this->http);
        $this->employees = new Employees($this->http);
        $this->packages = new Packages($this->http);
        $this->addons = new Addons($this->http);
        $this->contacts = new Contacts($this->http);
        $this->vendors = new Vendors($this->http);
        $this->payments = new Payments($this->http);
        $this->expenses = new Expenses($this->http);
        $this->musicRequests = new MusicRequests($this->http);
        $this->bookingHelpers = new BookingHelpers($this->http);
        $this->availability = new Availability($this->http);
        $this->settings = new Settings($this->http);
        $this->submissions = new Submissions($this->http);
        $this->closedDates = new ClosedDates($this->http);
        $this->equipment = new Equipment($this->http);
        $this->systems = new Systems($this->http);
        $this->planning = new EventPlanning($this->http);
        $this->planner = new Planner($this->http);
        $this->hourlyTracker = new HourlyTracker($this->http);
    }

    /**
     * Check API status and get available endpoints
     */
    public function status(): object
    {
        return $this->http->get('status');
    }
}
